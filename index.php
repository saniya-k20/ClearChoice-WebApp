<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user']['fullName'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Modern Web App</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="menu-icon" id="top-menu-icon">
        <i class="fas fa-bars"></i>
    </div>

    <div class="top-container">
        <div class="top-content">
            <div class="top-text">
                <h2>Hi, <?php echo htmlspecialchars($userName); ?>!</h2>
                <p>Your journey to better choices starts here.</p>
            </div>
            <div class="top-image">
                <img src="Leo1.png" alt="Design Image">
            </div>
        </div>
    </div>

<!-- Side Menu -->
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

    <div class="scan-heading">
        <h2>Scan Products</h2>
    </div>

    <a href="categories.html" class="scan-container">
        <div class="scan-content">
            <div class="scan-image">
                <img src="Leo2.png" alt="Scan Image">
            </div>
            <div class="scan-text">
                <p>Scan products for ingredients, nutrition & more!</p>
            </div>
        </div>
    </a>

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