<?php
require_once '../includes/functions.php';
initSession();
isAdminOrRedirect();
$pageTitle = 'Espace Administration';
require_once '../includes/admin_header.php';
?>
<div class="max-w-4xl mx-auto py-12">
    <h1 class="text-3xl font-bold mb-8 text-center text-primary-red">Espace Administration</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <a href="ajouter_album.php" class="bg-primary-red text-white rounded-lg p-8 flex flex-col items-center shadow hover:scale-105 transition">
            <i class="fas fa-plus fa-2x mb-4"></i>
            <span class="font-semibold">Créer un album</span>
        </a>
        <a href="ajouter_fichier.php" class="bg-primary-blue text-white rounded-lg p-8 flex flex-col items-center shadow hover:scale-105 transition">
            <i class="fas fa-music fa-2x mb-4"></i>
            <span class="font-semibold">Ajouter une musique/vidéo</span>
        </a>
        <a href="liste_albums.php" class="bg-primary-yellow text-gray-900 rounded-lg p-8 flex flex-col items-center shadow hover:scale-105 transition">
            <i class="fas fa-list fa-2x mb-4"></i>
            <span class="font-semibold">Gérer les albums</span>
        </a>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>