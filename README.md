# Class Management and Scheduling System (CMSS)

A web-based system designed for educational institutions to manage class schedules, professors, rooms, and courses efficiently. The system provides real-time schedule displays, professor status tracking, and administrative controls.

## Features

### Schedule Management
- **Real-time Schedule Display**: View current and upcoming class schedules
- **Fullscreen Display Mode**: Optimized for display on wall-mounted screens
- **Status Indicators**: Visual indicators for current, upcoming, and ended classes
- **Auto-refreshing**: Schedules update automatically without manual refresh

### Professor Management
- **Professor Status Tracking**: Track professor attendance status (Present, Absent, On Leave)
- **Profile Management**: Store and manage professor profiles including photos
- **Schedule Assignment**: Assign professors to specific courses and rooms

### Room & Course Management
- **Room Allocation**: Manage classroom availability and assignments
- **Course Catalog**: Maintain a database of courses offered
- **Schedule Conflict Prevention**: System prevents double-booking of rooms or professors

### User Interface
- **Responsive Design**: Works on desktop and mobile devices
- **Admin Dashboard**: Centralized control panel for administrative functions
- **Navigation Controls**: Easy navigation with back-to-top and back-to-bottom buttons
- **Modern UI**: Clean, intuitive interface with Bootstrap styling

## Technical Details

### Requirements
- PHP 7.4+
- MySQL/MariaDB
- Web server (Apache/Nginx)
- Bootstrap 5
- Font Awesome icons

### Database Structure
The system uses the following main tables:
- `schedules` - Stores class schedules with time, day, and relations
- `professors` - Stores professor information and status
- `rooms` - Manages classroom information
- `courses` - Contains course details and information

### Installation

1. Clone the repository to your web server directory
2. Create a MySQL database and import the provided SQL file
3. Configure database connection in `config.php`
4. Ensure proper permissions for file uploads
5. Access the system via web browser

```php
// Example config.php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'username');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'cmss_db');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

## Usage

### Admin Dashboard
Access the admin dashboard to manage all aspects of the system:
- Add/Edit/Delete Professors
- Add/Edit/Delete Rooms
- Add/Edit/Delete Courses
- Create and manage schedules
- View system logs

### Schedule Viewer
The schedule viewer (`view_schedules.php`) provides a real-time display of current and upcoming classes:
- Automatically displays in fullscreen mode
- Updates in real-time
- Shows professor status
- Highlights current, upcoming, and ended classes

### Professor Status Updates
Update professor status directly from the admin panel:
- Mark professors as Present, Absent, or On Leave
- Status changes immediately reflect on schedule displays

## Key Files

- `index.php` - Main dashboard page
- `view_schedules.php` - Public schedule display page
- `manage_professors.php` - Professor management interface
- `manage_schedules.php` - Schedule management interface
- `manage_rooms.php` - Room management interface
- `manage_courses.php` - Course management interface
- `queries.php` - SQL queries used throughout the system
- `config.php` - Database and system configuration
- `styles.css` - Custom CSS styles
- `assets/js/script.js` - JavaScript functionality

## Customization

The system can be customized in several ways:
- Edit CSS in `styles.css` to change the appearance
- Modify database queries in PHP files to change data handling
- Update JavaScript in `script.js` for client-side behavior

## Security Features

- Session-based authentication
- Prepared SQL statements to prevent SQL injection
- Input sanitization
- CSRF protection for forms

## Contributing

Contributions to improve the system are welcome. Please follow these steps:
1. Fork the repository
2. Create a feature branch
3. Submit a pull request with detailed description of changes

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact the system administrator.