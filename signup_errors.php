<?php
if (!empty($_SESSION['signup_errors'])) {
    echo '<div class="error-message">';
    foreach ($_SESSION['signup_errors'] as $error) {
        echo '<p><i class="fas fa-exclamation-circle"></i> '
             . htmlspecialchars($error, ENT_QUOTES) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['signup_errors']);
}
?>