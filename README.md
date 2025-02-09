# Laravel Project: Upload CSV and Generate PDF Report

## Project Overview
This Laravel application allows users to upload CSV files via an API endpoint, process the data to store it in a relational database, and generate a downloadable PDF report.  

The application is built using **PHP 8.1** and follows the **Repository Design Pattern** to ensure clean and maintainable code. The following Laravel packages are integrated for functionality:  
- **barryvdh/laravel-dompdf**: For generating PDF reports.  
- **maatwebsite/excel**: For parsing and processing CSV files.  

---

## Features

1. **Upload CSV File**:  
   - API endpoint to upload CSV files.  
   - Uploaded files are stored securely in Laravel's storage system.  

2. **Process CSV and Store Data**:  
   - Parses the uploaded CSV file to extract data.  
   - Stores data in appropriate relational database tables based on predefined schema and constraints.  

3. **Generate PDF Report**:  
   - Generates a PDF report in **landscape format**, displaying parsed data in a structured tabular layout.  

4. **Provide Downloadable PDF Link**:  
   - The generated PDF is stored in Laravel’s storage system and a publicly accessible download link is returned via JSON.  

---

## Requirements

- **PHP**: 8.1 or higher  
- **Composer**: Dependency management  
- **MySQL**: For database storage  
- **Node.js and npm**: For managing front-end assets  

---

## Setup Guide

### Step 1: Clone the Repository
```bash
git clone https://github.com/wiahwajith/CSV_upload_project.git
cd CSV_upload_project

```

## Step 2: Install Dependencies

Install PHP dependencies using Composer:

```bash
composer install
```


## Step 3: Configure Environment Variables

1. Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```

2. Update the `.env` file with your database and application settings:

    ```env
    APP_NAME=Laravel
    APP_URL=http://localhost

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```


## Step 4: Migrate the Database

Run the following command to create the database tables:

```bash
php artisan migrate
```
``

## Step 5: Start the Development Server

Run the Laravel development server:

```bash
php artisan serve
```

Visit the application in your browser:

```
http://localhost:8000
```

## Additional Notes

- If you encounter permission issues, ensure the `storage` and `bootstrap/cache` directories are writable:

    ```bash
    chmod -R 775 storage bootstrap/cache
    ```

- For queue workers, set up Supervisor or use `php artisan queue:work`.

- If you encounter missing dependencies, ensure you run `composer install` and `npm install` again.

## csv file upload API (local)

http://127.0.0.1:8000/api/upload-csv
(No API Authentication)

