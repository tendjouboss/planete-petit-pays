    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo et description -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 gradient-bg rounded-full flex items-center justify-center">
                            <i class="fas fa-music text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold">Planète Petit Pays</span>
                    </div>
                    <p class="text-gray-300 mb-4">
                        Découvrez la musique unique de notre artiste. Téléchargez vos morceaux préférés 
                        ou abonnez-vous pour un accès illimité à tout le catalogue.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Liens rapides -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Liens rapides</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Accueil</a></li>
                        <li><a href="albums.php" class="text-gray-300 hover:text-white transition-colors">Albums</a></li>
                        <li><a href="login.php" class="text-gray-300 hover:text-white transition-colors">Connexion</a></li>
                        <li><a href="register.php" class="text-gray-300 hover:text-white transition-colors">Inscription</a></li>
                    </ul>
                </div>
                
                <!-- Informations -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Informations</h3>
                    <ul class="space-y-2">
                        <li class="text-gray-300">Téléchargement : 5 F CFA</li>
                        <li class="text-gray-300">Abonnement : 500 F CFA/mois</li>
                        <li class="text-gray-300">Support : support@planete-petit-pays.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-300">
                    &copy; <?php echo date('Y'); ?> Planète Petit Pays. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Menu mobile toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Fermer le menu mobile en cliquant sur un lien
            const mobileLinks = document.querySelectorAll('.mobile-menu a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                });
            });
        });
        
        // Animation des cartes au survol
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.hover-scale');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
        
        // Confirmation de paiement
        function confirmPayment(type, amount, itemId) {
            if (confirm(`Confirmer le paiement de ${amount} F CFA pour ${type === 'abo' ? 'l\'abonnement' : 'ce fichier'} ?`)) {
                // Rediriger vers la page de paiement
                window.location.href = `payment.php?type=${type}&amount=${amount}&item=${itemId}`;
            }
        }
        
        // Prévisualisation audio/vidéo
        function previewMedia(url, type) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Aperçu</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="aspect-video bg-gray-100 rounded">
                        ${type === 'audio' 
                            ? `<audio controls class="w-full h-full"><source src="${url}" type="audio/mpeg"></audio>`
                            : `<video controls class="w-full h-full"><source src="${url}" type="video/mp4"></video>`
                        }
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Fermer avec Escape
            modal.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    modal.remove();
                }
            });
            
            // Fermer en cliquant à l'extérieur
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }
    </script>
</body>
</html> 