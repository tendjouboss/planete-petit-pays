<?php
require_once '../includes/functions.php';
initSession();
isAdminOrRedirect();
$pageTitle = 'Créer un album';
require_once '../includes/admin_header.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = sanitize($_POST['titre'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $date_sortie = $_POST['date_sortie'] ?? '';
    $prix_album = $_POST['prix_album'] ?? '0.00';
    $actif = isset($_POST['actif']) ? 1 : 0;
    $image_url = null;

    // Validation
    if (empty($titre)) {
        $errors[] = "Le titre de l'album est requis.";
    }
    if (empty($date_sortie)) {
        $errors[] = "La date de sortie est requise.";
    }
    // Gestion de l'upload d'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format d'image non autorisé.";
        } else {
            $uploadDir = '../assets/uploads/albums/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = uniqid('album_', true) . '.' . $ext;
            $dest = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $image_url = 'assets/uploads/albums/' . $filename;
            } else {
                $errors[] = "Erreur lors de l'upload de l'image.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            $sql = "INSERT INTO albums (titre, description, date_sortie, image_url, prix_album, actif) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titre, $description, $date_sortie, $image_url, $prix_album, $actif]);
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
        <h1 class="text-4xl font-bold mb-6 bg-gradient-to-r from-primary-red to-primary-orange bg-clip-text text-transparent">
            Créer un nouvel album
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Ajoutez un nouvel album à votre collection avec toutes les informations nécessaires
        </p>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-8 animate-slide-up">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                <span class="font-semibold">Album créé avec succès !</span>
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
                        <i class="fas fa-music mr-2 text-primary-red"></i>
                        Titre de l'album *
                    </label>
                    <input type="text" name="titre" 
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20 transition-all duration-300" 
                           required 
                           value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                           placeholder="Titre de votre album">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-align-left mr-2 text-primary-orange"></i>
                        Description
                    </label>
                    <textarea name="description" rows="4" 
                              class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-orange focus:ring-2 focus:ring-primary-orange/20 transition-all duration-300 resize-none"
                              placeholder="Description détaillée de l'album..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-calendar mr-2 text-primary-yellow"></i>
                        Date de sortie *
                    </label>
                    <input type="date" name="date_sortie" 
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-yellow focus:ring-2 focus:ring-primary-yellow/20 transition-all duration-300" 
                           required 
                           value="<?= htmlspecialchars($_POST['date_sortie'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-tag mr-2 text-primary-blue"></i>
                        Prix de l'album (F CFA)
                    </label>
                    <input type="number" name="prix_album" min="0" step="0.01" 
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-blue focus:ring-2 focus:ring-primary-blue/20 transition-all duration-300" 
                           value="<?= htmlspecialchars($_POST['prix_album'] ?? '0.00') ?>"
                           placeholder="0.00">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center space-x-3 text-gray-700 font-semibold text-lg">
                        <i class="fas fa-toggle-on mr-2 text-primary-green"></i>
                        <span>Statut de l'album</span>
                        <input type="checkbox" name="actif" value="1" 
                               class="w-6 h-6 rounded border-gray-300 text-primary-green focus:ring-primary-green"
                               <?= isset($_POST['actif']) ? 'checked' : 'checked' ?>>
                        <span class="text-sm text-gray-500">Actif (visible pour les utilisateurs)</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-2 ml-8">
                        <i class="fas fa-info-circle mr-1"></i>
                        Un album actif sera visible par tous les utilisateurs. Un album inactif sera caché mais pourra être réactivé plus tard.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-3 text-lg">
                        <i class="fas fa-image mr-2 text-primary-red"></i>
                        Image de couverture (optionnel)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary-red transition-colors duration-300 relative">
                        <input type="file" name="image" accept="image/*" 
                               class="w-full opacity-0 absolute inset-0 cursor-pointer" 
                               id="imageInput">
                        <div class="space-y-4">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                            <div>
                                <p class="text-lg font-medium text-gray-700">Cliquez pour sélectionner une image</p>
                                <p class="text-sm text-gray-500">JPG, PNG, GIF jusqu'à 5MB</p>
                            </div>
                        </div>
                    </div>
                    <div id="imagePreview" class="mt-4 hidden">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <img id="previewImg" class="w-16 h-16 object-cover rounded-lg mr-4" alt="Aperçu">
                                <div>
                                    <p class="font-semibold" id="imageName"></p>
                                    <p class="text-sm text-gray-500" id="imageSize"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-primary-red to-primary-orange text-white px-8 py-4 rounded-xl font-semibold text-lg hover:from-red-700 hover:to-orange-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                    <i class="fas fa-plus mr-3"></i>
                    Créer l'album
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
// Prévisualisation de l'image
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const imageName = document.getElementById('imageName');
    const imageSize = document.getElementById('imageSize');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            imageName.textContent = file.name;
            imageSize.textContent = formatFileSize(file.size);
            preview.classList.remove('hidden');
            preview.classList.add('animate-fade-in');
        }
        reader.readAsDataURL(file);
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