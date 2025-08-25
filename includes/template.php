<?php
$page_title = ""; // This will be set in each page
include 'includes/header.php';
?>

<!-- Page Content -->
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 mb-4"><?php echo $page_title; ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $page_title; ?></li>
                </ol>
            </nav>
            
            <!-- Page specific content will go here -->
            
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
