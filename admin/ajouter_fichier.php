<?php
// Configuration pour augmenter les limites d'upload
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');
ini_set('max_input_time', 300);

require_once '../includes/functions.php';
initSession();
isAdminOrRedirect();
$pageTitle = 'Ajouter une musique/vidéo';
require_once '../includes/admin_header.php';

$success = false;
$errors = [];

// Récupérer la liste des albums pour le select
try {
    $pdo = getDBConnection();
    $albums = $pdo->query('SELECT id, titre FROM albums ORDER BY titre')->fetchAll();
} catch (PDOException $e) {
    $albums = [];
    $errors[] = "Erreur lors de la récupération des albums.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $album_id = (int)($_POST['album_id'] ?? 0);
    $titre = sanitize($_POST['titre'] ?? '');
    $type = $_POST['type'] ?? '';
    $duree = $_POST['duree'] ?? '';
    $prix = $_POST['prix'] ?? '5.00';
    $actif = isset($_POST['actif']) ? 1 : 0;

    // Validation
    if (!$album_id) {
        $errors[] = "Veuillez sélectionner un album.";
    }
    if (empty($titre)) {
        $errors[] = "Le titre du fichier est requis.";
    }
    if (!in_array($type, ['audio', 'video'])) {
        $errors[] = "Le type de fichier est invalide.";
    }
    if (empty($duree)) {
        $errors[] = "La durée est requise.";
    }

    // Gestion de l'upload de fichier
    $url = null;
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        
        // Vérifier le type de fichier
        $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
        $allowed_video = ['mp4', 'avi', 'mov', 'wmv'];
        
        if ($type === 'audio' && !in_array($ext, $allowed_audio)) {
            $errors[] = "Format audio non autorisé. Formats acceptés : " . implode(', ', $allowed_audio);
        } elseif ($type === 'video' && !in_array($ext, $allowed_video)) {
            $errors[] = "Format vidéo non autorisé. Formats acceptés : " . implode(', ', $allowed_video);
        } else {
            $uploadDir = '../assets/uploads/' . $type . 's/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = uniqid($type . '_', true) . '.' . $ext;
            $dest = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $dest)) {
                $url = 'assets/uploads/' . $type . 's/' . $filename;
            } else {
                $errors[] = "Erreur lors de l'upload du fichier.";
            }
        }
    } else {
        $errors[] = "Veuillez sélectionner un fichier.";
    }

    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            $sql = "INSERT INTO fichiers (album_id, titre, type, url, prix, duree, actif) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$album_id, $titre, $type, $url, $prix, $duree, $actif]);
            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}
?>
<div class="max-w-4xl mx-auto py-12 animate-fade-in">
    <!-- Header avec animation -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-6 bg-gradient-to-r from-primary-blue to-primary-yellow bg-clip-text text-transparent">
            Ajouter une musique/vidéo
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Uploadez un nouveau fichier audio ou vidéo et associez-le à un album existant
        </p>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-8 animate-slide-up">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                <span class="font-semibold">Fichier ajouté avec succès !</span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-8 animate-slide-up">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                <span class="font-semibold">Erreurs détectées :</span>
            </div>
            <ul class="list-disc list-inside ml-6">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulaire avec design moderne -->
    <div class="bg-white rounded-2xl shadow-xl p-8 animate-slide-up">
        <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Champs du formulaire -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-compact-disc mr-2 text-primary-red"></i>
                        Album de destination *
                    </label>
                    <select name="album_id" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20 transition-all duration-300" 
                            required>
                        <option value="">Sélectionnez un album</option>
                        <?php foreach ($albums as $album): ?>
                            <option value="<?= $album['id'] ?>" <?= (isset($_POST['album_id']) && $_POST['album_id'] == $album['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($album['titre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-music mr-2 text-primary-red"></i>
                        Titre du fichier *
                    </label>
                    <input type="text" name="titre" 
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20 transition-all duration-300" 
                           required 
                           value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                           placeholder="Titre du morceau ou de la vidéo">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-file-audio mr-2 text-primary-blue"></i>
                        Type de fichier *
                    </label>
                    <select name="type" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-blue focus:ring-2 focus:ring-primary-blue/20 transition-all duration-300"
                            required>
                        <option value="">Sélectionnez le type</option>
                        <option value="audio" <?= (isset($_POST['type']) && $_POST['type'] === 'audio') ? 'selected' : '' ?>>Audio</option>
                        <option value="video" <?= (isset($_POST['type']) && $_POST['type'] === 'video') ? 'selected' : '' ?>>Vidéo</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-clock mr-2 text-primary-orange"></i>
                        Durée (MM:SS) *
                    </label>
                    <input type="text" name="duree" placeholder="03:45" 
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-orange focus:ring-2 focus:ring-primary-orange/20 transition-all duration-300" 
                           required 
                           value="<?= htmlspecialchars($_POST['duree'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-tag mr-2 text-primary-yellow"></i>
                        Prix (F CFA)
                    </label>
                    <input type="number" name="prix" min="0" step="0.01" 
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-yellow focus:ring-2 focus:ring-primary-yellow/20 transition-all duration-300" 
                           value="<?= htmlspecialchars($_POST['prix'] ?? '5.00') ?>"
                           placeholder="5.00">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center space-x-3 text-gray-700 font-semibold text-lg">
                        <i class="fas fa-toggle-on mr-2 text-primary-green"></i>
                        <span>Statut du fichier</span>
                        <input type="checkbox" name="actif" value="1" 
                               class="w-6 h-6 rounded border-gray-300 text-primary-green focus:ring-primary-green"
                               <?= isset($_POST['actif']) ? 'checked' : 'checked' ?>>
                        <span class="text-sm text-gray-500">Actif (visible pour les utilisateurs)</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-2 ml-8">
                        <i class="fas fa-info-circle mr-1"></i>
                        Un fichier actif sera visible par tous les utilisateurs. Un fichier inactif sera caché mais pourra être réactivé plus tard.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-upload mr-2 text-primary-red"></i>
                        Fichier à uploader *
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary-red transition-colors duration-300 relative">
                        <input type="file" name="fichier" 
                               class="w-full opacity-0 absolute inset-0 cursor-pointer" 
                               id="fileInput"
                               required>
                        <div class="space-y-4">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                            <div>
                                <p class="text-lg font-medium text-gray-700">Cliquez pour sélectionner un fichier</p>
                                <p class="text-sm text-gray-500" id="fileTypes">Sélectionnez d'abord le type de fichier</p>
                            </div>
                        </div>
                    </div>
                    <div id="filePreview" class="mt-4 hidden">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-file-audio text-2xl text-primary-blue mr-3" id="fileIcon"></i>
                                <div>
                                    <p class="font-semibold" id="fileName"></p>
                                    <p class="text-sm text-gray-500" id="fileSize"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-primary-blue to-primary-yellow text-white px-8 py-4 rounded-xl font-semibold text-lg hover:from-blue-700 hover:to-yellow-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                    <i class="fas fa-plus mr-3"></i>
                    Ajouter le fichier
                </button>
                <a href="index.php" 
                   class="flex-1 bg-gray-100 text-gray-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-200 transform hover:scale-105 transition-all duration-300 text-center">
                    <i class="fas fa-arrow-left mr-3"></i>
                    Retour au tableau de bord
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Mise à jour des types de fichiers selon le type sélectionné
document.querySelector('select[name="type"]').addEventListener('change', function() {
    const fileTypes = document.getElementById('fileTypes');
    const fileInput = document.getElementById('fileInput');
    
    if (this.value === 'audio') {
        fileTypes.textContent = 'MP3, WAV, OGG, M4A pour audio';
        fileInput.accept = '.mp3,.wav,.ogg,.m4a';
    } else if (this.value === 'video') {
        fileTypes.textContent = 'MP4, AVI, MOV, WMV pour vidéo';
        fileInput.accept = '.mp4,.avi,.mov,.wmv';
    } else {
        fileTypes.textContent = 'Sélectionnez d\'abord le type de fichier';
        fileInput.accept = '';
    }
});

// Prévisualisation du fichier
document.getElementById('fileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileIcon = document.getElementById('fileIcon');
    
    if (file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        
        // Définir l'icône selon le type
        const type = document.querySelector('select[name="type"]').value;
        if (type === 'audio') {
            fileIcon.className = 'fas fa-file-audio text-2xl text-primary-blue mr-3';
        } else if (type === 'video') {
            fileIcon.className = 'fas fa-file-video text-2xl text-primary-purple mr-3';
        } else {
            fileIcon.className = 'fas fa-file text-2xl text-gray-500 mr-3';
        }
        
        preview.classList.remove('hidden');
        preview.classList.add('animate-fade-in');
    } else {
        preview.classList.add('hidden');
    }
});

// Fonction pour formater la taille du fichier
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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

<?php require_once '../includes/admin_footer.php'; ?>