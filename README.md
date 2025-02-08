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
git clone https://github.com/your-repo-url.git
cd your-repo-name


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
