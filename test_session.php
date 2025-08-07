<?php
require_once 'includes/header.php';
?>
<div class="max-w-xl mx-auto py-12">
    <h1 class="text-2xl font-bold mb-6 text-primary-red text-center">Test de la session PHP</h1>
    <div class="bg-gray-100 p-4 rounded mb-4">
        <h2 class="font-semibold mb-2">Contenu de 24_SESSION :</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
    <div class="bg-gray-100 p-4 rounded mb-4">
        <h2 class="font-semibold mb-2">Statut de connexion :</h2>
        <ul>
            <li><strong>isLoggedIn() :</strong> <?php echo isLoggedIn() ? 'OUI' : 'NON'; ?></li>
            <li><strong>isAdmin() :</strong> <?php echo isAdmin() ? 'OUI' : 'NON'; ?></li>
        </ul>
    </div>
    <div class="text-center mt-6">
        <a href="logout.php" class="bg-primary-red text-white px-4 py-2 rounded hover:bg-red-700 font-semibold">Déconnexion</a>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>