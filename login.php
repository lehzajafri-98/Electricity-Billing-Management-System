<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Dummy login (replace with DB validation)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin'] = 'Admin';
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EBMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        
        .login-container {
            display: flex;
            height: 100vh;
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
            z-index: 1;
        }
        
        .login-left-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 2rem;
        }
        
        .logo {
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }
        
        .welcome-text {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .login-right {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-form-container {
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-title {
            font-size: 2rem;
            color: #1e3a8a;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .login-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        
        .alert {
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 2rem;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .input-icon .form-control {
            padding-left: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-left {
                flex: 0 0 auto;
                height: 40vh;
            }
            
            .welcome-text {
                font-size: 1.8rem;
            }
            
            .logo {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-left-content">
                <div class="logo">⚡</div>
                <h1 class="welcome-text">Welcome to EBMS</h1>
                <p class="subtitle">Electricity Bill Management System</p>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-form-container">
                <div class="login-header">
                    <h2 class="login-title">Admin Login</h2>
                    <p class="login-subtitle">Enter your credentials to access the dashboard</p>
                </div>
                
                <?php if (isset($error)) echo "<div class='alert'>$error</div>"; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" class="form-control" value="admin" readonly required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </button>
                </form>
                
                <p class="footer-text">© 2025 EBMS. All rights reserved to .</p>
            </div>
        </div>
    </div>
</body>
</html>