# Installation System

This directory contains the installation system for the Schedule Management System.

## How It Works

1. When accessing the main application, it checks if the database exists
2. If the database doesn't exist, you'll be redirected to this installation wizard
3. Follow the steps in the installation wizard to set up your database

## Installation Steps

1. **Database Configuration**
   - Enter your database connection details
   - The wizard will test the connection

2. **Database Setup**
   - You can use the default schema (smart_schedule.sql)
   - Or upload a custom SQL file if you have your own schema

3. **Completion**
   - Once installation is complete, you'll be redirected to the main application

## Manual Installation

If you prefer to set up the database manually:

1. Create a database in MySQL
2. Import the `smart_schedule.sql` file from the root directory
3. Update the database connection settings in `config.php`

## Troubleshooting

If you encounter any issues during installation:

- Make sure your database credentials are correct
- Ensure the web server has permission to write to the `config.php` file
- Check that the SQL file is valid and compatible with your MySQL version

For additional help, refer to the main application documentation. 