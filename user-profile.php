<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user']['fullName'] ?? 'Guest';
$usernameHandle = '@' . strtolower(str_replace(' ', '_', $userName)) . '_123';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>User Profile</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="menu-icon" id="top-menu-icon">
        <i class="fas fa-bars"></i>
    </div>

    <div class="side-menu">
        <div class="menu-content">
            <a href="index.php" class="menu-item">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="user-profile.php" class="menu-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="user-guide.php" class="menu-item">
                <i class="fas fa-book"></i>
                <span>User Guide</span>
            </a>
            <a href="about-us.php" class="menu-item">
                <i class="fas fa-info-circle"></i>
                <span>About Us</span>
            </a>
            <a href="contact-us.php" class="menu-item">
                <i class="fas fa-phone"></i>
                <span>Contact Us</span>
            </a>
            <div class="menu-divider"></div>
            <a href="logout.php" class="menu-item logout-button">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <div class="overlay"></div>

    <main class="profile-container">
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($userName); ?></h2>
            <p><?php echo htmlspecialchars($usernameHandle); ?></p>
        </div>

        <div class="profile-options">
            <div class="profile-option" id="edit-profile-option">
                <i class="fas fa-user-edit"></i>
                <span>Edit Name</span>
            </div>

            <div class="edit-profile-form" id="edit-profile-form">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="edit-name" placeholder="Enter your name" value="<?php echo htmlspecialchars($userName); ?>">
                </div>
                <button class="save-button" id="save-button">Save</button>
                <div class="custom-popup" id="edit-name-popup"></div>
            </div>

            <div class="profile-option" id="change-password-option">
                <i class="fas fa-lock"></i>
                <span>Change Password</span>
            </div>

            <div class="change-password-form" id="change-password-form">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="current-password" placeholder="Current Password">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="new-password" placeholder="New Password">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm-password" placeholder="Confirm New Password">
                </div>
                <button class="save-button" id="save-password-button">Save</button>
                <div class="custom-popup" id="change-password-popup"></div>
            </div>

            <div class="profile-option">
                <i class="fas fa-heart"></i>
                <span>Favorites</span>
            </div>

            <div class="profile-option" id="dark-mode-toggle">
                <i class="fas fa-moon"></i>
                <span>Dark Mode</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="dark-mode-checkbox">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </main>

    <footer class="bottom-nav">
        <a href="favourites.html" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>Favorites</span>
        </a>
        <a href="index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="user-profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </footer>

    <script src="script.js"></script>
</body>
</html>