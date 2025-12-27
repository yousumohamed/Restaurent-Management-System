<?php
/**
 * Restaurant Management System
 * Login Page - Modern Split Design
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

require_once 'config.php';
require_once 'functions.php';

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $sql = "SELECT id, username, password, full_name, profile_image FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['profile_image'] = $user['profile_image'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Som Restaurant</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #FF7A18;
            --text-dark: #1A202C;
            --text-gray: #718096;
            --input-bg: #EDF2F7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* Split Layout */
        .split-container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Left Side */
        .left-panel {
            flex: 1;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 60px;
            border-right: 1px solid #f0f0f0;
            position: relative;
        }
        
        .logo-top {
            position: absolute;
            top: 40px;
            left: 40px;
        }
        
        .logo-top img {
            height: 85px; /* Increased size */
        }

        .testimonial {
            max-width: 500px;
            margin-bottom: 40px;
        }

        .testimonial p {
            font-size: 1.5rem;
            color: var(--text-dark);
            line-height: 1.4;
            font-weight: 500;
        }

        /* Right Side */
        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #fff;
        }

        .login-form-container {
            width: 100%;
            max-width: 420px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--text-gray);
            font-size: 0.9rem;
            margin-bottom: 30px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .back-link:hover { color: var(--primary-orange); }

        .form-header {
            margin-bottom: 32px;
            text-align: left;
        }

        .form-header h1 {
            font-size: 2rem;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-gray);
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.95rem;
            text-align: left;
        }
        
        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #A0AEC0;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 44px; /* Space for icon */
            background: var(--input-bg);
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text-dark);
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            background: white;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 122, 24, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .forgot-password {
            color: var(--text-gray);
            text-decoration: none;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FF7A18 0%, #FF5A18 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 122, 24, 0.25);
        }

        .alert-error {
            background: #FFF5F5;
            color: #C53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid #FEB2B2;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .left-panel { display: none; }
        }
    </style>
</head>
<body>

    <div class="split-container">
        <!-- Left Panel: Brand/Testimonial -->
        <div class="left-panel">
            <div class="logo-top">
                <img src="assets/website images/freepik-cool-shiny-catering-logo-20251227115416r8zy.png" alt="Logo">
            </div>
            
            <div class="testimonial">
                <p>"The authentic taste of home, revolutionized by modern management."</p>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="right-panel">
            <div class="login-form-container">
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to home</a>
                
                <div class="form-header">
                    <h1>Welcome back</h1>
                    <p>Enter your credentials to sign in to your account</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i> <!-- Using envelope icon as generic user icon logic -->
                            <input type="text" id="username" name="username" class="form-control" 
                                   placeholder="Enter your username" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                             <!-- Just spacer -->
                        </div>
                         <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="••••••••••••" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-gray);">
                            <input type="checkbox" style="accent-color: var(--primary-orange);"> Remember me
                        </label>
                        <!-- Forgot password removed -->
                    </div>

                    <button type="submit" class="btn-submit">
                        Sign in <i class="fas fa-arrow-right"></i>
                    </button>
                    
                    <div style="text-align: center; margin-top: 25px; color: var(--text-gray); font-size: 0.9rem;">
                        <span style="opacity: 0.7;">New to the system?</span> <a href="#" style="color: var(--primary-orange); text-decoration: none; font-weight: 600;">Contact Admin</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
