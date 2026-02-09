        </main>
        </div>
        <!-- Fin Content Wrapper -->

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Custom JS -->
        <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

        <!-- Sidebar Toggle Script -->
        <script>
            // Toggle sidebar on logo click
            document.getElementById('menuToggle')?.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                const navbar = document.getElementById('navbar');
                const contentWrapper = document.querySelector('.content-wrapper');

                sidebar.classList.toggle('expanded');
                navbar.classList.toggle('expanded');

                // Ajustar content-wrapper manualmente si :has() no es soportado
                if (sidebar.classList.contains('expanded')) {
                    contentWrapper.style.left = '340px';
                } else {
                    contentWrapper.style.left = '120px';
                }
            });

            // Submenu toggle
            document.querySelectorAll('.sidebar-right button').forEach(button => {
                button.addEventListener('click', function() {
                    const submenu = this.nextElementSibling;
                    if (submenu && submenu.classList.contains('submenu')) {
                        submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
                    }
                });
            });
        </script>
        </body>

        </html>