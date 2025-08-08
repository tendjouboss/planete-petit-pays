<?php
require_once __DIR__ . '/functions.php';
initSession();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Administration - Planète Petit Pays</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-red': '#DC2626',
                        'primary-orange': '#EA580C',
                        'primary-yellow': '#EAB308',
                        'primary-blue': '#2563EB',
                        'primary-green': '#059669',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #DC2626 0%, #EA580C 25%, #EAB308 50%, #2563EB 100%);
        }
        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="../index.php" class="flex items-center space-x-2">
                        <div class="w-8 h-8 gradient-bg rounded-full flex items-center justify-center">
                            <i class="fas fa-music text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Planète Petit Pays</span>
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="../index.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-home mr-1"></i>Accueil
                    </a>
                    <a href="../albums.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-compact-disc mr-1"></i>Albums
                    </a>
                    <?php if (isLoggedIn()): ?>
                        <a href="../profile.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-user mr-1"></i>Mon Profil
                        </a>
                        <a href="index.php" class="bg-primary-red text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-cog mr-1"></i>Administration
                        </a>
                        <a href="../logout.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="../login.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-sign-in-alt mr-1"></i>Connexion
                        </a>
                        <a href="../register.php" class="bg-primary-red text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                            <i class="fas fa-user-plus mr-1"></i>Inscription
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="mobile-menu-button text-gray-700 hover:text-primary-red">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div class="mobile-menu hidden md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="../index.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-home mr-1"></i>Accueil
                    </a>
                    <a href="../albums.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-compact-disc mr-1"></i>Albums
                    </a>
                    <?php if (isLoggedIn()): ?>
                        <a href="../profile.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user mr-1"></i>Mon Profil
                        </a>
                        <a href="index.php" class="bg-primary-red text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-cog mr-1"></i>Administration
                        </a>
                        <a href="../logout.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="../login.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i>Connexion
                        </a>
                        <a href="../register.php" class="bg-primary-red text-white block px-3 py-2 rounded-md text-base font-medium hover:bg-red-700">
                            <i class="fas fa-user-plus mr-1"></i>Inscription
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php echo displayMessage(); ?> 