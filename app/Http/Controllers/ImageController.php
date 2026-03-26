<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Jobs\ExtractImageMetadata;
use App\Models\Image;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ImageController extends Controller
{
    /**
     * Display the dashboard with upload form and recent uploads.
     */
    public function index(): View
    {
        $images = Auth::user()->images()
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('images'));
    }

    /**
     * Handle file upload with duplicate detection and metadata extraction.
     */
    public function store(StoreImageRequest $request): RedirectResponse
    {
        $file = $request->file('image');

        // Generate SHA-256 hash of file content for duplicate detection
        $fileHash = hash_file('sha256', $file->getRealPath());

        // Check for duplicates across ALL users
        $existingImage = Image::where('file_hash', $fileHash)->first();
        $isDuplicate = $existingImage !== null;

        // Store the file using Laravel's Storage facade
        $path = $file->store('uploads', 'public');

        // Extract EXIF metadata
        $metadata = $this->extractMetadata($file->getRealPath(), $file);

        // Create the image record
        $image = Auth::user()->images()->create([
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_hash' => $fileHash,
            'file_size' => $file->getSize(),
            'metadata' => $metadata,
            'is_duplicate' => $isDuplicate,
        ]);

        // Build redirect with appropriate message
        if ($isDuplicate) {
            $originalDate = $existingImage->created_at->format('M d, Y \a\t h:i A');
            return redirect()->route('images.show', $image)
                ->with('warning', "Warning: This file was previously uploaded on {$originalDate}.");
        }

        return redirect()->route('images.show', $image)
            ->with('success', 'Image uploaded and analyzed successfully!');
    }

    /**
     * Display a single image with its metadata.
     */
    public function show(Image $image): View
    {
        // Ensure users can only view their own images
        if ($image->user_id != Auth::id()) {
            abort(403);
        }

        return view('images.show', compact('image'));
    }

    /**
     * Display gallery view with filtering.
     */
    public function gallery(Request $request): View
    {
        $query = Auth::user()->images()->latest();

        // Filter by duplicate status
        if ($request->has('filter')) {
            match ($request->filter) {
                'duplicates' => $query->where('is_duplicate', true),
                'unique' => $query->where('is_duplicate', false),
                default => null,
            };
        }

        $images = $query->paginate(12);

        return view('images.gallery', compact('images'));
    }

    /**
     * Delete an image and its file from storage.
     */
    public function destroy(Image $image): RedirectResponse
    {
        // Ensure users can only delete their own images
        if ($image->user_id != Auth::id()) {
            abort(403);
        }

        // Delete the file from storage
        Storage::disk('public')->delete($image->file_path);

        // Delete the database record
        $image->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Image deleted successfully.');
    }

    /**
     * Extract EXIF and other metadata from an uploaded image.
     *
     * @param string $filePath Absolute path to the temporary uploaded file
     * @param \Illuminate\Http\UploadedFile $file The uploaded file instance
     * @return array
     */
    private function extractMetadata(string $filePath, $file): array
    {
        $metadata = [];

        // Get image dimensions using getimagesize
        $imageInfo = @getimagesize($filePath);
        if ($imageInfo) {
            $metadata['width'] = $imageInfo[0];
            $metadata['height'] = $imageInfo[1];
            $metadata['dimensions'] = $imageInfo[0] . ' × ' . $imageInfo[1] . ' px';
            $metadata['mime_type'] = $imageInfo['mime'] ?? $file->getMimeType();
        }

        // Extract EXIF data (only works for JPEG/TIFF)
        if (function_exists('exif_read_data') && in_array($file->getMimeType(), ['image/jpeg', 'image/tiff'])) {
            try {
                $exif = @exif_read_data($filePath, 'ANY_TAG', true);

                if ($exif) {
                    // Camera information
                    if (isset($exif['IFD0']['Make'])) {
                        $metadata['camera_make'] = $exif['IFD0']['Make'];
                    }
                    if (isset($exif['IFD0']['Model'])) {
                        $metadata['camera_model'] = $exif['IFD0']['Model'];
                    }
                    if (isset($exif['IFD0']['Software'])) {
                        $metadata['software'] = $exif['IFD0']['Software'];
                    }

                    // Exposure information
                    if (isset($exif['EXIF']['ExposureTime'])) {
                        $metadata['exposure_time'] = $exif['EXIF']['ExposureTime'];
                    }
                    if (isset($exif['EXIF']['FNumber'])) {
                        $fNumber = $exif['EXIF']['FNumber'];
                        if (is_string($fNumber) && str_contains($fNumber, '/')) {
                            [$num, $den] = explode('/', $fNumber);
                            $metadata['aperture'] = 'f/' . round($num / $den, 1);
                        } else {
                            $metadata['aperture'] = 'f/' . $fNumber;
                        }
                    }
                    if (isset($exif['EXIF']['ISOSpeedRatings'])) {
                        $metadata['iso'] = $exif['EXIF']['ISOSpeedRatings'];
                    }
                    if (isset($exif['EXIF']['FocalLength'])) {
                        $focal = $exif['EXIF']['FocalLength'];
                        if (is_string($focal) && str_contains($focal, '/')) {
                            [$num, $den] = explode('/', $focal);
                            $metadata['focal_length'] = round($num / $den, 1) . 'mm';
                        } else {
                            $metadata['focal_length'] = $focal . 'mm';
                        }
                    }

                    // Date taken
                    if (isset($exif['EXIF']['DateTimeOriginal'])) {
                        $metadata['date_taken'] = $exif['EXIF']['DateTimeOriginal'];
                    } elseif (isset($exif['IFD0']['DateTime'])) {
                        $metadata['date_taken'] = $exif['IFD0']['DateTime'];
                    }

                    // GPS coordinates
                    if (isset($exif['GPS']['GPSLatitude']) && isset($exif['GPS']['GPSLongitude'])) {
                        $metadata['has_gps'] = true;
                    }

                    // Flash
                    if (isset($exif['EXIF']['Flash'])) {
                        $metadata['flash'] = ($exif['EXIF']['Flash'] & 1) ? 'Fired' : 'Did not fire';
                    }

                    // Orientation
                    if (isset($exif['IFD0']['Orientation'])) {
                        $orientations = [
                            1 => 'Normal',
                            2 => 'Mirrored',
                            3 => 'Rotated 180°',
                            4 => 'Mirrored & Rotated 180°',
                            5 => 'Mirrored & Rotated 90° CCW',
                            6 => 'Rotated 90° CW',
                            7 => 'Mirrored & Rotated 90° CW',
                            8 => 'Rotated 90° CCW',
                        ];
                        $orientation = $exif['IFD0']['Orientation'];
                        $metadata['orientation'] = $orientations[$orientation] ?? "Unknown ($orientation)";
                    }
                }
            } catch (\Exception $e) {
                // EXIF extraction failed — continue without it
            }
        }

        return $metadata;
    }
}
