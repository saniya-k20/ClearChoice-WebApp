<?php
require_once 'config.php';
clean_output();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check for signup success
$signupSuccess = false;
if (isset($_SESSION['signup_success'])) {
    $signupSuccess = true;
    $signupEmail = $_SESSION['signup_email'] ?? '';
    unset($_SESSION['signup_success']);
    unset($_SESSION['signup_email']);
}

// Buffer all output
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Account System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <style>
        /* Animation for messages */
        .success-message, .error-message {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Validation error styling */
        .validation-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        /* Loading spinner */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Success popup */
        .login-success-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            transition: opacity 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .login-success-popup i {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="background-design">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    
    <div class="form-container">
        <?php if ($signupSuccess): ?>
            <div class="success-message">
                <p><i class="fas fa-check-circle"></i> Signup successful! Please login with <?= htmlspecialchars($signupEmail) ?></p>
            </div>
        <?php endif; ?>
        
        <div class="form-toggle">
            <button id="login-toggle" class="toggle-button active">Login</button>
            <button id="signup-toggle" class="toggle-button">Sign Up</button>
        </div>
        
        <form id="login-form" class="form-content active">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="form-button">Login</button>
        </form>
        
        <form id="signup-form" class="form-content">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="fullName" placeholder="Full Name" 
                       minlength="2" required pattern=".{2,}"
                       title="Please enter at least 2 characters">
                <div id="nameError" class="validation-error"></div>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
                <div id="emailError" class="validation-error"></div>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="form-button">Sign Up</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form toggling
        const loginToggle = document.getElementById('login-toggle');
        const signupToggle = document.getElementById('signup-toggle');
        const loginForm = document.getElementById('login-form');
        const signupForm = document.getElementById('signup-form');

        loginToggle.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.add('active');
            signupToggle.classList.remove('active');
            loginForm.classList.add('active');
            signupForm.classList.remove('active');
        });

        signupToggle.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.add('active');
            loginToggle.classList.remove('active');
            signupForm.classList.add('active');
            loginForm.classList.remove('active');
        });

        // Auto-show login form if coming from successful signup
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('form') === 'login') {
            loginToggle.click();
        }

        // Form submission handlers
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, 'login.php');
        });

        document.getElementById('signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, 'signup.php');
        });

        // Real-time validation
        document.querySelector('#signup-form input[name="fullName"]').addEventListener('input', function() {
            document.getElementById('nameError').style.display = 'none';
        });
        
        document.querySelector('#signup-form input[name="email"]').addEventListener('input', function() {
            document.getElementById('emailError').style.display = 'none';
        });
    });

    async function submitForm(form, actionUrl) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            // Clear existing errors
            document.querySelectorAll('.error-message').forEach(el => el.remove());

            // Special validation for signup form
            if (form.id === 'signup-form') {
                const password = form.querySelector('[name="password"]').value;
                const confirmPassword = form.querySelector('[name="confirmPassword"]').value;
                
                if (password !== confirmPassword) {
                    throw {
                        errors: {
                            confirmPassword: "Passwords don't match"
                        }
                    };
                }
            }

            // Prepare form data
            const formData = new FormData(form);
            const formObj = Object.fromEntries(formData.entries());

            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formObj)
            });

            const data = await response.json();
            
            if (!response.ok) throw data;

            if (data.success) {
                // Show success popup if specified
                if (data.show_popup) {
                    const popup = document.createElement('div');
                    popup.className = 'login-success-popup';
                    popup.innerHTML = `
                        <i class="fas fa-check-circle"></i>
                        <div class="popup-content">
                            <p>${data.popup_message}</p>
                        </div>
                    `;
                    document.body.appendChild(popup);
                    
                    setTimeout(() => {
                        popup.style.opacity = '0';
                        setTimeout(() => popup.remove(), 300);
                    }, 3000);
                }
                
                // Redirect if specified
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }
        } catch (error) {
            console.error('Error:', error);
            
            if (error.errors) {
                // Display field-specific errors
                Object.entries(error.errors).forEach(([field, message]) => {
                    const inputGroup = form.querySelector(`[name="${field}"]`).closest('.input-group');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.innerHTML = `<p><i class="fas fa-exclamation-circle"></i> ${message}</p>`;
                    inputGroup.appendChild(errorDiv);
                });
            } else {
                // Display general error
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerHTML = `<p><i class="fas fa-exclamation-circle"></i> ${error.message || 'An error occurred'}</p>`;
                form.prepend(errorDiv);
            }
            
            // Scroll to first error
            const firstError = form.querySelector('.error-message');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }
    </script>
</body>
</html>
<?php
ob_end_flush();
exit;
?>