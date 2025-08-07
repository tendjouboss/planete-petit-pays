<?php
require_once 'includes/functions.php';
initSession();

$pageTitle = "Albums";

// Récupérer tous les albums avec leurs fichiers
$albums = getAlbumsWithFiles();

require_once 'includes/header.php';
?>

<!-- Section Hero avec parallaxe -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-primary-red via-primary-orange to-primary-blue">
    <!-- Éléments décoratifs animés -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-10 animate-float">
            <i class="fas fa-music text-white/10 text-8xl"></i>
        </div>
        <div class="absolute top-40 right-20 animate-float-delayed">
            <i class="fas fa-headphones text-white/10 text-6xl"></i>
        </div>
        <div class="absolute bottom-40 left-20 animate-spin-slow">
            <i class="fas fa-compact-disc text-white/10 text-7xl"></i>
        </div>
        <div class="absolute bottom-20 right-10 animate-float">
            <i class="fas fa-microphone text-white/10 text-5xl"></i>
        </div>
        <div class="absolute top-1/2 left-1/4 animate-pulse">
            <i class="fas fa-play text-white/10 text-4xl"></i>
        </div>
        <div class="absolute top-1/3 right-1/3 animate-bounce">
            <i class="fas fa-volume-up text-white/10 text-3xl"></i>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
        <div class="animate-fade-in-up">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-white to-yellow-200 bg-clip-text text-transparent">
                Nos Albums
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-200 leading-relaxed">
                Découvrez la collection complète de notre artiste. Chaque album raconte une histoire unique 
                et vous transporte dans un univers musical exceptionnel.
            </p>
            
            <!-- Statistiques animées -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto">
                <div class="text-center transform hover:scale-110 transition-transform duration-300">
                    <div class="text-4xl font-bold text-yellow-300 mb-2 counter" data-target="<?php echo count($albums); ?>">0</div>
                    <div class="text-sm text-gray-300">Albums disponibles</div>
                </div>
                <div class="text-center transform hover:scale-110 transition-transform duration-300">
                    <div class="text-4xl font-bold text-yellow-300 mb-2 counter" data-target="<?php 
                        $totalFiles = 0;
                        foreach ($albums as $album) {
                            $totalFiles += $album['nb_fichiers'];
                        }
                        echo $totalFiles;
                    ?>">0</div>
                    <div class="text-sm text-gray-300">Morceaux au total</div>
                </div>
                <div class="text-center transform hover:scale-110 transition-transform duration-300">
                    <div class="text-4xl font-bold text-yellow-300 mb-2">5 F CFA</div>
                    <div class="text-sm text-gray-300">Par téléchargement</div>
                </div>
                <div class="text-center transform hover:scale-110 transition-transform duration-300">
                    <div class="text-4xl font-bold text-yellow-300 mb-2">500 F CFA</div>
                    <div class="text-sm text-gray-300">Abonnement mensuel</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Vague décorative -->
    <div class="absolute bottom-0 left-0 w-full">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="w-full h-16">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="white"></path>
            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="white"></path>
            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="white"></path>
        </svg>
    </div>
</section>

<!-- Section Albums -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($albums)): ?>
            <!-- Filtres et recherche -->
            <div class="mb-12">
                <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="relative">
                        <input type="text" id="searchAlbums" placeholder="Rechercher un album..." 
                               class="pl-10 pr-4 py-3 w-80 border border-gray-300 rounded-full focus:ring-2 focus:ring-primary-red focus:border-transparent transition-all duration-300">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div class="flex gap-2">
                        <button class="filter-btn active px-4 py-2 rounded-full bg-primary-red text-white transition-all duration-300" data-filter="all">
                            Tous
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-full bg-white text-gray-700 hover:bg-primary-red hover:text-white transition-all duration-300" data-filter="recent">
                            Récents
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-full bg-white text-gray-700 hover:bg-primary-red hover:text-white transition-all duration-300" data-filter="popular">
                            Populaires
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Grille d'albums -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8" id="albumsGrid">
                <?php foreach ($albums as $index => $album): ?>
                    <div class="album-card group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" 
                         data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>" data-album-id="<?php echo $album['id']; ?>">
                        <!-- Image de l'album avec overlay -->
                        <div class="relative aspect-square overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-red to-primary-blue"></div>
                            <?php if (!empty($album['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($album['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($album['titre']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-compact-disc text-6xl text-white opacity-80 group-hover:animate-spin transition-all duration-500"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Overlay avec boutons -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center overlay-buttons">
                                <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                    <button onclick="playAlbumPreview(<?php echo $album['id']; ?>)" 
                                            class="bg-white text-primary-red p-4 rounded-full hover:bg-primary-red hover:text-white transition-all duration-300 transform hover:scale-110 mr-4">
                                        <i class="fas fa-play text-xl"></i>
                                    </button>
                                    <button onclick="previewAlbum(<?php echo $album['id']; ?>)" 
                                            class="bg-white text-primary-red p-4 rounded-full hover:bg-primary-red hover:text-white transition-all duration-300 transform hover:scale-110">
                                        <i class="fas fa-eye text-xl"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Badge nombre de morceaux -->
                            <?php if ($album['nb_fichiers'] > 0): ?>
                                <div class="absolute top-4 right-4 bg-white bg-opacity-90 rounded-full px-3 py-1 transform scale-0 group-hover:scale-100 transition-transform duration-300 badge">
                                    <span class="text-sm font-semibold text-gray-900"><?php echo $album['nb_fichiers']; ?> morceaux</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge prix -->
                            <?php if ($album['prix_album'] > 0): ?>
                                <div class="absolute top-4 left-4 bg-primary-red text-white rounded-full px-3 py-1 transform scale-0 group-hover:scale-100 transition-transform duration-300 badge">
                                    <span class="text-sm font-semibold"><?php echo formatPrice($album['prix_album']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Informations de l'album -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-red transition-colors duration-300">
                                <?php echo htmlspecialchars($album['titre']); ?>
                            </h3>
                            <p class="text-gray-600 mb-4 line-clamp-2"><?php echo htmlspecialchars($album['description']); ?></p>
                            
                            <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar mr-2 text-primary-red"></i>
                                    <?php echo date('d/m/Y', strtotime($album['date_sortie'])); ?>
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-primary-blue"></i>
                                    <?php echo $album['nb_fichiers']; ?> min
                                </span>
                            </div>
                            
                            <!-- Barre de progression (simulation) -->
                            <div class="mb-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Popularité</span>
                                    <span><?php echo rand(60, 95); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-primary-red to-primary-orange h-2 rounded-full transition-all duration-1000" 
                                         style="width: <?php echo rand(60, 95); ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="album.php?id=<?php echo $album['id']; ?>" 
                                   class="flex-1 bg-gradient-to-r from-primary-red to-primary-orange text-white text-center py-3 rounded-xl hover:from-primary-orange hover:to-primary-red transition-all duration-300 transform hover:scale-105 font-semibold">
                                    <i class="fas fa-play mr-2"></i>Écouter
                                </a>
                                <?php if (isLoggedIn()): ?>
                                    <button onclick="previewAlbum(<?php echo $album['id']; ?>)" 
                                            class="px-4 py-3 border-2 border-primary-red text-primary-red rounded-xl hover:bg-primary-red hover:text-white transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <!-- État vide amélioré -->
            <div class="text-center py-20">
                <div class="animate-bounce mb-8">
                    <i class="fas fa-music text-8xl text-gray-300"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-600 mb-4">Aucun album disponible</h3>
                <p class="text-gray-500 mb-8 text-lg">Les albums seront bientôt disponibles. Revenez plus tard !</p>
                <a href="index.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-primary-red to-primary-orange text-white rounded-full hover:from-primary-orange hover:to-primary-red transition-all duration-300 transform hover:scale-105 font-semibold">
                    <i class="fas fa-home mr-3"></i>Retour à l'accueil
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal de prévisualisation d'album amélioré -->
<div id="albumModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-screen overflow-y-auto transform scale-95 opacity-0 transition-all duration-300" id="modalContent">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-3xl font-bold text-gray-900" id="modalTitle">Album</h3>
                    <button onclick="closeAlbumModal()" class="text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-full transition-all duration-300">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <div id="modalBody">
                    <!-- Le contenu sera chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS personnalisés -->
<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

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

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
}

.animate-spin-slow {
    animation: spin-slow 20s linear infinite;
}

.animate-fade-in-up {
    animation: fade-in-up 1s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.album-card {
    position: relative;
}

.album-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(220, 38, 38, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.album-card:hover::before {
    opacity: 1;
}

.filter-btn.active {
    background: linear-gradient(135deg, #DC2626, #EA580C);
    color: white;
}

/* Animation pour les compteurs */
.counter {
    transition: all 0.5s ease;
}

/* Responsive design amélioré */
@media (max-width: 768px) {
    .album-card {
        margin-bottom: 2rem;
    }
}
</style>

<script>
// Animation des compteurs
function animateCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 secondes
        const step = target / (duration / 16); // 60 FPS
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    });
}

// Recherche d'albums
document.getElementById('searchAlbums').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const albumCards = document.querySelectorAll('.album-card');
    
    albumCards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const description = card.querySelector('p').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
            card.style.animation = 'fade-in-up 0.5s ease-out';
        } else {
            card.style.display = 'none';
        }
    });
});

// Filtres
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Retirer la classe active de tous les boutons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        // Ajouter la classe active au bouton cliqué
        this.classList.add('active');
        
        const filter = this.getAttribute('data-filter');
        const albumCards = document.querySelectorAll('.album-card');
        
        albumCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.animation = 'fade-in-up 0.5s ease-out';
            }, index * 100);
        });
    });
});

// Prévisualisation d'album améliorée
function previewAlbum(albumId) {
    fetch(`ajax/get_album_files.php?id=${albumId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalTitle').textContent = data.album.titre;
                document.getElementById('modalBody').innerHTML = data.html;
                
                const modal = document.getElementById('albumModal');
                const modalContent = document.getElementById('modalContent');
                
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.style.transform = 'scale(1)';
                    modalContent.style.opacity = '1';
                }, 10);
            } else {
                showNotification('Erreur lors du chargement de l\'album', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors du chargement de l\'album', 'error');
        });
}

function closeAlbumModal() {
    const modal = document.getElementById('albumModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.style.transform = 'scale(0.95)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Prévisualisation audio
function playAlbumPreview(albumId) {
    // Simulation de lecture audio
    showNotification('Lecture de l\'aperçu audio...', 'info');
}

// Système de notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 transform translate-x-full transition-transform duration-300`;
    
    switch(type) {
        case 'error':
            notification.style.backgroundColor = '#DC2626';
            break;
        case 'success':
            notification.style.backgroundColor = '#059669';
            break;
        default:
            notification.style.backgroundColor = '#2563EB';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Fermer le modal avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAlbumModal();
    }
});

// Fermer le modal en cliquant à l'extérieur
document.getElementById('albumModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAlbumModal();
    }
});

// Animation au scroll
function animateOnScroll() {
    const elements = document.querySelectorAll('[data-aos]');
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('animate-fade-in-up');
        }
    });
}

// Amélioration mobile - rendre les boutons toujours visibles
function enhanceMobileExperience() {
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Rendre les boutons overlay toujours visibles sur mobile
        const overlayButtons = document.querySelectorAll('.overlay-buttons');
        overlayButtons.forEach(overlay => {
            overlay.style.opacity = '1';
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.3)';
            
            const buttons = overlay.querySelector('div');
            if (buttons) {
                buttons.style.opacity = '1';
                buttons.style.transform = 'translateY(0)';
            }
        });
        
        // Rendre les badges toujours visibles
        const badges = document.querySelectorAll('.badge');
        badges.forEach(badge => {
            badge.style.transform = 'scale(1)';
        });
        
        // Rendre les images plus visibles sur mobile
        const albumImages = document.querySelectorAll('.album-card .aspect-square img');
        albumImages.forEach(img => {
            img.style.opacity = '0.8';
        });
    } else {
        // Sur desktop, remettre les styles par défaut
        const overlayButtons = document.querySelectorAll('.overlay-buttons');
        overlayButtons.forEach(overlay => {
            overlay.style.opacity = '';
            overlay.style.backgroundColor = '';
            
            const buttons = overlay.querySelector('div');
            if (buttons) {
                buttons.style.opacity = '';
                buttons.style.transform = '';
            }
        });
        
        const albumImages = document.querySelectorAll('.album-card .aspect-square img');
        albumImages.forEach(img => {
            img.style.opacity = '';
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    animateCounters();
    animateOnScroll();
    enhanceMobileExperience();
    
    window.addEventListener('scroll', animateOnScroll);
    window.addEventListener('resize', enhanceMobileExperience);
});
</script>

<?php require_once 'includes/footer.php'; ?> 