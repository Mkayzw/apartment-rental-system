<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/lib/src/Register.php';

use app\config\Database;
use lib\src\Register;

$database = new Database();
$conn = $database->getConnection();
$register = new Register();
$message = $register->registerUser();

// If registration was successful and we have a redirect, don't show the form
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $redirectUrl = '';
    switch($_SESSION['role']) {
        case 'agent':
            $redirectUrl = '/uzoca/agent/subscription.php';
            break;
        case 'landlord':
            $redirectUrl = '/uzoca/landlord/dashboard.php';
            break;
        case 'admin':
            $redirectUrl = '/uzoca/admin/index.php';
            break;
        default:
            $redirectUrl = '/uzoca/login.php';
    }
    header("Location: $redirectUrl");
    exit();
}

// If we have a success message, show it and redirect after 3 seconds
if (strpos($message, 'successful') !== false) {
    echo "<script>
        setTimeout(function() {
            window.location.href = '/uzoca/login.php';
        }, 3000);
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UZOCA</title>
    <link rel="stylesheet" href="/uzoca/assets/css/style.css">
    <link rel="stylesheet" href="/uzoca/assets/fonts/fonts.min.css">
    <link rel="icon" type="image/x-icon" href="/uzoca/assets/img/logo-light.png">
    <script>
        // Set dark mode as default
            document.documentElement.classList.add('dark');
        document.documentElement.style.backgroundColor = '#0f172a';
        document.documentElement.style.color = '#f1f5f9';
        localStorage.setItem('theme', 'dark');
    </script>
</head>
<body class="bg-slate-100 dark:bg-slate-900 transition-colors duration-200">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white dark:bg-slate-800 rounded-lg shadow-md">
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    Create your account
                </h2>
                <button id="theme-toggle" class="p-2 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors duration-200">
                    <i class="fr fi-rr-moon text-xl text-slate-800 dark:text-slate-200"></i>
                </button>
            </div>

            <?php if ($message): ?>
            <div class="rounded-md p-4 <?php echo strpos($message, 'successful') !== false ? 'bg-green-50 dark:bg-green-900' : 'bg-red-50 dark:bg-red-900'; ?>">
                <p class="text-sm <?php echo strpos($message, 'successful') !== false ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'; ?>">
                    <?php echo $message; ?>
                </p>
            </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="userType" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Register as</label>
                        <select name="userType" id="userType" required class="mt-1 block w-full py-2 px-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                        <option value="">Select User Type</option>
                            <option value="landlord" <?php echo isset($_POST['userType']) && $_POST['userType'] === 'landlord' ? 'selected' : ''; ?>>Landlord</option>
                            <option value="agent" <?php echo isset($_POST['userType']) && $_POST['userType'] === 'agent' ? 'selected' : ''; ?>>Agent</option>
                    </select>
                </div>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name</label>
                        <input id="name" name="name" type="text" required class="mt-1 block w-full py-2 px-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone Number</label>
                        <input id="phone" name="phone" type="tel" required class="mt-1 block w-full py-2 px-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email address</label>
                        <input id="email" name="email" type="email" required class="mt-1 block w-full py-2 px-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                        <input id="password" name="password" type="password" required class="mt-1 block w-full py-2 px-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm Password</label>
                        <input id="password_confirm" name="password-confirm" type="password" required class="mt-1 block w-full py-2 px-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                </div>

                <div>
                    <button type="submit" name="register-submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                        Register
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Already have an account?
                    <a href="/uzoca/login.php" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        
        themeToggle.addEventListener('click', () => {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.backgroundColor = '#f8fafc';
                document.documentElement.style.color = '#1e293b';
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.style.backgroundColor = '#0f172a';
                document.documentElement.style.color = '#f1f5f9';
                localStorage.theme = 'dark';
            }
        });
    </script>
</body>
</html>
