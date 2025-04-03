<?php
require_once 'db_check.php';

// If user is already logged in, redirect to index.php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Default admin credentials (you should change these)
    $admin_username = 'admin';
    $admin_password = 'admin123'; // In production, use hashed passwords
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Smart Schedule System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6f1f1;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== UNIVERSITY HEADER ===== */
        .university-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #870100;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        
        .university-name {
            font-size: 1.5rem !important;
            font-weight: bold;
            margin: 0;
            font-family: 'Algerian';
        }
        
        .university-name .university-text {
            font-weight: bold;
            display: inline-block;
            margin-right: 10px;
            background: linear-gradient(to top, #ff8800, #ffff00);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-fill-color: transparent;
        }
        
        .university-name .college-text {
            background: linear-gradient(to top, #888888, #ffffff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-fill-color: transparent;
        }
                
        .logo {
            height: 4rem;
            width: auto;
            margin-left: 15px;
            margin-right: 15px;
            object-fit: cover;
            display: block;
            transition: all 0.3s ease;
        }

        /* login container */
            
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #870100;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #6c757d;
            margin-bottom: 0;
        }
        .form-control {
            border-radius: 5px;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        .btn-login {
            background-color: #870100;
            border-color: #870100;
            width: 100%;
            padding: 0.75rem;
            font-weight: 500;
            margin-top: 1rem;
        }
        .btn-login:hover {
            background-color: #6b0000;
            border-color: #6b0000;
        }
        .alert {
            margin-bottom: 1rem;
        }

        @media screen and (max-width: 1280px), screen and (max-height: 720px) {
            /* University header */
            .university-header {
                padding: 5px 10px;
            }
            
            .university-name {
                font-size: 1.2rem;
                margin: 0 10px;
            }
            
            .university-text {
                margin-right: 6px;
            }
            
            .logo {
                max-height: 45px;
            }

            .login-container {
                width: 75%;
                max-width: auto;
                max-height: 455px;
            }
        }

        @media screen and (max-height: 720px) {
            body.browser-fullscreen .university-header {
                padding: 10px 5px;
            }
            
            body.browser-fullscreen .university-name {
                font-size: 1.3rem;
            }
            
            body.browser-fullscreen .logo {
                max-height: 50px;
            }
        }
    </style>
</head>
<body>
    <header class="university-header">
        <img src="images/ul-logo.png" alt="University Logo" class="university-logo logo">
            <h1 class="university-name">
                <span class="university-text">University of Luzon</span> 
                <span class="college-text">College of Computer Studies</span>
            </h1>
            <img src="images/cs-logo.png" alt="Department Logo" class="department-logo logo">
    </header>
    <div class="login-container">
        <div class="login-header">
            <h1>Smart Schedule System</h1>
            <p>Admin Login</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-login">Login</button>
        </form>
    </div>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html> 