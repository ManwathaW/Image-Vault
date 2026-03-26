# Image Vault

Image Vault is a secure, robust personal image management application built with Laravel and Vite. It allows authenticated users to upload, organize, view, and securely delete their personal images within a modern and responsive user interface.

## Core Features

- **User Authentication:** Secure registration, login, logout, and profile management handles leveraging Laravel Breeze.
- **Image Upload Dashboard:** A smooth and intuitive interface for users to upload new images directly into their vault.
- **Interactive Gallery View:** A responsive grid layout designed to elegantly display all uploaded images.
- **Image Details & Secure Deletion:** View individual images in full size. The application includes a secure deletion workflow that ensures a removed image is deleted from both the database records and physical storage entirely.
- **Modern Asset Bundling:** Powered by Vite for lightning-fast hot module replacement during development and optimized builds for production.
- **Automated Testing:** Covered by a reliable suite of PHPUnit/Pest tests ensuring regression-free deployment.

## Technical Stack

- **Framework:** Laravel 11.x
- **Frontend:** Blade Templates, Tailwind CSS framework, Alpine.js (via Breeze integration)
- **Asset Bundler:** Vite
- **Database:** SQLite (default) / MySQL / PostgreSQL ready

## Prerequisites

Before setting up the project locally, ensure you have the following installed on your machine:
- PHP >= 8.2
- Composer
- Node.js & NPM
- Git

## Installation Guide

**1. Clone the repository:**
```bash
git clone <repository-url>
cd image-vault
```

**2. Install PHP dependencies:**
```bash
composer install
```

**3. Install Frontend NPM dependencies:**
```bash
npm install
```

**4. Environment Configuration:**
Duplicate the example environment file and generate a unique application key:
```bash
cp .env.example .env
php artisan key:generate
```

**5. Database Setup:**
Configure your `.env` file with your database preferences (SQLite is configured out-of-the-box for Laravel 11), then run the database migrations to build the tables:
```bash
php artisan migrate
```

**6. Storage Link:**
Create a symbolic link to make your locally stored public images accessible from the web server:
```bash
php artisan storage:link
```

## Running the Application Locally

Running the application effectively requires booting both the backend PHP server and the frontend Vite bundler. Open two terminal windows and execute:

**Terminal 1 (Laravel Server):**
```bash
php artisan serve
```
*The app backend will now be accessible at [http://127.0.0.1:8000](http://127.0.0.1:8000).*

**Terminal 2 (Vite Assets Server):**
```bash
npm run dev
```
*This continuously compiles your Tailwind styles and JavaScript.*

## Application Routes

All image-handling routes are protected by the `auth` middleware.
- **`GET /login` / `GET /register`:** Authentication portals.
- **`GET /dashboard`:** The primary dashboard where users can upload new images.
- **`POST /images`:** The upload endpoint for storing images.
- **`GET /gallery`:** The main portal displaying the grid of vaulted images.
- **`GET /images/{image}`:** View a singular image perfectly scaled.
- **`DELETE /images/{image}`:** Secure endpoint for stripping an image completely from disk and db. 

## Running Tests

To ensure the application's core functionality adheres, run the test suite:
```bash
php artisan test
```

## Security Vulnerabilities
If you discover a security vulnerability within Image Vault, please ensure proper protocols are taken to secure the physical storage endpoints.

## License

The Image Vault framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
