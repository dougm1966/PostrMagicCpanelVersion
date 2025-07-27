        </main>
    </div>
    
    <!-- Alpine.js for dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
        
        // Theme Toggle Functionality
        const html = document.documentElement;
        const themeToggle = document.getElementById('theme-toggle');
        const themeMenuToggle = document.getElementById('theme-menu-toggle');
        
        // Check for saved user preference, if any, on load
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
        
        // Toggle theme function
        function toggleTheme() {
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
            updateThemeIcon();
        }
        
        // Update icon based on current theme
        function updateThemeIcon() {
            const isDark = html.classList.contains('dark');
            if (themeToggle) {
                const moonIcon = themeToggle.querySelector('.fa-moon');
                const sunIcon = themeToggle.querySelector('.fa-sun');
                
                if (isDark) {
                    if (moonIcon) moonIcon.classList.add('hidden');
                    if (sunIcon) sunIcon.classList.remove('hidden');
                    if (themeMenuToggle) {
                        themeMenuToggle.innerHTML = '<i class="fas fa-sun mr-2 w-4 text-center"></i> Light Mode';
                    }
                } else {
                    if (moonIcon) moonIcon.classList.remove('hidden');
                    if (sunIcon) sunIcon.classList.add('hidden');
                    if (themeMenuToggle) {
                        themeMenuToggle.innerHTML = '<i class="fas fa-moon mr-2 w-4 text-center"></i> Dark Mode';
                    }
                }
            }
        }
        
        // Event Listeners
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleTheme);
        }
        
        if (themeMenuToggle) {
            themeMenuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                toggleTheme();
            });
        }
        
        // Initialize icons on load
        updateThemeIcon();
        
        // Feedback button handler (placeholder)
        document.getElementById('feedback-btn')?.addEventListener('click', (e) => {
            e.preventDefault();
            // Implement feedback modal or redirect
            console.log('Feedback button clicked');
        });
    </script>
</body>
</html>
