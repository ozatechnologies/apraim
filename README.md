# ABC ID Management System

## Overview
This is a comprehensive PHP and MySQL-based ABC ID Management System with user and admin functionalities.

## Features
- Secure user registration and login
- Unique ABC ID generation
- Point tracking system
- Admin point management
- Transaction logging
- CSRF protection

## Requirements
- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)

## Installation
1. Clone the repository
2. Create a MySQL database named `abc_id_system`
3. Import `config/schema.sql` to create tables
4. Update database credentials in `config/database.php`
5. Configure web server to point to project root

## Default Admin Credentials
- Username: admin
- Password: admin123 (change immediately after first login)

## Security Features
- Bcrypt password hashing
- CSRF token protection
- Input sanitization
- Prepared statements
- Role-based access control

## License
MIT License

## Developed by
Codeium AI Assistant
