<?php
require_once 'includes/functions.php';
initSession();

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil avec un message
setMessage('success', 'Vous avez été déconnecté avec succès.');
redirect('index.php');
?> 