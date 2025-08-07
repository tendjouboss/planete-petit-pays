<?php
require_once '../includes/functions.php';
initSession();
isAdminOrRedirect();
$pageTitle = 'Gérer les albums';
require_once '../includes/admin_header.php';

$success = false;
$errors = [];

// Suppression d'un album
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $album_id = (int)$_GET['delete'];
    try {
        $pdo = getDBConnection();
        // Supprimer les fichiers associés
        $pdo->prepare('DELETE FROM fichiers WHERE album_id = ?')->execute([$album_id]);
        // Supprimer l'album
        $pdo->prepare('DELETE FROM albums WHERE id = ?')->execute([$album_id]);
        $success = true;
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupérer la liste des albums et le nombre de fichiers associés
try {
    $pdo = getDBConnection();
    $albums = $pdo->query('SELECT a.*, (SELECT COUNT(*) FROM fichiers f WHERE f.album_id = a.id) AS nb_fichiers FROM albums a ORDER BY date_sortie DESC')->fetchAll();
} catch (PDOException $e) {
    $albums = [];
    $errors[] = "Erreur lors de la récupération des albums.";
}
?>
<div class="max-w-5xl mx-auto py-12">
    <h1 class="text-2xl font-bold mb-6 text-primary-yellow text-center">Gestion des albums</h1>
    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">Album supprimé avec succès !</div>
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
                    <th class="px-4 py-2 border-b">Date de sortie</th>
                    <th class="px-4 py-2 border-b">Fichiers</th>
                    <th class="px-4 py-2 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($albums as $album): ?>
                    <tr>
                        <td class="px-4 py-2 border-b font-semibold text-gray-900"><?= htmlspecialchars($album['titre']) ?></td>
                        <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($album['date_sortie']) ?></td>
                        <td class="px-4 py-2 border-b text-center"><?= $album['nb_fichiers'] ?></td>
                        <td class="px-4 py-2 border-b flex gap-2">
                            <a href="modifier_album.php?id=<?= $album['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">Modifier</a>
                            <a href="voir_fichiers.php?album_id=<?= $album['id'] ?>" class="bg-primary-blue text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">Fichiers</a>
                            <a href="?delete=<?= $album['id'] ?>" onclick="return confirm('Supprimer cet album ? Cette action est irréversible.');" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>