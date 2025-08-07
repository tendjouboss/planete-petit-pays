<?php
require_once 'includes/functions.php';

$pageTitle = "Inscription";

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Validation
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé";
        }
    }
    
    // Créer le compte si pas d'erreurs
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $email, $hashedPassword]);
            
            setMessage('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
            redirect('login.php');
            
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la création du compte";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 gradient-bg rounded-full flex items-center justify-center">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Créer un compte
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ou
                <a href="login.php" class="font-medium text-primary-red hover:text-red-700">
                    connectez-vous à votre compte existant
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="nom" class="sr-only">Nom complet</label>
                    <input id="nom" name="nom" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary-red focus:border-primary-red focus:z-10 sm:text-sm" 
                           placeholder="Nom complet"
                           value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                </div>
                <div>
                    <label for="email" class="sr-only">Adresse email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-red focus:border-primary-red focus:z-10 sm:text-sm" 
                           placeholder="Adresse email"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-red focus:border-primary-red focus:z-10 sm:text-sm" 
                           placeholder="Mot de passe">
                </div>
                <div>
                    <label for="confirm_password" class="sr-only">Confirmer le mot de passe</label>
                    <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary-red focus:border-primary-red focus:z-10 sm:text-sm" 
                           placeholder="Confirmer le mot de passe">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-red">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-red-300 group-hover:text-red-200"></i>
                    </span>
                    Créer mon compte
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    En créant un compte, vous acceptez nos 
                    <a href="#" class="font-medium text-primary-red hover:text-red-700">conditions d'utilisation</a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 