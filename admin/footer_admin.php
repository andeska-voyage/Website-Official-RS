        </div> <!-- End Content Wrapper -->
    </div> <!-- End Main Content -->

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Script Toggle Sidebar Mobile -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        // Fungsi Toggle
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Event Listener Tombol Hamburger
        toggleBtn.addEventListener('click', toggleSidebar);
        
        // Event Listener Overlay (klik area gelap untuk tutup)
        overlay.addEventListener('click', toggleSidebar);

        // Opsional: Tutup sidebar otomatis saat link diklik di mobile
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>