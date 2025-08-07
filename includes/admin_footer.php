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
                        Découvrez la musique unique de notre artiste. Des mélodies qui transportent 
                        et des rythmes qui font vibrer votre âme.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Liens rapides -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Liens rapides</h3>
                    <ul class="space-y-2">
                        <li><a href="../index.php" class="text-gray-300 hover:text-white transition-colors">Accueil</a></li>
                        <li><a href="../albums.php" class="text-gray-300 hover:text-white transition-colors">Albums</a></li>
                        <li><a href="../profile.php" class="text-gray-300 hover:text-white transition-colors">Mon Profil</a></li>
                        <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Administration</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><i class="fas fa-envelope mr-2"></i>contact@planete-petit-pays.com</li>
                        <li><i class="fas fa-phone mr-2"></i>+237 XX XX XX XX</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i>Douala, Cameroun</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Planète Petit Pays. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.querySelector('.mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html> 