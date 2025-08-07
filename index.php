<?php
require_once 'includes/functions.php';
initSession();

// Récupérer les albums récents
$albums = getAlbumsWithFiles();

// Vérifier si l'utilisateur est connecté et a un abonnement actif
$hasSubscription = false;
if (isLoggedIn()) {
    $hasSubscription = hasActiveSubscription($_SESSION['user_id']);
}

$pageTitle = 'Accueil - Planète Petit Pays';
require_once 'includes/header.php';
?>

<!-- Hero Section avec animation et image de fond -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Image de fond avec overlay -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-red/90 via-primary-orange/80 to-primary-blue/90"></div>
        <div class="absolute inset-0 bg-black/40"></div>
        <!-- Éléments décoratifs musicaux -->
        <div class="absolute top-20 left-10 animate-bounce">
            <i class="fas fa-music text-white/20 text-6xl"></i>
        </div>
        <div class="absolute top-40 right-20 animate-pulse">
            <i class="fas fa-headphones text-white/20 text-4xl"></i>
        </div>
        <div class="absolute bottom-40 left-20 animate-spin-slow">
            <i class="fas fa-compact-disc text-white/20 text-5xl"></i>
        </div>
        <div class="absolute bottom-20 right-10 animate-bounce">
            <i class="fas fa-microphone text-white/20 text-3xl"></i>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
        <div class="animate-fade-in-up">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-white to-yellow-200 bg-clip-text text-transparent">
                Planète Petit Pays
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-200 leading-relaxed">
                Découvrez la richesse musicale de la Côte d'Ivoire<br>
                <span class="text-yellow-300 font-semibold">Téléchargez, écoutez, partagez</span>
            </p>
            
            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <?php if (isLoggedIn()): ?>
                    <a href="albums.php" 
                       class="group bg-white text-primary-red px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-200 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                        <i class="fas fa-music mr-3 group-hover:animate-pulse"></i>
                        Explorer les albums
                    </a>
                    <?php if (!$hasSubscription): ?>
                        <a href="payment.php?type=subscription&amount=<?php echo PRIX_ABONNEMENT; ?>" 
                           class="group bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                            <i class="fas fa-crown mr-3 group-hover:animate-bounce"></i>
                            S'abonner maintenant
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" 
                       class="group bg-white text-primary-red px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-200 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                        <i class="fas fa-sign-in-alt mr-3 group-hover:animate-pulse"></i>
                        Se connecter
                    </a>
                    <a href="register.php" 
                       class="group bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                        <i class="fas fa-user-plus mr-3 group-hover:animate-bounce"></i>
                        S'inscrire
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Statistiques -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-300 mb-2 animate-count">50+</div>
                    <div class="text-sm text-gray-300">Albums</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-300 mb-2 animate-count">500+</div>
                    <div class="text-sm text-gray-300">Morceaux</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-300 mb-2 animate-count">1000+</div>
                    <div class="text-sm text-gray-300">Téléchargements</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-300 mb-2 animate-count">24/7</div>
                    <div class="text-sm text-gray-300">Disponible</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <i class="fas fa-chevron-down text-white text-2xl"></i>
    </div>
</section>

<!-- Section Albums Récents -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                <i class="fas fa-fire text-primary-red mr-3 animate-pulse"></i>
                Albums Récents
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Découvrez les dernières sorties musicales de nos artistes talentueux
            </p>
        </div>
        
        <?php if (!empty($albums)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($albums as $album): ?>
                    <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                        <!-- Image de l'album avec overlay -->
                        <div class="relative overflow-hidden">
                            <div class="aspect-square bg-gradient-to-br from-primary-red to-primary-blue">
                                <?php if (!empty($album['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($album['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($album['titre']); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-compact-disc text-white text-6xl opacity-80"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Overlay avec boutons -->
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="flex space-x-3">
                                    <a href="album.php?id=<?php echo $album['id']; ?>" 
                                       class="bg-white text-primary-red p-3 rounded-full hover:bg-primary-red hover:text-white transition-all duration-300 transform hover:scale-110">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (isLoggedIn()): ?>
                                        <?php if ($hasSubscription): ?>
                                            <button onclick="downloadAlbum(<?php echo $album['id']; ?>, true)" 
                                                    class="bg-green-500 text-white p-3 rounded-full hover:bg-green-600 transition-all duration-300 transform hover:scale-110">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        <?php else: ?>
                                            <button onclick="downloadAlbum(<?php echo $album['id']; ?>, false)" 
                                                    class="bg-primary-blue text-white p-3 rounded-full hover:bg-blue-600 transition-all duration-300 transform hover:scale-110">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="login.php" 
                                           class="bg-primary-red text-white p-3 rounded-full hover:bg-red-700 transition-all duration-300 transform hover:scale-110">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Badge prix -->
                            <div class="absolute top-4 right-4 bg-primary-red text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <?php echo formatPrice(PRIX_UNITAIRE); ?> par morceau
                            </div>
                        </div>
                        
                        <!-- Informations de l'album -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-red transition-colors duration-300">
                                <?php echo htmlspecialchars($album['titre']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                <?php echo htmlspecialchars(substr($album['description'], 0, 100)) . '...'; ?>
                            </p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?php echo date('d/m/Y', strtotime($album['date_sortie'])); ?>
                                </span>
                                <span>
                                    <i class="fas fa-music mr-1"></i>
                                    <?php echo $album['nb_fichiers']; ?> morceaux
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Bouton voir plus -->
            <div class="text-center mt-12">
                <a href="albums.php" 
                   class="inline-flex items-center bg-gradient-to-r from-primary-red to-primary-orange text-white px-8 py-4 rounded-full font-semibold text-lg hover:from-red-700 hover:to-orange-700 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                    <i class="fas fa-music mr-3"></i>
                    Voir tous les albums
                    <i class="fas fa-arrow-right ml-3"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <div class="bg-white rounded-2xl shadow-lg p-12 max-w-md mx-auto">
                    <i class="fas fa-music text-6xl text-gray-300 mb-6"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucun album disponible</h3>
                    <p class="text-gray-500">Les albums seront bientôt disponibles.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Section Avantages -->
<section class="py-20 bg-gradient-to-br from-primary-red via-primary-orange to-primary-yellow text-white relative overflow-hidden">
    <!-- Éléments décoratifs animés -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 animate-float">
            <i class="fas fa-music text-8xl"></i>
        </div>
        <div class="absolute top-40 right-20 animate-float-delayed">
            <i class="fas fa-headphones text-6xl"></i>
        </div>
        <div class="absolute bottom-40 left-1/4 animate-spin-slow">
            <i class="fas fa-compact-disc text-7xl"></i>
        </div>
        <div class="absolute bottom-20 right-1/3 animate-bounce">
            <i class="fas fa-microphone text-5xl"></i>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-white to-yellow-200 bg-clip-text text-transparent">
                <i class="fas fa-star mr-4 animate-pulse"></i>
                Pourquoi choisir Planète Petit Pays ?
            </h2>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto leading-relaxed">
                Une expérience musicale unique avec des avantages exclusifs pour tous les passionnés de musique
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center group transform hover:-translate-y-2 transition-all duration-500">
                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-10 mb-6 group-hover:bg-white/20 transition-all duration-500 transform group-hover:scale-105 border border-white/20 group-hover:border-white/40 shadow-2xl">
                    <div class="bg-gradient-to-r from-white to-yellow-200 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 group-hover:animate-bounce transition-all duration-300">
                        <i class="fas fa-download text-3xl text-primary-red"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-yellow-200">Téléchargements Illimités</h3>
                    <p class="opacity-90 text-lg leading-relaxed">Accédez à tous les albums et morceaux sans limite avec votre abonnement premium.</p>
                </div>
            </div>
            
            <div class="text-center group transform hover:-translate-y-2 transition-all duration-500">
                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-10 mb-6 group-hover:bg-white/20 transition-all duration-500 transform group-hover:scale-105 border border-white/20 group-hover:border-white/40 shadow-2xl">
                    <div class="bg-gradient-to-r from-white to-yellow-200 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 group-hover:animate-bounce transition-all duration-300">
                        <i class="fas fa-mobile-alt text-3xl text-primary-red"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-yellow-200">Paiement Mobile Money</h3>
                    <p class="opacity-90 text-lg leading-relaxed">Paiement sécurisé et instantané via Orange Money et MTN Mobile Money.</p>
                </div>
            </div>
            
            <div class="text-center group transform hover:-translate-y-2 transition-all duration-500">
                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-10 mb-6 group-hover:bg-white/20 transition-all duration-500 transform group-hover:scale-105 border border-white/20 group-hover:border-white/40 shadow-2xl">
                    <div class="bg-gradient-to-r from-white to-yellow-200 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 group-hover:animate-bounce transition-all duration-300">
                        <i class="fas fa-headphones text-3xl text-primary-red"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-yellow-200">Qualité Premium</h3>
                    <p class="opacity-90 text-lg leading-relaxed">Profitez d'une qualité audio exceptionnelle pour tous vos morceaux favoris.</p>
                </div>
            </div>
        </div>
        
        <!-- Statistiques supplémentaires -->
        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center transform hover:scale-110 transition-transform duration-300">
                <div class="text-4xl font-bold text-yellow-300 mb-2 animate-count">24/7</div>
                <div class="text-sm opacity-80">Disponible</div>
            </div>
            <div class="text-center transform hover:scale-110 transition-transform duration-300">
                <div class="text-4xl font-bold text-yellow-300 mb-2 animate-count">100%</div>
                <div class="text-sm opacity-80">Sécurisé</div>
            </div>
            <div class="text-center transform hover:scale-110 transition-transform duration-300">
                <div class="text-4xl font-bold text-yellow-300 mb-2 animate-count">500+</div>
                <div class="text-sm opacity-80">Morceaux</div>
            </div>
            <div class="text-center transform hover:scale-110 transition-transform duration-300">
                <div class="text-4xl font-bold text-yellow-300 mb-2 animate-count">50+</div>
                <div class="text-sm opacity-80">Albums</div>
            </div>
        </div>
    </div>
</section>

<!-- Section Abonnement -->
<section class="py-20 bg-gradient-to-b from-gray-900 via-gray-800 to-black text-white relative overflow-hidden">
    <!-- Éléments décoratifs -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 left-20 animate-spin-slow">
            <i class="fas fa-compact-disc text-9xl"></i>
        </div>
        <div class="absolute bottom-20 right-20 animate-bounce">
            <i class="fas fa-music text-6xl"></i>
        </div>
        <div class="absolute top-1/2 left-1/4 animate-float">
            <i class="fas fa-headphones text-5xl"></i>
        </div>
        <div class="absolute top-1/3 right-1/3 animate-pulse">
            <i class="fas fa-microphone text-4xl"></i>
        </div>
    </div>
    
    <div class="max-w-6xl mx-auto px-4 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-yellow-300 to-orange-400 bg-clip-text text-transparent">
                <i class="fas fa-crown mr-4 animate-pulse"></i>
                Passez à l'abonnement Premium
            </h2>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto leading-relaxed">
                Découvrez un monde de musique illimité avec notre abonnement premium
            </p>
        </div>
        
        <div class="bg-gradient-to-r from-primary-red via-primary-orange to-primary-yellow rounded-3xl p-12 relative overflow-hidden shadow-2xl border border-yellow-400/20">
            <!-- Éléments décoratifs du card -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <div class="absolute top-10 left-10 animate-spin-slow">
                    <i class="fas fa-compact-disc text-6xl"></i>
                </div>
                <div class="absolute bottom-10 right-10 animate-bounce">
                    <i class="fas fa-music text-4xl"></i>
                </div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-pulse">
                    <i class="fas fa-star text-3xl"></i>
                </div>
            </div>
            
            <div class="relative z-10">
                <!-- Prix mis en avant -->
                <div class="text-center mb-12">
                    <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-8 inline-block">
                        <div class="text-6xl md:text-7xl font-bold text-yellow-300 mb-4 animate-pulse">
                            <?php echo formatPrice(PRIX_ABONNEMENT); ?>
                        </div>
                        <div class="text-xl opacity-90">par mois</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
                    <div class="space-y-6">
                        <h3 class="text-2xl font-bold mb-6 text-yellow-300 flex items-center">
                            <i class="fas fa-gift mr-3"></i>
                            Avantages inclus :
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-center group">
                                <div class="bg-green-500 rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <span class="text-lg">Téléchargements illimités</span>
                            </li>
                            <li class="flex items-center group">
                                <div class="bg-green-500 rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <span class="text-lg">Accès à tous les albums</span>
                            </li>
                            <li class="flex items-center group">
                                <div class="bg-green-500 rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <span class="text-lg">Qualité audio premium</span>
                            </li>
                            <li class="flex items-center group">
                                <div class="bg-green-500 rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <span class="text-lg">Support prioritaire</span>
                            </li>
                            <li class="flex items-center group">
                                <div class="bg-green-500 rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                                <span class="text-lg">Nouveautés en avant-première</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="space-y-6">
                        <h3 class="text-2xl font-bold mb-6 text-yellow-300 flex items-center">
                            <i class="fas fa-play-circle mr-3"></i>
                            Comment ça marche :
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-center group">
                                <div class="bg-primary-blue rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <span class="text-white font-bold text-sm">1</span>
                                </div>
                                <span class="text-lg">Choisissez votre abonnement</span>
                            </li>
                            <li class="flex items-center group">
                                <div class="bg-primary-blue rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <span class="text-white font-bold text-sm">2</span>
                                </div>
                                <span class="text-lg">Payez via Mobile Money</span>
                            </li>
                            <li class="flex items-center group">
                                <div class="bg-primary-blue rounded-full w-8 h-8 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <span class="text-white font-bold text-sm">3</span>
                                </div>
                                <span class="text-lg">Téléchargez sans limite</span>
                            </li>
                        </ul>
                        
                        <!-- Statistiques rapides -->
                        <div class="mt-8 p-6 bg-white/10 rounded-2xl">
                            <h4 class="text-lg font-bold mb-4 text-yellow-300">Chiffres clés :</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-300">1000+</div>
                                    <div class="text-sm opacity-80">Utilisateurs</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-300">99%</div>
                                    <div class="text-sm opacity-80">Satisfaction</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="text-center">
                    <?php if (isLoggedIn() && !$hasSubscription): ?>
                        <a href="payment.php?type=subscription&amount=<?php echo PRIX_ABONNEMENT; ?>" 
                           class="inline-flex items-center bg-white text-primary-red px-10 py-5 rounded-full font-bold text-xl hover:bg-yellow-200 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg">
                            <i class="fas fa-crown mr-4 text-2xl"></i>
                            S'abonner maintenant
                        </a>
                    <?php elseif (!isLoggedIn()): ?>
                        <div class="flex flex-col sm:flex-row gap-6 justify-center">
                            <a href="login.php" 
                               class="inline-flex items-center bg-white text-primary-red px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-200 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-sign-in-alt mr-3"></i>
                                Se connecter
                            </a>
                            <a href="register.php" 
                               class="inline-flex items-center bg-yellow-400 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-user-plus mr-3"></i>
                                S'inscrire
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="bg-green-500 text-white px-8 py-4 rounded-full inline-flex items-center text-lg font-bold shadow-lg">
                            <i class="fas fa-check-circle mr-3 text-2xl"></i>
                            Vous êtes déjà abonné !
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de confirmation de téléchargement -->
<div id="downloadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl">
            <div class="text-center">
                <div class="bg-gradient-to-r from-primary-red to-primary-orange rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-download text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4" id="modalTitle">Télécharger l'album</h3>
                <p class="text-gray-600 mb-8" id="modalMessage">
                    Voulez-vous télécharger cet album ?
                </p>
                
                <div class="flex space-x-4">
                    <button onclick="closeDownloadModal()" 
                            class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-400 transition-colors font-semibold">
                        Annuler
                    </button>
                    <button onclick="confirmDownload()" 
                            class="flex-1 bg-gradient-to-r from-primary-red to-primary-orange text-white px-6 py-3 rounded-xl hover:from-red-700 hover:to-orange-700 transition-all transform hover:scale-105 font-semibold">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes count {
    from {
        opacity: 0;
        transform: scale(0.5);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 1s ease-out;
}

.animate-spin-slow {
    animation: spin-slow 3s linear infinite;
}

.animate-count {
    animation: count 1s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
let currentAlbumId = null;
let isSubscribed = <?php echo $hasSubscription ? 'true' : 'false'; ?>;

function downloadAlbum(albumId, subscribed) {
    currentAlbumId = albumId;
    
    if (subscribed) {
        // Utilisateur abonné - téléchargement direct
        showDownloadModal('Téléchargement gratuit', 'Votre abonnement vous permet de télécharger gratuitement cet album.');
    } else {
        // Utilisateur non abonné - demande de paiement
        showDownloadModal('Paiement requis', 'Le téléchargement de cet album coûte 5 F CFA par morceau. Voulez-vous continuer ?');
    }
}

function showDownloadModal(title, message) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('downloadModal').classList.remove('hidden');
}

function closeDownloadModal() {
    document.getElementById('downloadModal').classList.add('hidden');
    currentAlbumId = null;
}

function confirmDownload() {
    if (currentAlbumId) {
        if (isSubscribed) {
            // Redirection vers le téléchargement gratuit
            window.location.href = `download_album.php?id=${currentAlbumId}&free=1`;
        } else {
            // Redirection vers la page de paiement
            window.location.href = `payment.php?album_id=${currentAlbumId}&type=download`;
        }
    }
    closeDownloadModal();
}

// Fermer le modal avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDownloadModal();
    }
});

// Fermer le modal en cliquant à l'extérieur
document.getElementById('downloadModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDownloadModal();
    }
});

// Animation au scroll
document.addEventListener('DOMContentLoaded', function() {
    // Amélioration mobile
    enhanceMobileExperience();
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observer les éléments à animer
    document.querySelectorAll('.group').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(el);
    });
    
    // Animation des compteurs
    animateCounters();
});

// Amélioration mobile
function enhanceMobileExperience() {
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Ajuster la taille des textes pour mobile
        const heroTitle = document.querySelector('.hero-title');
        if (heroTitle) {
            heroTitle.classList.remove('text-5xl', 'md:text-7xl');
            heroTitle.classList.add('text-4xl', 'md:text-6xl');
        }
        
        // Rendre les boutons plus grands sur mobile
        const buttons = document.querySelectorAll('.hero-buttons button, .hero-buttons a');
        buttons.forEach(button => {
            button.classList.add('py-3', 'px-6', 'text-base');
        });
        
        // Ajuster les statistiques pour mobile
        const stats = document.querySelectorAll('.stats-grid > div');
        stats.forEach(stat => {
            stat.classList.add('text-center', 'mb-4');
        });
    }
}

// Animation des compteurs
function animateCounters() {
    const counters = document.querySelectorAll('.animate-count');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/\D/g, ''));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current) + (counter.textContent.includes('+') ? '+' : '');
        }, 16);
    });
}

// Écouter le redimensionnement de la fenêtre
window.addEventListener('resize', enhanceMobileExperience);
</script>

<?php require_once 'includes/footer.php'; ?> 