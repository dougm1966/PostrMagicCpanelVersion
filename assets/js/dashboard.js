// Dashboard-specific interactions for PostrMagic
// Consolidated from inline scripts in sidebar-user.php and sidebar-admin.php
// This file is loaded by includes/dashboard-header.php after main.js

(function () {
  document.addEventListener('DOMContentLoaded', function () {
    /* ------------------------------
       Icon Initialisation (Lucide)
    ------------------------------ */
    if (window.lucide && typeof lucide.createIcons === 'function') {
      lucide.createIcons();
    }

    /* ------------------------------
       Sidebar Accordion (nav groups)
    ------------------------------ */
    document.querySelectorAll('.nav-group > button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var dropdown = btn.nextElementSibling;
        if (!dropdown) return;

        dropdown.classList.toggle('hidden');

        var chevron = btn.querySelector('[data-lucide="chevron-down"], [data-lucide="chevron-up"]');
        if (chevron) {
          var isHidden = dropdown.classList.contains('hidden');
          chevron.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
        }
      });
    });

    /* ------------------------------
       Sidebar Toggle (mobile / small)
    ------------------------------ */
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
      sidebarToggle.addEventListener('click', function (e) {
        e.preventDefault();
        sidebar.classList.toggle('open');
      });
    }

    // Close sidebar when clicking outside on mobile widths (< 768px)
    document.addEventListener('click', function (e) {
      if (window.innerWidth < 768 && sidebar && sidebarToggle) {
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
          sidebar.classList.remove('open');
        }
      }
    });

    /* ------------------------------
       Profile Menu Toggle
    ------------------------------ */
    var profileButton = document.getElementById('profile-menu-button');
    var profileMenu = document.getElementById('profile-menu');
    var profileChevron = document.getElementById('profile-chevron');

    function closeProfileMenu() {
      if (!profileMenu) return;
      profileMenu.classList.add('hidden');
      if (profileChevron) profileChevron.style.transform = 'rotate(0deg)';
      if (profileButton) profileButton.setAttribute('aria-expanded', 'false');
    }

    function toggleProfileMenu() {
      if (!profileMenu) return;
      var isOpen = !profileMenu.classList.contains('hidden');

      // Close any other open profile menus (if multiple sidebars on page)
      document.querySelectorAll('.profile-menu').forEach(function (menu) {
        if (menu !== profileMenu) menu.classList.add('hidden');
      });

      profileMenu.classList.toggle('hidden');
      if (profileChevron) profileChevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
      if (profileButton) profileButton.setAttribute('aria-expanded', String(!isOpen));
    }

    if (profileButton && profileMenu) {
      profileButton.addEventListener('click', function (e) {
        e.preventDefault();
        toggleProfileMenu();
      });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function (e) {
      if (profileMenu && profileButton && !profileMenu.contains(e.target) && !profileButton.contains(e.target)) {
        closeProfileMenu();
      }
    });

    // Close menu on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeProfileMenu();
      }
    });
  });
})();
