<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Sistem Makanan Sekolah'; ?></title>
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
        .sidebar-gradient { background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%); }
        .card-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .nav-item { transition: all 0.2s ease; }
        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .nav-item.active { background: rgba(255,255,255,0.2); border-right: 3px solid white; }
        .card-hover i, 
        .card-hover svg, 
        .card-hover [class*="fa-"],
        .bg-yellow-100 i,
        .bg-green-100 i,
        .bg-blue-100 i,
        .bg-red-100 i,
        .bg-amber-100 i,
        .bg-purple-100 i,
        .bg-cyan-100 i,
        div[class*="bg-"].flex.items-center.justify-center i {
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }
        
        .card-hover > div[class*="bg-"],
        .bg-white.rounded-xl > div[class*="bg-"],
        .bg-white.rounded-2xl > div[class*="bg-"] {
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
        }
        
        div.flex.items-center.justify-center[class*="bg-"] {
            animation: none !important;
            transition: transform 0.2s ease !important;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
