<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSCT Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Custom navbar color (teal gradient) */
        
       .navbar-custom {
    background: linear-gradient(90deg, #5e6269ff 0%, #454446ff 100%);
}
.navbar-custom .navbar-brand {
    color: #ffffff !important;
    font-weight: 600;
}
.navbar-custom .nav-link {
    color: rgba(255,255,255,0.95) !important;
    transition: color 0.15s ease, background-color 0.15s ease;
}
.navbar-custom .nav-link:hover {
    color: #fff3bf !important; /* soft yellow on hover */
    background-color: rgba(255,255,255,0.06);
    border-radius: 6px;
}
.navbar-custom .nav-link.active {
    background-color: rgba(255,255,255,0.12);
    color: #ffffff !important;
    border-radius: 6px;
}
.navbar-custom .navbar-toggler {
    border-color: rgba(255,255,255,0.12);
}
/* Make toggler icon visible on gradient */
.navbar-custom .navbar-toggler-icon {
    filter: invert(1) brightness(2) contrast(1.2);
}

    </style>
</head>
<body>
  <!-- Top Bar -->
  <div class="top-bar bg-primary text-white py-2">
      <div class="container">
          <div class="row">
              <div class="col-md-6">
                  <span><i class="fas fa-phone-alt me-2"></i> Helpline: 707-167-7676</span>
              </div>
              <div class="col-md-6 text-md-end">
                  <?php if (isset($_SESSION['user_id'])): ?>
                      <span class="me-3"><i class="fas fa-user me-1"></i> Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                      <a href="logout.php" class="text-white"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                  <?php else: ?>
                      <a href="login.php" class="text-white me-3"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                      <a href="register.php" class="text-white"><i class="fas fa-user-plus me-1"></i> Register</a>
                  <?php endif; ?>
              </div>
          </div>
      </div>
  </div>

    <!-- Header -->
<header class="py-3 bg-white shadow-sm">
        <div class="container">
        <div class="row align-items-center">
            
            <!-- Left Section -->
            <div class="col-12 col-lg-8 d-flex align-items-center justify-content-center justify-content-lg-start">
                <a href="index.php" class="d-flex align-items-center text-decoration-none text-reset">
                    <img src="assets/images/logo.png" alt="NSCT Logo" class="img-fluid site-logo">
                    <div class="ms-3">
                        <h1 class="h3 mb-0 fw-bold">नन्दवंशी सेल्फ केयर ट्रस्ट</h1>
                        <p class="mb-1 text-muted">अल्प अंशदान बनेगा वरदान </p>
                    </div>
                </a>
            </div>

            <!-- Right Section -->
            <div class="col-12 col-lg-4 text-center text-lg-end mt-3 mt-lg-0">
                <!-- Registration Info -->
                <div class="text-muted mb-2" style="font-size:0.95rem;">
                    <span class="fw-semibold">
                        <i class="fas fa-certificate me-1 text-primary"></i>Reg. No.: 4/014/2021
                    </span><br>
                    <span class="small text-muted">(मेजा, प्रयागराज)</span>
                </div>

                <!-- Search Bar -->
                <div class="input-group ms-auto" style="max-width: 260px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Search...">
                    <button class="btn btn-primary btn-sm" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</header>




    <!-- Navigation Menu -->
<nav class="navbar navbar-expand-lg navbar-custom">
      <div class="container">
          <button class="navbar-toggler rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="mainNav">
              <ul class="navbar-nav me-auto">
                  <li class="nav-item"><a href="index.php" class="nav-link ">Home</a></li>
                  <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
                  <li class="nav-item"><a href="niyamawali.php" class="nav-link">Niyamawali</a></li>
                  <li class="nav-item"><a href="sahyog_list.php" class="nav-link">Sahyog List</a></li>
                  <li class="nav-item"><a href="vyawastha_shulk.php" class="nav-link">Vyawastha Shulk</a></li>
                  <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
              </ul>
          </div>
      </div>
  </nav>
