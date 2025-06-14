<?php
namespace app\src\components;

class AgentSidebar {
    private $currentPage;

    public function __construct() {
        $this->currentPage = basename($_SERVER['PHP_SELF']);
    }

    public function render() {
        ?>
        <!-- Sidebar Toggle Button (Dropdown Style) -->
        <button id="sidebarToggle" class="fixed top-4 left-4 z-50 px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            Menu
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Sidebar -->
        <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-40">
            <!-- Close Button -->
            <button id="closeSidebar" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-8">Agent Dashboard</h2>
                
                <!-- Dropdown Menu -->
                <div class="relative">
                    <button id="menuDropdown" class="w-full flex items-center justify-between px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                        <span>Menu</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Content -->
                    <div id="menuContent" class="hidden absolute left-0 mt-2 w-full bg-white rounded-md shadow-lg z-50">
                        <a href="dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $this->currentPage === 'dashboard.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                            Dashboard
                        </a>
                        <a href="properties.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $this->currentPage === 'properties.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                            Properties
                        </a>
                        <a href="bookings.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $this->currentPage === 'bookings.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                            Bookings
                        </a>
                        <a href="tenants.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $this->currentPage === 'tenants.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                            Tenants
                        </a>
                        <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $this->currentPage === 'profile.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                            Profile
                        </a>
                        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

        <!-- JavaScript for Sidebar Functionality -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebarToggle');
                const closeSidebar = document.getElementById('closeSidebar');
                const menuDropdown = document.getElementById('menuDropdown');
                const menuContent = document.getElementById('menuContent');
                const overlay = document.getElementById('sidebarOverlay');

                // Toggle sidebar
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                });

                // Close sidebar
                function closeSidebarMenu() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                }

                closeSidebar.addEventListener('click', closeSidebarMenu);
                overlay.addEventListener('click', closeSidebarMenu);

                // Toggle dropdown menu
                menuDropdown.addEventListener('click', function() {
                    menuContent.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!menuDropdown.contains(event.target) && !menuContent.contains(event.target)) {
                        menuContent.classList.add('hidden');
                    }
                });

                // Handle page navigation
                const menuLinks = menuContent.getElementsByTagName('a');
                Array.from(menuLinks).forEach(link => {
                    link.addEventListener('click', function() {
                        closeSidebarMenu();
                    });
                });
            });
        </script>
        <?php
    }
} 