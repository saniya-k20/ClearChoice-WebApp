<?php
if (!empty($_SESSION['login_errors'])) {
    echo '<div class="error-message">';
    foreach ($_SESSION['login_errors'] as $error) {
        echo '<p><i class="fas fa-exclamation-circle"></i> '
             . htmlspecialchars($error, ENT_QUOTES) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['login_errors']);
}
?>