<?php
$page_title = "News & Updates";
$page_content = <<<HTML
<div class="news-section">
    <div class="text-center mb-5">
        <h2 class="display-6 fw-bold">Latest News & Updates</h2>
        <p class="lead text-muted">Stay informed about the latest happenings at NSCT.</p>
    </div>

    <div class="row g-4">
        <!-- News Item 1 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <img src="assets/images/news/news1.jpg" class="card-img-top" alt="News Image 1" onerror="this.src='assets/images/placeholder.svg'">
                <div class="card-body">
                    <div class="small text-muted mb-2">10 Nov 2024 | Announcements</div>
                    <h5 class="card-title">NSCT Launches New Scholarship Program</h5>
                    <p class="card-text small">A new merit-based scholarship program has been launched to support higher education for students from the community.</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                </div>
            </div>
        </div>
        <!-- News Item 2 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <img src="assets/images/news/news2.jpg" class="card-img-top" alt="News Image 2" onerror="this.src='assets/images/placeholder.svg'">
                <div class="card-body">
                    <div class="small text-muted mb-2">01 Nov 2024 | Events</div>
                    <h5 class="card-title">Successful Health Camp Organized</h5>
                    <p class="card-text small">Over 200 members benefited from the free health check-up camp organized in Mishrapur last month.</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                </div>
            </div>
        </div>
        <!-- News Item 3 -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="alert alert-info h-100 d-flex flex-column justify-content-center text-center">
                        <h5 class="alert-heading">No More News</h5>
                        <p>Check back later for more updates and announcements from NSCT.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.card-img-top { aspect-ratio: 16/9; object-fit: cover; }
</style>
HTML;

include 'templates/page-template.php';
?>
