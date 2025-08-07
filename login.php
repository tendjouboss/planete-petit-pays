<?php
require_once 'includes/functions.php';
initSession();

if (isLoggedIn()) {
    redirect('index.php');
}

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    // Validation
    if (empty($email)) {
        $errors[] = "L'email est requis";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    }
    
    // Authentification
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, nom, email, mot_de_passe, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                setMessage('success', 'Connexion réussie ! Bienvenue ' . $user['nom']);
                redirect('index.php');
            } else {
                $errors[] = "Email ou mot de passe incorrect";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la connexion";
        }
    }
}

$pageTitle = "Connexion";
require_once 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 gradient-bg rounded-full flex items-center justify-center">
                <i class="fas fa-sign-in-alt text-white text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Se connecter
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ou
                <a href="register.php" class="font-medium text-primary-red hover:text-red-700">
                    créez un nouveau compte
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
                    <label for="email" class="sr-only">Adresse email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary-red focus:border-primary-red focus:z-10 sm:text-sm" 
                           placeholder="Adresse email"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary-red focus:border-primary-red focus:z-10 sm:text-sm" 
                           placeholder="Mot de passe">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox" 
                           class="h-4 w-4 text-primary-red focus:ring-primary-red border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Se souvenir de moi
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-primary-red hover:text-red-700">
                        Mot de passe oublié ?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-red">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-red-300 group-hover:text-red-200"></i>
                    </span>
                    Se connecter
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Compte de test : admin@planete-petit-pays.com / password
                </p>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 