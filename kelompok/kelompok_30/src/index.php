<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header("Location: pages/dashboard_$role.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Makanan Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .login-gradient { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%); }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); }
        .floating { animation: floating 3s ease-in-out infinite; }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="min-h-screen login-gradient flex items-center justify-center p-4">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-2xl mb-4 floating">
                <i class="fas fa-utensils text-4xl text-primary-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Sistem Makanan Sekolah</h1>
            <p class="text-white/70">Silakan login untuk melanjutkan</p>
        </div>

        <div class="glass-card rounded-3xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Masuk ke Sistem</h2>
            
            <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span class="text-red-700 text-sm"><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="text-green-700 text-sm"><?php echo htmlspecialchars($_GET['success']); ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="auth/login.php" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white"
                            placeholder="Masukkan email Anda">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white"
                            placeholder="Masukkan password Anda">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:ring-4 focus:ring-primary-300 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-primary-500/30">
                    Masuk
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-center text-sm text-gray-500">
                    Default: <span class="font-medium text-gray-700">pegawai@admin.com</span> / <span class="font-medium text-gray-700">password</span>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
