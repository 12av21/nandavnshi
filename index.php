<?php
session_start();
require_once 'config/database.php';
// Fetch Sahyog statistics and live case
try {
    // Using COALESCE to handle NULL results from SUM() when no records are found
    $live_sahyog_val = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM contributions WHERE contribution_type = 'Sahyog' AND status = 'Pending'")->fetchColumn();
    $complete_sahyog_val = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM contributions WHERE contribution_type = 'Sahyog' AND status = 'Completed'")->fetchColumn();
    
    // Total Sahyog is the sum of completed and pending (live) sahyog amounts
    $total_sahyog_val = $live_sahyog_val + $complete_sahyog_val;

    // Format numbers for display
    $complete_sahyog = number_format($complete_sahyog_val);
    $total_sahyog = number_format($total_sahyog_val);

    // Fetch details for the latest "Live Sahyog" case
    $live_sahyog_case_stmt = $pdo->query("
        SELECT
            c.id as contribution_id,
            c.amount as sahyog_amount,
            c.notes as sahyog_reason,
            m.name as member_name,
            m.father_husband_name,
            m.district,
            m.state
        FROM
            contributions c
        JOIN
            members m ON c.member_id = m.id
        WHERE
            c.contribution_type = 'Sahyog' AND c.status = 'Pending'
        ORDER BY
            c.payment_date DESC, c.id DESC
        LIMIT 1
    ");
    $live_sahyog_case = $live_sahyog_case_stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Fallback to placeholders on DB error
    error_log("Homepage Stats Error: " . $e->getMessage());
    $complete_sahyog = "12,75,000";
    $total_sahyog = "14,25,000";
    $live_sahyog_case = null;
}
?>
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
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Slider Section -->
    <section class="slider mb-4">
        <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/images/slide.png" class="d-block w-100" alt="Slide 1">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/slide1.png" class="d-block w-100" alt="Slide 2">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/slide2.png" class="d-block w-100" alt="Slide 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <!-- Alert Banner -->
    <div class="alert-banner">
        <div class="alert-text">
            🚨 महत्वपूर्ण सूचना: सभी सदस्यों से अनुरोध है कि वे अपनी सदस्यता की जानकारी अपडेट करें। किसी भी सहायता के लिए हमसे संपर्क करें। 📞 हेल्पलाइन: 707-167-7676
        </div>
    </div>

    <!-- Stats Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-left-primary shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="row no-gutters align-items-center mb-2">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">लाइव सहयोग (Live Sahyog)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php if ($live_sahyog_case): ?>
                                            <?php echo htmlspecialchars($live_sahyog_case['member_name']); ?>
                                        <?php else: ?>
                                            No Active Sahyog
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hand-holding-heart fa-3x text-gray-300"></i>
                                </div>
                            </div>
                            
                            <?php if ($live_sahyog_case): ?>
                                <div class="casualty-details mt-auto">
                                    <p class="mb-1 small text-muted">
                                        <strong>S/o:</strong> <?php echo htmlspecialchars($live_sahyog_case['father_husband_name']); ?>
                                    </p>
                                    <p class="mb-1 small text-muted">
                                        <strong>Location:</strong> <?php echo htmlspecialchars($live_sahyog_case['district'] . ', ' . $live_sahyog_case['state']); ?>
                                    </p>
                                    <p class="mb-2 small text-muted">
                                        <strong>Reason:</strong> <?php echo htmlspecialchars($live_sahyog_case['sahyog_reason'] ?? 'Medical Emergency'); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="fw-bold text-primary h5 mb-0">₹<?php echo number_format($live_sahyog_case['sahyog_amount']); ?></span>
                                        <a href="donate.php?case=<?php echo $live_sahyog_case['contribution_id']; ?>" class="btn btn-primary btn-sm">सहयोग करें</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mt-auto">
                                    <p class="text-muted small mb-0">
                                        Currently, there are no active sahyog campaigns. All support goals have been met. Thank you for your generosity!
                                    </p>
                                </div> 
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-left-success shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">पूर्ण सहयोग (Complete Sahyog)</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">₹ <?php echo $complete_sahyog; ?></div>
                                    <div class="text-muted small mt-1">Support Provided Till Date</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card border-left-info shadow-sm h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">कुल सहयोग (Total Sahyog)</div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">₹ <?php echo $total_sahyog; ?></div>
                                    <a href="sahyog_list.php" class="btn btn-info btn-sm text-white mt-2"><i class="fas fa-list-ul me-1"></i> पूरी सूची देखें</a>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-donate fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold text-primary mb-3">Quick Links</h2>
                <div class="divider mx-auto bg-primary" style="height: 3px; width: 80px;"></div>
                <p class="lead text-muted mt-3">Quick access to important sections</p>
            </div>
            
            <div class="row g-4">
                <!-- Column 1: About Us -->
                <div class="col-md-3 col-6">
                    <a href="about.php" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm hover-effect hover-bg">
                            <div class="card-body text-center p-4">
                                <div class="icon-box mb-3">
                                    <i class="fas fa-info-circle fa-2x text-primary"></i>
                                </div>
                                <h5 class="card-title text-primary mb-3">About Us</h5>
                                <p class="text-muted small">Learn about our mission, vision, and values</p>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="mission.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                        <i class="fas fa-bullseye text-primary me-2"></i>Our Mission
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="team.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                        <i class="fas fa-users text-primary me-2"></i>Our Team
                                    </a>
                                </li>
                                <li class="mb-0">
                                    <a href="guidelines.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                        <i class="fas fa-book text-primary me-2"></i>Guidelines
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Column 2: Membership -->
                <div class="col-md-3 col-6">
                    <a href="become-member.php" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm hover-effect hover-bg">
                            <div class="card-body text-center p-4">
                                <div class="icon-box mb-3">
                                    <i class="fas fa-user-plus fa-2x text-primary"></i>
                                </div>
                                <h5 class="card-title text-primary mb-3">Membership</h5>
                                <p class="text-muted small">Join our community and enjoy exclusive benefits</p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <a href="become-member.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                            <i class="fas fa-user-edit text-primary me-2"></i>Become a Member
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="member-list.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                            <i class="fas fa-list-ul text-primary me-2"></i>Member Directory
                                        </a>
                                    </li>
                                    <li class="mb-0">
                                        <a href="benefits.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                            <i class="fas fa-award text-primary me-2"></i>Benefits
                                        </a>
                                    </li>
                                </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Column 3: Resources -->
                <div class="col-md-3 col-6">
                    <a href="downloads.php" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm hover-effect hover-bg">
                            <div class="card-body text-center p-4">
                                <div class="icon-box mb-3">
                                    <i class="fas fa-folder-open fa-2x text-primary"></i>
                                </div>
                                <h5 class="card-title text-primary mb-3">Resources</h5>
                                <p class="text-muted small">Useful documents and forms</p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <a href="downloads.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                            <i class="fas fa-file-alt text-primary me-2"></i>Documents
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="downloads.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                            <i class="fas fa-file-signature text-primary me-2"></i>Forms
                                        </a>
                                    </li>
                                    <li class="mb-0">
                                        <a href="gallery.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                            <i class="fas fa-images text-primary me-2"></i>Gallery
                                        </a>
                                    </li>
                                </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Column 4: Contact -->
                <div class="col-md-3 col-6">
                    <a href="contact.php" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm hover-effect hover-bg">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mb-3">
                                <i class="fas fa-envelope fa-2x text-primary"></i>
                            </div>
                            <h5 class="card-title text-primary mb-3">Contact Us</h5>
                            <p class="text-muted small">Get in touch with us</p>
                            <ul class="list-unstyled text-start mb-0">
                                <li class="mb-2">
                                    <a href="contact.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                        <i class="fas fa-address-card text-primary me-2"></i>Contact Information
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="faq.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                        <i class="fas fa-question-circle text-primary me-2"></i>FAQs
                                    </a>
                                </li>
                                <li class="mb-0">
                                    <a href="feedback.php" class="text-decoration-none text-dark d-block p-2 rounded hover-bg">
                                        <i class="fas fa-comment-alt text-primary me-2"></i>Feedback
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Founders Section -->
    <section class="founders-section py-5 bg-light">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="display-5 fw-bold text-primary">हमारे संस्थापक</h2>
                <div class="divider mx-auto bg-primary"></div>
                <p class="lead">एनएससीटी के पीछे के दूरदर्शी व्यक्तित्व</p>
            </div>
            <div class="row justify-content-center g-4">
                <!-- Founder 1 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="assets/images/founder1.jpg" class="card-img-top" alt="Founder 1" onerror="this.src='assets/images/placeholder.svg'">
                            <div class="social-links">
                                <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">मंगला प्रसाद शर्मा 'नन्दवंशी'</h5>
                            <p class="text-muted mb-3">संरक्षक</p>
                            <p class="card-text small">समाज के प्रत्येक व्यक्ति की पीड़ा को अपने हृदय से महसूस कर, उसके समाधान हेतु दिन-रात निस्वार्थ समर्पित</p>
                        </div>
                    </div>
                </div>
                
                <!-- Founder 2 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="assets/images/founder2.jpg" class="card-img-top" alt="Founder 2" onerror="this.src='assets/images/placeholder.svg'">
                            <div class="social-links">
                                <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">प्रदीप कुमार वर्मा 'नन्दवंशी'</h5>
                            <p class="text-muted mb-3">संस्थापक एवं राष्ट्रीय अध्यक्ष</p>
                            <p class="card-text small">समाज में सहयोग की ज्योति जलाकर, सूक्ष्म अंशदान को वृहद आर्थिक संबल में बदलने वाले सच्चे प्रणेता।</p>
                        </div>
                    </div>
                </div>
                
                <!-- Founder 3 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="position-relative overflow-hidden">
                            <img src="assets/images/founder3.jpg" class="card-img-top" alt="Founder 3" onerror="this.src='assets/images/placeholder.svg'">
                            <div class="social-links">
                                <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">जितेंद्र कुमार शर्मा 'नन्दवंशी'</h5>
                            <p class="text-muted mb-3">संस्थापक एवं प्रांतीय अध्यक्ष</p>
                            <p class="card-text small">शिक्षा, सेवा, स्वास्थ्य और सुरक्षा – जीवन के मूल आधारों की अलख जगाकर समाज को दिशा देने वाले समर्पित समाजसेवी।</p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Co-Founders Slider Section -->
    <section class="co-founders-section py-5 bg-white">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="display-5 fw-bold text-primary">हमारे सह-संस्थापक</h2>
                <div class="divider mx-auto bg-primary"></div>
                <p class="lead">हमारे समर्पित सह-संस्थापकों की टीम से मिलें</p>
            </div>
            
            <?php
            // Array of co-founders with their details
            $coFounders = [
                [
                    'name' => 'शिव सहाय नन्द',
                    'designation' => 'संरक्षक',
                    'image' => 'co-founder1.jpg'
                ],
                [
                    'name' => 'नरेंद्र कुमार वर्मा',
                    'designation' => 'मार्गदर्शक & सलाहकार',
                    'image' => 'co-founder2.jpg'
                ],
                [
                    'name' => 'घनश्याम शर्मा',
                    'designation' => 'राष्ट्रीय प्रचारक',
                    'image' => 'co-founder3.jpg'
                ],
                [
                    'name' => 'प्रभाशंकर शर्मा',
                    'designation' => 'सह-संस्थापक',
                    'image' => 'co-founder4.jpg'
                ],
                [
                    'name' => 'श्याम जी शर्मा',
                    'designation' =>'सह-संस्थापक',
                    'image' => 'co-founder5.jpg'
                ],
                [
                    'name' => 'आशा शर्मा ',
                    'designation' => 'सह-संस्थापिका',
                    'image' => 'co-founder6.jpg'
                ],
                [
                    'name' =>'शिवा कांत शर्मा ',
                    'designation' => 'सह-संस्थापक',
                    'image' => 'co-founder7.jpg'
                ],
                [
                    'name' => 'सुरेंद्र कुमार शर्मा ',
                    'designation' => 'प्रान्तीय विस्तारक',
                    'image' => 'co-founder8.jpg'
                ],
                [
                    'name' => 'प्रकाश शर्मा ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder9.jpg'
                ],
                [
                    'name' => 'अमर नन्द श्रीवास्तव ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder10.jpg'
                ],
                [
                    'name' => 'हरिकिशन नन्दवंशी ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder11.jpg'
                ],
                [
                    'name' => 'चन्द्र प्रकाश ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder12.jpg'
                ],
				[
                    'name' => 'सूरज सेन ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder13.jpg'
                ],
                [
                    'name' => 'घनश्याम शर्मा ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder14.jpg'
                ],
                [
                    'name' => 'रवि प्रकाश शर्मा ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder15.jpg'
                ],
                [
                    'name' => 'विनोद कुमार शर्मा',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder16.jpg'
                ],
                [
                    'name' => 'ओम प्रकाश शर्मा ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder17.jpg'
                ],
                [
                    'name' => 'मनी राम भार्गव',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder18.jpg'
                ],
                [
                    'name' => 'होरी लाल सेन ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder19.jpg'
                ],
                [
                    'name' =>'सन्त प्रकाश वर्मा',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder20.jpg'
                ],
                [
                    'name' => 'मुकेश कुमार सेन ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder21.jpg'
                ],
                [
                    'name' => 'नवीन कुमार शर्मा ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder22.jpg'
                ],
                [
                    'name' => 'नरेश चंद्र ',
                    'designation' => 'सदस्य कोर टीम',
                    'image' => 'co-founder23.jpg'
                ],
                [
                    'name' => 'सौरभ शर्मा ',
                    'designation' => 'सदस्य कोर टीम ',
                    'image' => 'co-founder24.jpg'
                ]
				,
                [
                    'name' => 'वेद प्रकाश शर्मा ',
                    'designation' => 'सदस्य कोर टीम ',
                    'image' => 'co-founder25.jpg'
                ],
                [
                    'name' => 'जितेंद्र कुमार वर्मा नन्दवंशी ',
                    'designation' => 'लीगल एडवाइजर',
                    'image' => 'co-founder26.jpg'
                ],
                [
                    'name' => 'प्रभाकर शर्मा',
                    'designation' => 'तकनीकी & IT सेल',
                    'image' => 'co-founder27.jpg'
                ],
                [
                    'name' => 'आदर्श वर्मा',
                    'designation' => 'तकनीकी & IT सेल',
                    'image' => 'co-founder28.jpg'
                ],
                [
                    'name' => 'रोशन सेन ',
                    'designation' => 'तकनीकी & IT सेल',
                    'image' => 'co-founder29.jpg'
                ],
                [
                    'name' => 'विजय शंकर शर्मा',
                    'designation' => 'सदस्य',
                    'image' => 'co-founder30.jpg'
                ]
            ];
            
            // Split the co-founders into chunks of 6 for each slide
            $slides = array_chunk($coFounders, 6);
            ?>
            
            <div id="coFoundersCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach($slides as $slideIndex => $slide): ?>
                    <div class="carousel-item <?php echo $slideIndex === 0 ? 'active' : ''; ?>">
                        <div class="row g-4">
                            <?php foreach($slide as $index => $founder): 
                                $imgNumber = $slideIndex * 6 + $index + 1;
                            ?>
                            <div class="col-6 col-md-4 col-lg-2 text-center">
                                <div class="co-founder-card">
                             <img src="assets/images/<?php echo $founder['image']; ?>" 
                                 class="rounded-circle mb-3" 
                                 onerror="this.src='assets/images/placeholder.svg'"
                                 alt="<?php echo $founder['name']; ?>">
                                    <h5 class="co-founder-name mb-1"><?php echo htmlspecialchars($founder['name']); ?></h5>
                                    <p class="co-founder-designation text-muted mb-0"><?php echo htmlspecialchars($founder['designation']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#coFoundersCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-primary rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#coFoundersCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-primary rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                
                <!-- Indicators -->
                <div class="carousel-indicators position-relative mt-4">
                    <?php 
                    $totalSlides = 5; // 30 co-founders / 6 per slide = 5 slides
                    for($i = 0; $i < $totalSlides; $i++): 
                    ?>
                    <button type="button" data-bs-target="#coFoundersCarousel" 
                            data-bs-slide-to="<?php echo $i; ?>" 
                            class="<?php echo $i === 0 ? 'active' : ''; ?> bg-secondary"
                            aria-current="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                            aria-label="Slide <?php echo $i + 1; ?>">
                    </button>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top"><i class="fas fa-arrow-up"></i></a>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>
