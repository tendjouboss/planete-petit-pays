<?php
require_once 'includes/functions.php';
initSession();

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    setMessage('error', 'Vous devez être connecté pour effectuer un paiement');
    redirect('login.php');
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('albums.php');
}

$album_id = isset($_POST['album_id']) ? (int)$_POST['album_id'] : 0;
$file_id = isset($_POST['file_id']) ? (int)$_POST['file_id'] : 0;
$subscription = isset($_POST['subscription']) ? (bool)$_POST['subscription'] : false;
$total_amount = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0;
$type = $_POST['type'] ?? 'download';
$full_name = sanitize($_POST['full_name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$payment_method = $_POST['payment_method'] ?? 'mobile_money';
$terms = isset($_POST['terms']);

$errors = [];

// Validation
if (!$album_id && !$file_id && !$subscription) {
    $errors[] = "Aucun contenu sélectionné";
}

if ($total_amount <= 0) {
    $errors[] = "Montant invalide";
}

if (empty($full_name)) {
    $errors[] = "Le nom complet est requis";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide";
}

if (empty($phone)) {
    $errors[] = "Le numéro de téléphone est requis";
}

if (!in_array($payment_method, ['orange_money', 'mtn_money'])) {
    $errors[] = "Méthode de paiement invalide";
}

if (!$terms) {
    $errors[] = "Vous devez accepter les conditions d'utilisation";
}

// Récupérer les informations de l'album ou du fichier
try {
    $pdo = getDBConnection();
    
    if ($album_id) {
        // Paiement pour un album complet
        $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND actif = 1");
        $stmt->execute([$album_id]);
        $album = $stmt->fetch();
        
        if (!$album) {
            $errors[] = "Album non trouvé ou non disponible";
        }
        
        // Récupérer les fichiers de l'album
        $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE album_id = ? AND actif = 1");
        $stmt->execute([$album_id]);
        $fichiers = $stmt->fetchAll();
        
        if (empty($fichiers)) {
            $errors[] = "Aucun fichier disponible pour cet album";
        }
        
    } else {
        // Paiement pour un fichier individuel
        $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE id = ? AND actif = 1");
        $stmt->execute([$file_id]);
        $fichier = $stmt->fetch();
        
        if (!$fichier) {
            $errors[] = "Fichier non trouvé ou non disponible";
        } else {
            $fichiers = [$fichier];
        }
    }
    
} catch (PDOException $e) {
    $errors[] = "Erreur lors de la récupération des données";
}

// Si pas d'erreurs, traiter le paiement
if (empty($errors)) {
    try {
        // Simuler le paiement Mobile Money selon l'opérateur choisi
        if ($payment_method === 'orange_money') {
            $paymentResult = simulateOrangeMoneyPayment($total_amount, $phone);
        } else {
            $paymentResult = simulateMTNMoneyPayment($total_amount, $phone);
        }
        
        if ($paymentResult['success']) {
            // Enregistrer la transaction
            $transactionId = recordTransaction($_SESSION['user_id'], $type, $total_amount);
            
            if ($subscription) {
                // Activer l'abonnement
                updateSubscriptionStatus($_SESSION['user_id'], true);
                setMessage('success', 'Abonnement activé avec succès ! Vous avez maintenant accès à tous les téléchargements.');
                redirect('profile.php');
            } else {
                // Enregistrer les achats pour chaque fichier
                foreach ($fichiers as $fichier) {
                    recordPurchase($_SESSION['user_id'], $fichier['id'], $transactionId);
                }
                
                // Créer les tokens de téléchargement
                $downloadTokens = [];
                foreach ($fichiers as $fichier) {
                    $token = createDownloadToken($_SESSION['user_id'], $fichier['id']);
                    $downloadTokens[] = [
                        'fichier' => $fichier,
                        'token' => $token
                    ];
                }
                
                // Stocker les tokens en session pour la page de téléchargement
                $_SESSION['download_tokens'] = $downloadTokens;
                
                if ($album_id) {
                    $_SESSION['paid_album_id'] = $album_id;
                    setMessage('success', 'Paiement effectué avec succès ! Vous pouvez maintenant télécharger votre album.');
                    redirect('download_album.php?id=' . $album_id . '&paid=1');
                } else {
                    $_SESSION['paid_file_id'] = $file_id;
                    setMessage('success', 'Paiement effectué avec succès ! Vous pouvez maintenant télécharger votre fichier.');
                    redirect('download_file.php?token=' . $downloadTokens[0]['token']);
                }
            }
            
        } else {
            $errors[] = $paymentResult['message'];
        }
        
    } catch (Exception $e) {
        $errors[] = "Erreur lors du traitement du paiement : " . $e->getMessage();
    }
}

// Si il y a des erreurs, rediriger vers la page de paiement avec les erreurs
if (!empty($errors)) {
    $_SESSION['payment_errors'] = $errors;
    $_SESSION['payment_data'] = [
        'album_id' => $album_id,
        'file_id' => $file_id,
        'subscription' => $subscription,
        'total_amount' => $total_amount,
        'type' => $type,
        'full_name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'payment_method' => $payment_method
    ];
    
    if ($subscription) {
        redirect('payment.php?type=subscription&amount=' . $total_amount);
    } elseif ($album_id) {
        redirect('payment.php?album_id=' . $album_id . '&type=' . $type);
    } else {
        redirect('payment.php?file_id=' . $file_id . '&type=' . $type);
    }
}

/**
 * Simule un paiement Orange Money
 */
function simulateOrangeMoneyPayment($amount, $phone) {
    // Simulation d'un paiement Orange Money
    // En production, intégrer l'API Orange Money
    
    // Simuler un délai de traitement
    usleep(500000); // 0.5 seconde
    
    // Simuler un taux de succès de 95%
    $success = (rand(1, 100) <= 95);
    
    if ($success) {
        return [
            'success' => true,
            'transaction_id' => 'ORANGE_' . time() . '_' . rand(1000, 9999),
            'message' => 'Paiement Orange Money effectué avec succès'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Paiement Orange Money échoué. Veuillez vérifier votre solde et réessayer.'
        ];
    }
}

/**
 * Simule un paiement MTN Mobile Money
 */
function simulateMTNMoneyPayment($amount, $phone) {
    // Simulation d'un paiement MTN Mobile Money
    // En production, intégrer l'API MTN Mobile Money
    
    // Simuler un délai de traitement
    usleep(500000); // 0.5 seconde
    
    // Simuler un taux de succès de 95%
    $success = (rand(1, 100) <= 95);
    
    if ($success) {
        return [
            'success' => true,
            'transaction_id' => 'MTN_' . time() . '_' . rand(1000, 9999),
            'message' => 'Paiement MTN Mobile Money effectué avec succès'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Paiement MTN Mobile Money échoué. Veuillez vérifier votre solde et réessayer.'
        ];
    }
}
?> 