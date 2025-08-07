<?php
require_once 'includes/functions.php';
initSession();

$albumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$albumId) {
    setMessage('error', 'Album non trouvé');
    redirect('albums.php');
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND actif = 1");
$stmt->execute([$albumId]);
$album = $stmt->fetch();

if (!$album) {
    setMessage('error', 'Album non trouvé');
    redirect('albums.php');
}

$files = getAlbumFiles($albumId);

// Vérifier si l'utilisateur est connecté et a un abonnement actif
$hasSubscription = false;
if (isLoggedIn()) {
    $hasSubscription = hasActiveSubscription($_SESSION['user_id']);
}

$pageTitle = $album['titre'];
require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Header de l'album -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="md:flex">
            <!-- Image de l'album -->
            <div class="md:w-1/3">
                <div class="aspect-square bg-gradient-to-br from-primary-red to-primary-blue flex items-center justify-center">
                    <?php if (!empty($album['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($album['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($album['titre']); ?>" 
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-compact-disc text-8xl text-white opacity-80"></i>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Informations de l'album -->
            <div class="md:w-2/3 p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($album['titre']); ?></h1>
                <p class="text-gray-600 mb-6"><?php echo nl2br(htmlspecialchars($album['description'])); ?></p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-red"><?php echo count($files); ?></div>
                        <div class="text-sm text-gray-600">Morceaux</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-orange"><?php echo date('d/m/Y', strtotime($album['date_sortie'])); ?></div>
                        <div class="text-sm text-gray-600">Date de sortie</div>
                    </div>
                    <?php if ($album['prix_album'] > 0): ?>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary-yellow"><?php echo formatPrice($album['prix_album']); ?></div>
                            <div class="text-sm text-gray-600">Prix album</div>
                        </div>
                    <?php endif; ?>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-blue"><?php echo formatPrice(PRIX_UNITAIRE); ?></div>
                        <div class="text-sm text-gray-600">Par morceau</div>
                    </div>
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <div class="flex flex-wrap gap-4">
                        <!-- Bouton de téléchargement d'album -->
                        <?php if ($hasSubscription): ?>
                            <!-- Utilisateur abonné - téléchargement gratuit -->
                            <button onclick="downloadAlbum(<?php echo $albumId; ?>, true)" 
                                    class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition-all">
                                <i class="fas fa-download mr-2"></i>Télécharger l'album (Gratuit)
                            </button>
                        <?php else: ?>
                            <!-- Utilisateur non abonné - demande de paiement -->
                            <button onclick="downloadAlbum(<?php echo $albumId; ?>, false)" 
                                    class="bg-primary-blue text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-all">
                                <i class="fas fa-download mr-2"></i>Télécharger l'album
                            </button>
                        <?php endif; ?>
                        
                        <!-- Bouton d'abonnement -->
                        <a href="payment.php?type=subscription&amount=<?php echo PRIX_ABONNEMENT; ?>" 
                           class="bg-gradient-to-r from-primary-red to-primary-orange text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-orange-700 transition-all">
                            <i class="fas fa-crown mr-2"></i>S'abonner (<?php echo formatPrice(PRIX_ABONNEMENT); ?>)
                        </a>
                        
                        <!-- Bouton retour -->
                        <a href="albums.php" class="border border-primary-red text-primary-red px-6 py-3 rounded-lg font-semibold hover:bg-primary-red hover:text-white transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Retour aux albums
                        </a>
                    </div>
                    
                    <!-- Indicateur de prix pour téléchargement -->
                    <?php if (!$hasSubscription): ?>
                        <div class="mt-4 text-center">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Téléchargement : 5 F CFA par morceau (<?php echo count($files); ?> morceaux = <?php echo count($files) * 5; ?> F CFA)
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="mt-4 text-center">
                            <span class="text-sm text-green-600">
                                <i class="fas fa-crown mr-1"></i>
                                Téléchargement gratuit avec votre abonnement
                            </span>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="flex space-x-4">
                        <a href="login.php" class="bg-primary-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter pour télécharger
                        </a>
                        <a href="register.php" class="border border-primary-red text-primary-red px-6 py-3 rounded-lg font-semibold hover:bg-primary-red hover:text-white transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>S'inscrire
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Liste des fichiers -->
    <?php if (!empty($files)): ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Morceaux de l'album</h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($files as $file): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary-red to-primary-blue rounded-lg flex items-center justify-center">
                                    <i class="fas fa-<?php echo $file['type'] === 'audio' ? 'music' : 'video'; ?> text-white"></i>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($file['titre']); ?></h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>
                                            <i class="fas fa-<?php echo $file['type'] === 'audio' ? 'music' : 'video'; ?> mr-1"></i>
                                            <?php echo ucfirst($file['type']); ?>
                                        </span>
                                        <?php if ($file['duree']): ?>
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                <?php echo $file['duree']; ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($file['taille_fichier']): ?>
                                            <span>
                                                <i class="fas fa-file mr-1"></i>
                                                <?php echo formatFileSize($file['taille_fichier']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <?php if (isLoggedIn()): ?>
                                    <?php 
                                    $hasSubscription = hasActiveSubscription($_SESSION['user_id']);
                                    $hasPurchased = hasPurchasedFile($_SESSION['user_id'], $file['id']);
                                    ?>
                                    
                                    <?php if ($hasSubscription || $hasPurchased): ?>
                                        <!-- Téléchargement gratuit -->
                                        <button onclick="downloadFile(<?php echo $file['id']; ?>)" 
                                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-download mr-2"></i>Télécharger
                                        </button>
                                    <?php else: ?>
                                        <!-- Bouton de téléchargement avec paiement -->
                                        <button onclick="downloadSingleFile(<?php echo $file['id']; ?>, '<?php echo htmlspecialchars($file['titre']); ?>', <?php echo $file['prix']; ?>)" 
                                                class="bg-primary-blue text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-download mr-2"></i>Télécharger (<?php echo formatPrice($file['prix']); ?>)
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Prévisualisation -->
                                    <button onclick="previewMedia('<?php echo htmlspecialchars($file['url']); ?>', '<?php echo $file['type']; ?>')" 
                                            class="border border-primary-red text-primary-red px-3 py-2 rounded-lg hover:bg-primary-red hover:text-white transition-colors">
                                        <i class="fas fa-play"></i>
                                    </button>
                                <?php else: ?>
                                    <a href="login.php" class="bg-primary-red text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <i class="fas fa-music text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucun morceau disponible</h3>
            <p class="text-gray-500">Les morceaux seront bientôt disponibles.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmation de téléchargement -->
<div id="downloadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="text-center">
                <i class="fas fa-download text-4xl text-primary-blue mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2" id="modalTitle">Télécharger l'album</h3>
                <p class="text-gray-600 mb-6" id="modalMessage">
                    Voulez-vous télécharger cet album ?
                </p>
                
                <div class="flex space-x-4">
                    <button onclick="closeDownloadModal()" 
                            class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button onclick="confirmDownload()" 
                            class="flex-1 bg-primary-blue text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentAlbumId = null;
let currentFileId = null;
let currentFileTitle = null;
let currentFilePrice = null;
let isSubscribed = <?php echo $hasSubscription ? 'true' : 'false'; ?>;

function downloadAlbum(albumId, subscribed) {
    currentAlbumId = albumId;
    currentFileId = null;
    currentFileTitle = null;
    currentFilePrice = null;
    
    if (subscribed) {
        // Utilisateur abonné - téléchargement direct
        showDownloadModal('Téléchargement gratuit', 'Votre abonnement vous permet de télécharger gratuitement cet album.');
    } else {
        // Utilisateur non abonné - demande de paiement
        const totalCost = <?php echo count($files) * 5; ?>;
        showDownloadModal('Paiement requis', `Le téléchargement de cet album coûte ${totalCost} F CFA (5 F CFA par morceau). Voulez-vous continuer ?`);
    }
}

function downloadSingleFile(fileId, fileTitle, filePrice) {
    currentFileId = fileId;
    currentFileTitle = fileTitle;
    currentFilePrice = filePrice;
    currentAlbumId = null;
    
    if (isSubscribed) {
        // Utilisateur abonné - téléchargement direct
        showDownloadModal('Téléchargement gratuit', `Votre abonnement vous permet de télécharger gratuitement "${fileTitle}".`);
    } else {
        // Utilisateur non abonné - demande de paiement
        showDownloadModal('Paiement requis', `Le téléchargement de "${fileTitle}" coûte ${filePrice} F CFA. Voulez-vous continuer ?`);
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
    currentFileId = null;
    currentFileTitle = null;
    currentFilePrice = null;
}

function confirmDownload() {
    if (currentAlbumId) {
        // Téléchargement d'album complet
        if (isSubscribed) {
            // Redirection vers le téléchargement gratuit
            window.location.href = `download_album.php?id=${currentAlbumId}&free=1`;
        } else {
            // Redirection vers la page de paiement
            window.location.href = `payment.php?album_id=${currentAlbumId}&type=download`;
        }
    } else if (currentFileId) {
        // Téléchargement d'un fichier individuel
        if (isSubscribed) {
            // Téléchargement direct pour utilisateur abonné
            downloadFile(currentFileId);
        } else {
            // Redirection vers la page de paiement pour un fichier individuel
            window.location.href = `payment.php?file_id=${currentFileId}&type=single`;
        }
    }
    closeDownloadModal();
}

function downloadFile(fileId) {
    // Créer un token de téléchargement
    fetch('ajax/create_download_token.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            file_id: fileId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Rediriger vers le téléchargement
            window.location.href = `download.php?token=${data.token}`;
        } else {
            alert('Erreur lors de la création du lien de téléchargement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du téléchargement');
    });
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
</script>

<?php require_once 'includes/footer.php'; ?> 