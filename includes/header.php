<?php
// Initialiser la session AVANT tout affichage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Planète Petit Pays</title>
    
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
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                        'float-delayed': 'float 3s ease-in-out infinite 1.5s',
                        'spin-slow': 'spin 8s linear infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' }
                        }
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
        
        /* Animations pour mobile et desktop */
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-float-delayed {
            animation: float 3s ease-in-out infinite 1.5s;
        }
        
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }
        
        .animate-bounce-slow {
            animation: bounce 2s infinite;
        }
        
        .animate-pulse-slow {
            animation: pulse 3s infinite;
        }
        
        /* Boutons toujours visibles sur mobile */
        @media (max-width: 768px) {
            .album-card .overlay-buttons {
                opacity: 1 !important;
                transform: translateY(0) !important;
                background-color: rgba(0, 0, 0, 0.3) !important;
            }
            
            .album-card:hover .overlay-buttons {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            
            .album-card .overlay-buttons > div {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            
            .album-card:hover .overlay-buttons > div {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            
            .album-card .badge {
                transform: scale(1) !important;
            }
            
            .album-card:hover .badge {
                transform: scale(1) !important;
            }
            
            /* Rendre l'image plus visible sur mobile */
            .album-card .aspect-square img {
                opacity: 0.8 !important;
            }
            
            .album-card:hover .aspect-square img {
                opacity: 0.8 !important;
            }
        }
        
        /* Animation de comptage */
        .animate-count {
            animation: countUp 2s ease-out forwards;
        }
        
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Ligne de texte limitée */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
                    <a href="index.php" class="flex items-center space-x-2">
                        <div class="w-8 h-8 gradient-bg rounded-full flex items-center justify-center">
                            <i class="fas fa-music text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Planète Petit Pays</span>
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-home mr-1"></i>Accueil
                    </a>
                    <a href="albums.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-compact-disc mr-1"></i>Albums
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-user mr-1"></i>Mon Profil
                        </a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin/" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-cog mr-1"></i>Admin
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-700 hover:text-primary-red px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-sign-in-alt mr-1"></i>Connexion
                        </a>
                        <a href="register.php" class="bg-primary-red text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
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
                    <a href="index.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-home mr-1"></i>Accueil
                    </a>
                    <a href="albums.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-compact-disc mr-1"></i>Albums
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-user mr-1"></i>Mon Profil
                        </a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin/" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                                <i class="fas fa-cog mr-1"></i>Admin
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-700 hover:text-primary-red block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i>Connexion
                        </a>
                        <a href="register.php" class="bg-primary-red text-white block px-3 py-2 rounded-md text-base font-medium hover:bg-red-700">
                            <i class="fas fa-user-plus mr-1"></i>Inscription
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php 
        // Afficher les messages seulement si la fonction existe
        if (function_exists('displayMessage')) {
            echo displayMessage(); 
        }
        ?> 