# Smart Schedule & Digital Signage
# User Manual

## Table of Contents
1. [Introduction](#introduction)
2. [Smart Schedule System](#smart-schedule-system)
   - [Features](#smart-schedule-features)
   - [Login](#smart-schedule-login)
   - [Dashboard](#smart-schedule-dashboard)
   - [Managing Rooms](#managing-rooms)
   - [Managing Professors](#managing-professors)
   - [Managing Courses](#managing-courses)
   - [Managing Schedules](#managing-schedules)
   - [Filtering and Searching](#smart-schedule-filtering)
   - [Viewing Current Schedules](#viewing-current-schedules)
3. [Digital Signage System](#digital-signage-system)
   - [Features](#digital-signage-features)
   - [Admin Panel](#digital-signage-admin)
   - [Content Management](#content-management)
   - [Display Configuration](#display-configuration)
   - [Viewing Signage](#viewing-signage)
4. [Troubleshooting](#troubleshooting)
5. [Support](#support)

<a name="introduction"></a>
## 1. Introduction

This manual covers two integrated systems for educational institutions:

**Smart Schedule** - A comprehensive scheduling system that helps manage class schedules, rooms, professors, and courses with real-time status tracking.

**Digital Signage** - A digital display solution that shows current class schedules, announcements, and other information on monitors throughout the campus.

<a name="smart-schedule-system"></a>
## 2. Smart Schedule System

<a name="smart-schedule-features"></a>
### Features

- **User Authentication**: Secure login system for administrators
- **Room Management**: Add, edit, and delete rooms
- **Professor Management**: Add, edit, delete professors and track their status (Present, Absent, On Leave, On Meeting)
- **Course Management**: Add, edit, and delete courses with course codes
- **Schedule Management**: Create and manage class schedules with conflict detection
- **Real-time Status Display**: View current and upcoming classes with professor status
- **Filtering & Search**: Filter schedules by day, professor, room, and search functionality
- **Responsive Design**: Works on various screen sizes for administrative access

<a name="smart-schedule-login"></a>
### Login

1. Open your web browser and navigate to: `http://localhost/smart_schedule/`
2. Enter your username and password
3. Click "Login"

![Login Screen](images/login-screen.jpg)

> **Note**: If you haven't set up an account, please contact your system administrator.

<a name="smart-schedule-dashboard"></a>
### Dashboard

The dashboard is divided into tabs for easy navigation:
- **Rooms**: Manage classroom spaces
- **Professors**: Manage teaching staff and their status
- **Courses**: Manage course information
- **Schedules**: Manage class schedules

Each section can be viewed in either list or grid view using the view toggle buttons.

<a name="managing-rooms"></a>
### Managing Rooms

**To add a room:**
1. Click the "Rooms" tab
2. Click the "Add Room" button
3. Enter the room name
4. Click "Save"

**To edit a room:**
1. Find the room in the list/grid
2. Click the "Edit" button (pencil icon)
3. Update the room name
4. Click "Save"

**To delete a room:**
1. Find the room in the list/grid
2. Click the "Delete" button (trash icon)
3. Confirm deletion

**To search for rooms:**
1. Use the search box at the top of the Rooms tab
2. Type part of the room name
3. Results will filter automatically as you type

<a name="managing-professors"></a>
### Managing Professors

**To add a professor:**
1. Click the "Professors" tab
2. Click the "Add Professor" button
3. Enter the professor's name
4. Optionally upload a profile image
5. Click "Save"

**To edit a professor:**
1. Find the professor in the list/grid
2. Click the "Edit" button (pencil icon)
3. Update the information
4. Click "Save"

**To delete a professor:**
1. Find the professor in the list/grid
2. Click the "Delete" button (trash icon)
3. Confirm deletion

**To update professor status:**
1. Find the professor in the list/grid
2. Use the status dropdown to select:
   - Present (Green)
   - Absent (Red)
   - On Leave (Yellow)
   - On Meeting (Blue)
3. The status updates automatically when changed

<a name="managing-courses"></a>
### Managing Courses

**To add a course:**
1. Click the "Courses" tab
2. Click the "Add Course" button
3. Enter the course code (e.g., "CS101")
4. Enter the course name (e.g., "Introduction to Programming")
5. Click "Save"

**To edit a course:**
1. Find the course in the list/grid
2. Click the "Edit" button (pencil icon)
3. Update the information
4. Click "Save"

**To delete a course:**
1. Find the course in the list/grid
2. Click the "Delete" button (trash icon)
3. Confirm deletion

**To search for courses:**
1. Use the search box at the top of the Courses tab
2. Type part of the course code or name
3. Results will filter automatically as you type

<a name="managing-schedules"></a>
### Managing Schedules

**To add a schedule:**
1. Click the "Schedules" tab
2. Click the "Add Schedule" button
3. Select a course from the dropdown
4. Select a professor from the dropdown
5. Select a room from the dropdown
6. Choose the day (MWF, TTH, or Sat)
7. Set start and end times
8. Click "Save"

> **Note**: The system automatically checks for scheduling conflicts and will not allow overlapping schedules for the same room or professor.

**To edit a schedule:**
1. Find the schedule in the list
2. Click the "Edit" button (pencil icon)
3. Update the information
4. Click "Save"

**To delete a schedule:**
1. Find the schedule in the list
2. Click the "Delete" button (trash icon)
3. Confirm deletion

<a name="smart-schedule-filtering"></a>
### Filtering and Searching

**To filter schedules:**
1. In the Schedules tab, use the filter panel on the left
2. Check boxes to filter by:
   - Day (MWF, TTH, Sat)
   - Professor
   - Room
3. Schedules will automatically update to show only matching entries
4. Click "Clear Filters" to reset

**To search schedules:**
1. Use the search box at the top of the Schedules tab
2. Type any keyword (course name, professor name, room, etc.)
3. Results will filter automatically as you type

<a name="viewing-current-schedules"></a>
### Viewing Current Schedules

The current schedules view shows classes that are currently in session or starting soon.

1. Navigate to `http://localhost/smart_schedule/view_schedules.php`
2. The page automatically displays:
   - Classes currently in session
   - Classes starting within the next 5 minutes
   - Upcoming classes for the current day
3. Professor status is displayed with a colored badge:
   - Green: Present
   - Red: Absent
   - Yellow: On Leave
   - Blue: On Meeting

> **Note**: This page refreshes automatically every 2 minutes to show the most current information.

<a name="digital-signage-system"></a>
## 3. Digital Signage System

<a name="digital-signage-features"></a>
### Features

- **Content Management**: Create and manage announcements, notices, and other content
- **Schedule Integration**: Display current and upcoming class schedules
- **Multi-Display Support**: Configure different content for different displays
- **Rotation Settings**: Control how content rotates on screens
- **Media Support**: Display images, videos, and text-based content
- **Real-time Updates**: Changes appear on displays automatically

<a name="digital-signage-admin"></a>
### Admin Panel

1. Open your web browser and navigate to: `http://localhost/digital_signage/admin/`
2. Enter your username and password
3. Click "Login"

The admin dashboard gives you access to:
- Content Management
- Display Settings
- User Management
- System Settings

<a name="content-management"></a>
### Content Management

**To add content:**
1. In the admin panel, go to "Content"
2. Click "Add New Content"
3. Select the content type:
   - Announcement
   - Schedule Display
   - Image/Media
   - Custom HTML
4. Fill in the required information
5. Set display duration
6. Assign to displays
7. Click "Save"

**To edit content:**
1. Find the content in the list
2. Click "Edit"
3. Update the information
4. Click "Save"

**To delete content:**
1. Find the content in the list
2. Click "Delete"
3. Confirm deletion

**To schedule content:**
1. When creating or editing content
2. Set the "Valid From" and "Valid Until" dates
3. Content will only display during this period

<a name="display-configuration"></a>
### Display Configuration

**To add a display:**
1. In the admin panel, go to "Displays"
2. Click "Add New Display"
3. Enter a display name (e.g., "Lobby Monitor")
4. Enter the display location (e.g., "Main Building Entrance")
5. Set rotation interval (how often content changes)
6. Click "Save"

**To assign content to displays:**
1. Go to "Display Content"
2. Select a display from the dropdown
3. Check the content items you want to show
4. Set the display order
5. Click "Save"

<a name="viewing-signage"></a>
### Viewing Signage

**To view a display:**
1. Open your web browser and navigate to: `http://localhost/digital_signage/display.php?id=X`
   (where X is the display ID)
2. The browser will enter fullscreen mode
3. Content will rotate automatically based on the display settings

**For public displays:**
1. Set up a computer or monitor in the desired location
2. Open the browser to the display URL
3. Enable kiosk mode or use a digital signage player app

> **Tip**: For permanent installations, set the browser to automatically open to the display URL on startup and enter fullscreen mode.

<a name="troubleshooting"></a>
## 4. Troubleshooting

### Common Issues

**Smart Schedule:**
- **Schedule conflicts**: If you can't add a schedule, check for time conflicts with the same room or professor
- **Professor status not updating**: Refresh the page or check your internet connection
- **Search not working**: Ensure you're searching in the correct tab

**Digital Signage:**
- **Content not appearing**: Check if the content is assigned to the display and is within its valid date range
- **Display not rotating**: Verify the rotation interval settings
- **Media not loading**: Ensure the media files are properly uploaded and in a supported format

<a name="support"></a>
## 5. Support

For additional support:
- Check the documentation in the `/docs` folder of each project
- Contact your system administrator
- Visit the project repository for updates and issue reporting

---

© 2023 Smart Schedule & Digital Signage Systems 