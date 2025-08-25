<?php
$page_title = "About Us";
$page_content = <<<HTML
<div class="about-section">
    <!-- Introduction Section -->
    <div class="row mb-5 align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="assets/images/about-us-image.jpg" class="img-fluid rounded shadow" alt="Community Gathering" onerror="this.src='assets/images/placeholder.svg'">
        </div>
        <div class="col-lg-6">
            <h2 class="display-6 fw-bold">Welcome to Nandvanshi Self Care Trust</h2>
            <p class="lead text-muted">A community-driven initiative for collective growth and support.</p>
            <p>Nandvanshi Self Care Trust (NSCT) is a non-profit organization founded on the principles of mutual support, empowerment, and community welfare. We are dedicated to uplifting the Nandvanshi community by providing essential resources and creating opportunities for a brighter, self-reliant future.</p>
            <p>Our journey began with a simple idea: to create a support system that ensures no member of our community is left behind. Today, we stand as a testament to what collective effort and shared vision can achieve.</p>
        </div>
    </div>

    <!-- Mission and Vision Section -->
    <div class="row text-center mb-5 g-4">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                        <i class="fas fa-bullseye fa-2x"></i>
                    </div>
                    <h3 class="h4">Our Mission</h3>
                    <p class="text-muted">To empower the Nandvanshi community through education, healthcare, financial support, and skill development, fostering a culture of self-reliance and mutual care.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="fas fa-eye fa-2x"></i>
                    </div>
                    <h3 class="h4">Our Vision</h3>
                    <p class="text-muted">To build a resilient, progressive, and united Nandvanshi community where every individual has the opportunity to thrive and contribute to society.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- What We Do Section -->
    <div class="text-center mb-5">
        <h2 class="display-6 fw-bold">What We Do</h2>
        <p class="lead text-muted">Our core areas of focus to drive community development.</p>
    </div>
    <div class="row g-4 text-center">
        <div class="col-lg-3 col-md-6">
            <div class="p-4 rounded bg-light h-100">
                <div class="icon-box bg-info bg-opacity-10 text-info mx-auto mb-3">
                    <i class="fas fa-graduation-cap fa-2x"></i>
                </div>
                <h4 class="h5">Education</h4>
                <p class="small">Providing scholarships, coaching, and resources to ensure quality education for all.</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="p-4 rounded bg-light h-100">
                <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                    <i class="fas fa-hand-holding-medical fa-2x"></i>
                </div>
                <h4 class="h5">Healthcare</h4>
                <p class="small">Organizing medical camps and providing financial aid for health emergencies.</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="p-4 rounded bg-light h-100">
                <div class="icon-box bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                    <i class="fas fa-briefcase fa-2x"></i>
                </div>
                <h4 class="h5">Livelihood</h4>
                <p class="small">Offering skill development and training programs to enhance employment opportunities.</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="p-4 rounded bg-light h-100">
                <div class="icon-box bg-secondary bg-opacity-10 text-secondary mx-auto mb-3">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h4 class="h5">Community Support</h4>
                <p class="small">Providing emergency relief and support during crises to ensure community well-being.</p>
            </div>
        </div>
    </div>

    <!-- Our Journey Section -->
    <div class="row mt-5 pt-5 border-top">
        <div class="col-lg-8 mx-auto text-center">
            <h2 class="display-6 fw-bold">Our Journey</h2>
            <p class="lead text-muted">From a humble beginning to a thriving community trust.</p>
            <p>Founded in [Year], NSCT started with a small group of dedicated individuals who wanted to make a difference. Over the years, with the unwavering support of our members and volunteers, we have grown into a robust organization impacting hundreds of lives. Our journey is a story of community, collaboration, and commitment.</p>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card text-center p-4 bg-primary text-white border-0">
                <div class="card-body">
                    <h3 class="card-title">Join Us in Our Mission</h3>
                    <p class="card-text lead">Become a member, volunteer, or donate to help us continue our work and make a lasting impact.</p>
                    <a href="become-member.php" class="btn btn-light btn-lg px-4 me-2">Become a Member</a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg px-4">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
HTML;

include 'templates/page-template.php';
?>
