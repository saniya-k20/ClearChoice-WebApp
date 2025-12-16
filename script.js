// Helper function to show popup messages
function showPopup(element, message, isSuccess = true) {
  if (!element) return;
  element.textContent = message;
  element.style.display = 'block';
  element.style.color = isSuccess ? '#4CAF50' : '#f44336';
  setTimeout(() => element.style.display = 'none', 3000);
}

// Update user name across the application
function updateUserName(newName) {
    const profileHeader = document.querySelector('.profile-header h2');
    if (profileHeader) profileHeader.textContent = newName;
    
    const usernameHandle = document.querySelector('.profile-header p');
    if (usernameHandle) {
        usernameHandle.textContent = '@' + newName.toLowerCase().replace(/\s+/g, '_') + '_123';
    }

    fetch('update-name.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ newName: newName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && window.location.pathname.includes('index.php')) {
            const topContainerName = document.querySelector('.top-text h2');
            if (topContainerName) {
                topContainerName.textContent = `Hi, ${newName}!`;
            }
            showPopup(document.getElementById('edit-name-popup'), 'Name updated successfully!');
        }
    })
    .catch(error => {
        showPopup(document.getElementById('edit-name-popup'), error.message, false);
    });
}

// Dark Mode Toggle
function initializeDarkMode() {
  const darkModeToggle = document.getElementById('dark-mode-toggle');
  const darkModeCheckbox = document.getElementById('dark-mode-checkbox');
  const darkModeEnabled = localStorage.getItem('darkMode') === 'enabled';
  
  if (darkModeEnabled) {
      document.body.classList.add('dark-mode');
      darkModeCheckbox.checked = true;
  }

  darkModeToggle.addEventListener('click', function(e) {
      if (e.target.tagName === 'INPUT') return;
      const enableDarkMode = !document.body.classList.contains('dark-mode');
      darkModeCheckbox.checked = enableDarkMode;
      updateDarkMode(enableDarkMode);
  });

  darkModeCheckbox.addEventListener('change', function() {
      updateDarkMode(this.checked);
  });

  function updateDarkMode(enable) {
      if (enable) {
          document.body.classList.add('dark-mode');
          localStorage.setItem('darkMode', 'enabled');
      } else {
          document.body.classList.remove('dark-mode');
          localStorage.setItem('darkMode', 'disabled');
      }
  }
}

// Side Menu Toggle
function setupSideMenu() {
  const menuIcon = document.getElementById('top-menu-icon');
  const sideMenu = document.querySelector('.side-menu');
  const overlay = document.querySelector('.overlay');

  const toggleMenu = () => {
    sideMenu.classList.toggle('open');
    overlay.classList.toggle('active');
    document.body.style.overflow = sideMenu.classList.contains('open') ? 'hidden' : 'auto';
  };

  menuIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleMenu();
  });

  overlay.addEventListener('click', toggleMenu);
}

// Edit Profile Functionality
function setupEditProfile() {
  const editProfileOption = document.getElementById('edit-profile-option');
  const editProfileForm = document.getElementById('edit-profile-form');
  const editNameInput = document.getElementById('edit-name');
  const saveButton = document.getElementById('save-button');

  if (editProfileOption && editProfileForm) {
    editProfileOption.addEventListener('click', () => {
      editProfileForm.style.display = editProfileForm.style.display === 'block' ? 'none' : 'block';
    });
  }

  if (saveButton) {
    saveButton.addEventListener('click', () => {
      const newName = editNameInput.value.trim();
      if (newName) {
        updateUserName(newName);
      } else {
        showPopup(document.getElementById('edit-name-popup'), 'Please enter a valid name.', false);
      }
    });
  }
}

// Change Password Functionality
function setupChangePassword() {
  const changePasswordOption = document.getElementById('change-password-option');
  const changePasswordForm = document.getElementById('change-password-form');
  const savePasswordButton = document.getElementById('save-password-button');

  if (changePasswordOption && changePasswordForm) {
    changePasswordOption.addEventListener('click', () => {
      changePasswordForm.style.display = changePasswordForm.style.display === 'block' ? 'none' : 'block';
    });
  }

  if (savePasswordButton) {
    savePasswordButton.addEventListener('click', () => {
      const currentPassword = document.getElementById('current-password').value.trim();
      const newPassword = document.getElementById('new-password').value.trim();
      const confirmPassword = document.getElementById('confirm-password').value.trim();

      if (!currentPassword || !newPassword || !confirmPassword) {
        showPopup(document.getElementById('change-password-popup'), 'Please fill in all fields.', false);
        return;
      }

      if (newPassword !== confirmPassword) {
        showPopup(document.getElementById('change-password-popup'), 'Passwords do not match.', false);
        return;
      }

      showPopup(document.getElementById('change-password-popup'), 'Password changed successfully!');
      document.getElementById('current-password').value = '';
      document.getElementById('new-password').value = '';
      document.getElementById('confirm-password').value = '';
    });
  }
}

// Bottom Navigation
function setupBottomNav() {
  const navItems = document.querySelectorAll('.bottom-nav .nav-item');

  navItems.forEach((item) => {
    item.addEventListener('click', () => {
      navItems.forEach((navItem) => navItem.classList.remove('active'));
      item.classList.add('active');
    });
  });
}

// Initialize all functionality
function initializeApp() {
  setupSideMenu();
  setupEditProfile();
  setupChangePassword();
  setupBottomNav();
}

// Run the app when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeDarkMode();
  initializeApp();
});

// Account Page Script
document.addEventListener('DOMContentLoaded', function() {
  const loginToggle = document.getElementById('login-toggle');
  const signupToggle = document.getElementById('signup-toggle');
  const loginForm = document.getElementById('login-form');
  const signupForm = document.getElementById('signup-form');

  if (loginToggle && signupToggle && loginForm && signupForm) {
    [loginToggle, signupToggle].forEach(button => {
      button.addEventListener('click', function() {
        const isLogin = this.id === 'login-toggle';
        loginToggle.classList.toggle('active', isLogin);
        signupToggle.classList.toggle('active', !isLogin);
        loginForm.classList.toggle('active', isLogin);
        signupForm.classList.toggle('active', !isLogin);
      });
    });

    [loginForm, signupForm].forEach(form => {
      form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const response = await fetch(this.action, { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
          window.location.href = result.redirect;
        } else {
          alert(result.message);
        }
      });
    });
  }
});