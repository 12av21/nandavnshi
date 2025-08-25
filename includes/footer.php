    </div> <!-- Close container from header -->
    
      <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="assets/images/logo.png" alt="NSCT Logo" class="me-3" style="height: 50px; width: 50px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <h5 class="mb-1">Nandvanshi Self Care Trust</h5>
                            <small class="text-light">Today's Support, Tomorrow's Strength</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-light text-decoration-none"><i class="fas fa-home me-2"></i>Home</a></li>
                        <li class="mb-2"><a href="about.php" class="text-light text-decoration-none"><i class="fas fa-info-circle me-2"></i> About Us</a></li>
                        <li class="mb-2"><a href="membership.php" class="text-light text-decoration-none"><i class="fas fa-users me-2"></i>Membership</a></li>
                        <li class="mb-2"><a href="support.php" class="text-light text-decoration-none"><i class="fas fa-hands-helping me-2"></i>Support</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-light text-decoration-none"><i class="fas fa-phone me-2"></i>Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Contact Information</h6>
                    <div class="mb-3">
                        <p class="text-light small mb-2">
                            <a href="https://www.google.com/maps/search/?api=1&query=Village+Mishrpur+Post+Dubepur+Tehsil+Meja+Prayagraj+Uttar+Pradesh+212305" target="_blank" rel="noopener noreferrer" class="text-light text-decoration-none">
                                <i class="fas fa-map-marker-alt me-2 text-white"></i>
                                Village - Mishrapur, Post - Dubepur<br>
                                <span class="ms-4">Tehsil - Meja, District - Prayagraj</span><br>
                                <span class="ms-4">Uttar Pradesh - 212305</span>
                            </a>
                        </p>
                        <p class="text-light small mb-2">
                            <i class="fas fa-phone me-2 text-white"></i>
                            <a href="tel:+917071677676" class="text-light text-decoration-none">+91-707-167-7676</a>
                        </p>
                        <p class="text-light small mb-0">
                            <i class="fas fa-envelope me-2 text-white"></i>
                            <a href="mailto:call_me@nandvanshi.org" class="text-light text-decoration-none">call_me@nandvanshi.org</a>
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-youtube"></i></a>
                    </div>
                    <div class="mt-3">
                        <h6>Download App</h6>
                        <div class="d-flex">
                                <a href="#" class="me-2">
                                    <img src="assets/images/app-store.svg" alt="App Store" class="img-fluid" style="max-height: 40px;">
                                </a>
                                <a href="#">
                                    <img src="assets/images/play-store.svg" alt="Play Store" class="img-fluid" style="max-height: 40px;">
                                </a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-0 mb-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2025 Nandvanshi Self Care Team . All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="privacy.php" class="text-white-50 me-3">Privacy Policy</a>
                    <a href="terms.php" class="text-white-50 me-3">Terms of Use</a>
                    <a href="sitemap.php" class="text-white-50">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>


    <!-- Back to Top Button -->
    <a href="#" class="back-to-top"><i class="fas fa-arrow-up"></i></a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Enable dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Activate current nav link
    $(document).ready(function() {
        var url = window.location.pathname;
        var filename = url.substring(url.lastIndexOf('/')+1);
        $('.navbar-nav a[href$="' + filename + '"]').addClass('active');
    });
    </script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>
