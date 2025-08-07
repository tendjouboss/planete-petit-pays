<?php
require_once 'includes/functions.php';
initSession();

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    setMessage('error', 'Vous devez être connecté pour télécharger des albums');
    redirect('login.php');
}

$album_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_free = isset($_GET['free']) && $_GET['free'] == '1';

if (!$album_id) {
    setMessage('error', 'Album non trouvé');
    redirect('albums.php');
}

// Récupérer les informations de l'album
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND actif = 1");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch();
    
    if (!$album) {
        setMessage('error', 'Album non trouvé ou non disponible');
        redirect('albums.php');
    }
    
    // Récupérer les fichiers de l'album
    $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE album_id = ? AND actif = 1 ORDER BY titre");
    $stmt->execute([$album_id]);
    $fichiers = $stmt->fetchAll();
    
    if (empty($fichiers)) {
        setMessage('error', 'Aucun fichier disponible pour cet album');
        redirect('albums.php');
    }
    
} catch (PDOException $e) {
    setMessage('error', 'Erreur lors de la récupération de l\'album');
    redirect('albums.php');
}

// Vérifier le statut d'abonnement
$hasSubscription = hasActiveSubscription($_SESSION['user_id']);

// Vérifier si c'est un téléchargement payant
$is_paid = isset($_GET['paid']) && $_GET['paid'] == '1';

// Si ce n'est pas un téléchargement gratuit et pas payé, vérifier l'abonnement
if (!$is_free && !$hasSubscription && !$is_paid) {
    setMessage('error', 'Vous devez avoir un abonnement actif ou payer pour télécharger cet album');
    redirect('payment.php?album_id=' . $album_id . '&type=download');
}

// Si c'est un téléchargement payant, vérifier les tokens
if ($is_paid) {
    if (!isset($_SESSION['download_tokens']) || !isset($_SESSION['paid_album_id']) || $_SESSION['paid_album_id'] != $album_id) {
        setMessage('error', 'Session de téléchargement invalide. Veuillez effectuer le paiement à nouveau.');
        redirect('payment.php?album_id=' . $album_id . '&type=download');
    }
}

$pageTitle = 'Télécharger ' . $album['titre'];
require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-6 bg-gradient-to-r from-primary-red to-primary-orange bg-clip-text text-transparent">
            Télécharger l'album
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Préparez-vous à télécharger "<?= htmlspecialchars($album['titre']) ?>"
        </p>
    </div>

    <!-- Informations de l'album -->
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
        <div class="flex items-center space-x-6">
            <!-- Image de l'album -->
            <div class="w-32 h-32 bg-gradient-to-br from-primary-red to-primary-blue rounded-2xl overflow-hidden flex-shrink-0">
                <?php if (!empty($album['image_url'])): ?>
                    <img src="<?= htmlspecialchars($album['image_url']) ?>" 
                         alt="<?= htmlspecialchars($album['titre']) ?>" 
                         class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-compact-disc text-4xl text-white opacity-80"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Détails de l'album -->
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($album['titre']) ?></h2>
                <p class="text-gray-600 mb-4"><?= htmlspecialchars($album['description']) ?></p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Date de sortie :</span>
                        <div class="font-semibold"><?= date('d/m/Y', strtotime($album['date_sortie'])) ?></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Nombre de morceaux :</span>
                        <div class="font-semibold"><?= count($fichiers) ?></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Prix album :</span>
                        <div class="font-semibold text-primary-red"><?= $album['prix_album'] > 0 ? $album['prix_album'] . ' F CFA' : 'Gratuit' ?></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Statut :</span>
                        <div class="font-semibold text-green-600">
                            <i class="fas fa-crown mr-1"></i>Gratuit avec abonnement
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des fichiers à télécharger -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-download mr-3 text-primary-blue"></i>
            Fichiers disponibles (<?= count($fichiers) ?>)
        </h3>
        
        <div class="space-y-4">
            <?php foreach ($fichiers as $index => $fichier): ?>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-blue to-primary-yellow rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-<?= $fichier['type'] === 'audio' ? 'audio' : 'video' ?> text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($fichier['titre']) ?></h4>
                            <p class="text-sm text-gray-500">
                                <?= ucfirst($fichier['type']) ?> • <?= htmlspecialchars($fichier['duree']) ?>
                                <?php if ($fichier['prix'] > 0): ?>
                                    • <?= $fichier['prix'] ?> F CFA
                                <?php else: ?>
                                    • Gratuit
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <?php if ($is_paid): ?>
                            <?php
                            // Trouver le token correspondant à ce fichier
                            $downloadToken = null;
                            foreach ($_SESSION['download_tokens'] as $tokenData) {
                                if ($tokenData['fichier']['id'] == $fichier['id']) {
                                    $downloadToken = $tokenData['token'];
                                    break;
                                }
                            }
                            ?>
                            <?php if ($downloadToken): ?>
                                <a href="download_file.php?token=<?= $downloadToken ?>" 
                                   class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                                    <i class="fas fa-download mr-2"></i>Télécharger
                                </a>
                            <?php else: ?>
                                <span class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed">
                                    <i class="fas fa-lock mr-2"></i>Non disponible
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($fichier['url']) ?>" 
                               download="<?= htmlspecialchars($fichier['titre']) ?>"
                               class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-download mr-2"></i>Télécharger
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Bouton de téléchargement complet -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="text-center">
                <button onclick="downloadAllFiles()" 
                        class="bg-gradient-to-r from-primary-red to-primary-orange text-white px-8 py-4 rounded-xl font-semibold text-lg hover:from-red-700 hover:to-orange-700 transition-all transform hover:scale-105">
                    <i class="fas fa-download mr-3"></i>
                    Télécharger tous les fichiers (<?= count($fichiers) ?>)
                </button>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Les fichiers seront téléchargés un par un
                </p>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="mt-8 flex justify-center space-x-4">
        <a href="album.php?id=<?= $album_id ?>" 
           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour à l'album
        </a>
        <a href="albums.php" 
           class="bg-primary-blue text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fas fa-list mr-2"></i>Voir tous les albums
        </a>
    </div>
</div>

<script>
function downloadAllFiles() {
    const downloadLinks = document.querySelectorAll('a[download]');
    let currentIndex = 0;
    
    function downloadNext() {
        if (currentIndex < downloadLinks.length) {
            const link = downloadLinks[currentIndex];
            
            // Créer un élément temporaire pour déclencher le téléchargement
            const tempLink = document.createElement('a');
            tempLink.href = link.href;
            tempLink.download = link.download;
            tempLink.style.display = 'none';
            document.body.appendChild(tempLink);
            tempLink.click();
            document.body.removeChild(tempLink);
            
            currentIndex++;
            
            // Attendre un peu avant le prochain téléchargement
            setTimeout(downloadNext, 1000);
        }
    }
    
    // Démarrer les téléchargements
    downloadNext();
    
    // Afficher un message de confirmation
    alert('Téléchargement de ' + downloadLinks.length + ' fichiers en cours...');
}

// Animation au scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});
</script>

<?php require_once 'includes/footer.php'; ?> 