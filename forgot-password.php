<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = trim($_POST['mobile'] ?? '');
    $pan_aadhar = trim($_POST['pan_aadhar'] ?? '');
    
    if (empty($mobile) || empty($pan_aadhar)) {
        $error = 'Please enter both mobile number and PAN/Aadhar number';
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM members WHERE mobile = ? AND pan_aadhar = ?");
            $stmt->execute([$mobile, $pan_aadhar]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate a unique token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token in database
                $stmt = $pdo->prepare("UPDATE members SET reset_token = ?, reset_expires = ? WHERE id = ?");
                $stmt->execute([$token, $expires, $user['id']]);
                
                // In a real application, you would send an email/SMS with the reset link
                // For demo purposes, we'll show the reset link on the page
                $reset_link = "http://".$_SERVER['HTTP_HOST']."/NSCT/reset-password.php?token=$token";
                $success = 'Password reset link has been generated. <a href="' . $reset_link . '">Click here to reset your password</a>';
                
                // Clear the form
                $_POST = [];
            } else {
                $error = 'No account found with the provided details';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again later.';
            error_log('Forgot Password Error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - NSCT</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .forgot-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 150px;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
            font-weight: 600;
        }
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-container">
            <div class="logo">
                <img src="assets/images/logo.png" alt="NSCT Logo">
                <h3 class="mt-3">Forgot Password</h3>
                <p class="text-muted">Enter your registered mobile number and PAN/Aadhar to reset your password</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="forgotForm" novalidate>
                <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile Number</label>
                    <div class="input-group">
                        <span class="input-group-text">+91</span>
                        <input type="tel" class="form-control" id="mobile" name="mobile" 
                               value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>"
                               placeholder="Enter registered mobile number" 
                               pattern="[6-9]\d{9}" 
                               maxlength="10" 
                               required>
                    </div>
                    <div class="invalid-feedback">Please enter a valid 10-digit mobile number</div>
                </div>
                
                <div class="mb-4">
                    <label for="pan_aadhar" class="form-label">PAN or Aadhar Number</label>
                    <input type="text" class="form-control" id="pan_aadhar" name="pan_aadhar"
                           value="<?php echo htmlspecialchars($_POST['pan_aadhar'] ?? ''); ?>"
                           placeholder="Enter your PAN or Aadhar number"
                           required>
                    <div class="invalid-feedback">Please enter your PAN or Aadhar number</div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>Reset Password
                    </button>
                </div>
            </form>
            
            <div class="back-to-login">
                Remember your password? <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function () {
            'use strict'
            
            // Fetch the form we want to apply custom Bootstrap validation styles to
            var form = document.getElementById('forgotForm')
            
            // Mobile number input validation
            const mobileInput = document.getElementById('mobile');
            mobileInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
                
                if (this.value.length === 10 && /^[6-9]\d{9}$/.test(this.value)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (this.value.length > 0) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
            
            // Form submission
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                
                form.classList.add('was-validated')
            }, false)
        })()
    </script>
</body>
</html>
