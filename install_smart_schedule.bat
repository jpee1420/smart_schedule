@echo off
setlocal enabledelayedexpansion

echo Smart Schedule Installation Script
echo =================================
echo.

REM Check if script is running with admin privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo This script requires administrator privileges.
    echo Please right-click and select "Run as administrator".
    echo.
    pause
    exit /b 1
)

REM Define common XAMPP installation paths to check
set "xampp_paths=C:\xampp;C:\XAMPP;D:\xampp;D:\XAMPP;E:\xampp;E:\XAMPP"

set "xampp_found=false"
set "xampp_path="

echo Checking for XAMPP installation...

REM Check each potential XAMPP path
for %%p in (%xampp_paths%) do (
    if exist "%%p\htdocs" (
        set "xampp_found=true"
        set "xampp_path=%%p"
        echo XAMPP found at: %%p
        goto :found_xampp
    )
)

:found_xampp
if "%xampp_found%" == "false" (
    echo.
    echo XAMPP installation not found in common locations.
    echo.
    
    REM Ask user for custom XAMPP location
    set /p custom_path=Please enter your XAMPP installation path (e.g., C:\xampp): 
    
    if exist "!custom_path!\htdocs" (
        set "xampp_found=true"
        set "xampp_path=!custom_path!"
        echo XAMPP found at: !custom_path!
    ) else (
        echo.
        echo Error: Could not find htdocs folder in the specified path.
        echo Please make sure XAMPP is installed correctly and try again.
        echo.
        pause
        exit /b 1
    )
)

echo.
echo Preparing to install Smart Schedule project to: %xampp_path%\htdocs\smart_schedule

REM Check if smart_schedule folder already exists in htdocs
if exist "%xampp_path%\htdocs\smart_schedule" (
    echo.
    echo Warning: A folder named 'smart_schedule' already exists in htdocs.
    set /p overwrite=Do you want to overwrite it? (Y/N): 
    
    if /i "!overwrite!" neq "Y" (
        echo.
        echo Installation canceled by user.
        echo.
        pause
        exit /b 0
    )
    
    echo Removing existing smart_schedule folder...
    rmdir /s /q "%xampp_path%\htdocs\smart_schedule"
    if !errorlevel! neq 0 (
        echo Failed to remove existing folder. Please close any applications that may be using it.
        pause
        exit /b 1
    )
)

REM Get script directory to find the project files
set "script_dir=%~dp0"
echo.
echo Copying Smart Schedule project files...

REM Create smart_schedule directory in htdocs
mkdir "%xampp_path%\htdocs\smart_schedule" 2>nul

REM Copy all project files to the XAMPP htdocs directory
xcopy "%script_dir%*" "%xampp_path%\htdocs\smart_schedule\" /E /I /H /Y
if %errorlevel% neq 0 (
    echo.
    echo Error: Failed to copy project files. Please check permissions and try again.
    pause
    exit /b 1
)

echo.
echo Smart Schedule project has been successfully installed to: %xampp_path%\htdocs\smart_schedule
echo.
echo Next steps:
echo 1. Start XAMPP Control Panel and ensure Apache and MySQL services are running
echo 2. Import the database by visiting: http://localhost/smart_schedule/install/
echo 3. Once installation is complete, access the application at: http://localhost/smart_schedule/
echo.
pause 