<?php
require_once '../includes/functions.php';
initSession();
isAdminOrRedirect();
$pageTitle = 'Fichiers de l\'album';
require_once '../includes/admin_header.php';

$success = false;
$errors = [];

$album_id = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;
if (!$album_id) {
    echo '<div class="text-center text-red-600 py-12">Album non spécifié.</div>';
    require_once '../includes/admin_footer.php';
    exit;
}

// Suppression d'un fichier
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $fichier_id = (int)$_GET['delete'];
    try {
        $pdo = getDBConnection();
        $pdo->prepare('DELETE FROM fichiers WHERE id = ?')->execute([$fichier_id]);
        $success = true;
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupérer les infos de l'album
try {
    $pdo = getDBConnection();
    $album = $pdo->prepare('SELECT * FROM albums WHERE id = ?');
    $album->execute([$album_id]);
    $album = $album->fetch();
    if (!$album) {
        echo '<div class="text-center text-red-600 py-12">Album introuvable.</div>';
        require_once '../includes/admin_footer.php';
        exit;
    }
    $fichiers = $pdo->prepare('SELECT * FROM fichiers WHERE album_id = ? ORDER BY date_creation DESC');
    $fichiers->execute([$album_id]);
    $fichiers = $fichiers->fetchAll();
} catch (PDOException $e) {
    $fichiers = [];
    $errors[] = "Erreur lors de la récupération des fichiers.";
}
?>
<div class="max-w-4xl mx-auto py-12">
    <h1 class="text-2xl font-bold mb-6 text-primary-blue text-center">Fichiers de l'album : <?= htmlspecialchars($album['titre']) ?></h1>
    <div class="mb-6 text-center">
        <a href="ajouter_fichier.php" class="bg-primary-blue text-white px-4 py-2 rounded hover:bg-blue-700 font-semibold">Ajouter un fichier</a>
        <a href="liste_albums.php" class="ml-2 bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 font-semibold">Retour aux albums</a>
    </div>
    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">Fichier supprimé avec succès !</div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Titre</th>
                    <th class="px-4 py-2 border-b">Type</th>
                    <th class="px-4 py-2 border-b">Durée</th>
                    <th class="px-4 py-2 border-b">Prix</th>
                    <th class="px-4 py-2 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fichiers as $fichier): ?>
                    <tr>
                        <td class="px-4 py-2 border-b font-semibold text-gray-900"><?= htmlspecialchars($fichier['titre']) ?></td>
                        <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($fichier['type']) ?></td>
                        <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($fichier['duree']) ?></td>
                        <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($fichier['prix']) ?> F CFA</td>
                        <td class="px-4 py-2 border-b flex gap-2">
                            <a href="modifier_fichier.php?id=<?= $fichier['id'] ?>&album_id=<?= $album_id ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">Modifier</a>
                            <a href="?album_id=<?= $album_id ?>&delete=<?= $fichier['id'] ?>" onclick="return confirm('Supprimer ce fichier ? Cette action est irréversible.');" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($fichiers)): ?>
            <div class="text-center text-gray-500 py-8">Aucun fichier pour cet album.</div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>