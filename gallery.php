<?php
$page_title = "Gallery";
$page_container_class = 'col-12'; // Use full width for gallery
$page_content = <<<HTML
<div class="gallery-section">
    <div class="text-center mb-5">
        <h2 class="display-6 fw-bold">Our Moments</h2>
        <p class="lead text-muted">A glimpse into our community events and activities.</p>
    </div>

    <div class="row g-4">
        <!-- Gallery Image 1 -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal" data-bs-img="assets/images/gallery/1.jpg">
                <img src="assets/images/gallery/1.jpg" class="img-fluid rounded shadow-sm gallery-thumbnail" alt="Gallery Image 1" onerror="this.src='assets/images/placeholder.svg'">
            </a>
        </div>
        <!-- Gallery Image 2 -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal" data-bs-img="assets/images/gallery/2.jpg">
                <img src="assets/images/gallery/2.jpg" class="img-fluid rounded shadow-sm gallery-thumbnail" alt="Gallery Image 2" onerror="this.src='assets/images/placeholder.svg'">
            </a>
        </div>
        <!-- Gallery Image 3 -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal" data-bs-img="assets/images/gallery/3.jpg">
                <img src="assets/images/gallery/3.jpg" class="img-fluid rounded shadow-sm gallery-thumbnail" alt="Gallery Image 3" onerror="this.src='assets/images/placeholder.svg'">
            </a>
        </div>
        <!-- Gallery Image 4 -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal" data-bs-img="assets/images/gallery/4.jpg">
                <img src="assets/images/gallery/4.jpg" class="img-fluid rounded shadow-sm gallery-thumbnail" alt="Gallery Image 4" onerror="this.src='assets/images/placeholder.svg'">
            </a>
        </div>
        <!-- Add more images as needed -->
    </div>
</div>

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <img src="" id="modalImage" class="img-fluid w-100" alt="Enlarged Image">
      </div>
    </div>
  </div>
</div>

<style>
.gallery-thumbnail {
    aspect-ratio: 1 / 1;
    object-fit: cover;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}
.gallery-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}
</style>
HTML;

$page_scripts = <<<JS
<script>
document.addEventListener('DOMContentLoaded', function () {
    var galleryModal = document.getElementById('galleryModal');
    galleryModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var imgSrc = button.getAttribute('data-bs-img');
        var modalImage = galleryModal.querySelector('#modalImage');
        modalImage.src = imgSrc;
    });
});
</script>
JS;

include 'templates/page-template.php';
?>
