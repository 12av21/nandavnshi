<?php
session_start();
require_once 'config/database.php';

// Helper function to get settings from the database
if (!function_exists('get_setting')) {
    function get_setting($pdo, $key, $default = '') {
        static $settings = null;
        if ($settings === null) {
            try {
                $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
                $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            } catch (PDOException $e) {
                error_log("Error fetching settings: " . $e->getMessage());
                $settings = [];
            }
        }
        return $settings[$key] ?? $default;
    }
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $phone = trim($_POST['phone'] ?? ''); // Optional field

    // Basic validation
    if (empty($name)) $errors[] = "Full Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email is required.";
    if (empty($subject)) $errors[] = "Subject is required.";
    if (empty($message)) $errors[] = "Message is required.";
    if (!isset($_POST['privacyPolicy'])) $errors[] = "You must agree to the Privacy Policy.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([ $name, $email, $subject . ($phone ? " (Phone: $phone)" : ""), $message, $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN' ]);
            $success = "Thank you for your message! We have received it and will get back to you shortly.";
            $_POST = []; // Clear form
        } catch (PDOException $e) {
            $errors[] = "Sorry, there was an error sending your message. Please try again later.";
            error_log("Contact Form Error: " . $e->getMessage());
        }
    }
}

// Fetch social links
try {
    $stmt = $pdo->query("SELECT platform, icon_class, url FROM social_links WHERE is_active = 1 ORDER BY display_order ASC");
    $social_links = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching social links: " . $e->getMessage());
    $social_links = [];
}

// Fetch dynamic contact info
$contact_address = get_setting($pdo, 'contact_address', 'Village - Mishrapur, Post - Dubepur<br>Tehsil - Meja, District - Prayagraj<br>Uttar Pradesh - 212305');
$helpline_number = get_setting($pdo, 'helpline_number', '+91-707-167-7676');
$contact_email = get_setting($pdo, 'contact_email', 'call_me@nandvanshi.org');

$page_title = "Contact Us";
ob_start(); // Start output buffering
?>
    <!-- Display success or error messages -->
    <?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class='contact-section'>
        <div class='row mb-5'>
            <div class='col-lg-8 mx-auto text-center'>
                <h2>Contact Us</h2>
                <p class='lead'>Get in touch with the NSCT team for any inquiries or support</p>
                <div class='divider bg-primary mx-auto' style='width: 80px; height: 3px;'></div>
            </div>
        </div>
        
        <div class='row g-4 mb-5'>
            <!-- Contact Information -->
            <div class='col-lg-4'>
                <div class='card h-100 border-0 shadow-sm'>
                    <div class='card-body p-4'>
                        <div class='text-center mb-4'>
                            <div class='icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-3'>
                                <i class='fas fa-map-marker-alt fa-2x'></i>
                            </div>
                            <h4>Our Office</h4>
                            <p class='text-muted mb-0'>
                                <?php echo nl2br(htmlspecialchars($contact_address)); ?>
                            </p>
                        </div>
                        
                        <div class='text-center mb-4'>
                            <div class='icon-box bg-success bg-opacity-10 text-success mx-auto mb-3'>
                                <i class='fas fa-phone-alt fa-2x'></i>
                            </div>
                            <h4>Phone</h4>
                            <p class='text-muted mb-0'>
                                <a href='tel:<?php echo htmlspecialchars($helpline_number); ?>' class='text-decoration-none text-muted'><?php echo htmlspecialchars($helpline_number); ?></a>
                            </p>
                        </div>
                        
                        <div class='text-center'>
                            <div class='icon-box bg-info bg-opacity-10 text-info mx-auto mb-3'>
                                <i class='fas fa-envelope fa-2x'></i>
                            </div>
                            <h4>Email</h4>
                            <p class='text-muted mb-0'>
                                <a href='mailto:<?php echo htmlspecialchars($contact_email); ?>' class='text-decoration-none text-muted'><?php echo htmlspecialchars($contact_email); ?></a>
                            </p>
                        </div>

                        <hr class="my-4">

                        <div class='text-center'>
                            <h4 class="h5">Follow Us</h4>
                            <div class="d-flex justify-content-center mt-3">
                                <?php foreach($social_links as $link): ?>
                                <a href="<?php echo htmlspecialchars($link['url']); ?>" class="social-icon text-secondary me-3" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($link['platform']); ?>">
                                    <i class="<?php echo htmlspecialchars($link['icon_class']); ?> fa-2x"></i>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class='col-lg-8'>
                <div class='card border-0 shadow-sm h-100'>
                    <div class='card-body p-4'>
                        <h4 class='mb-4'>Send us a Message</h4>
                        <form id='contactForm' method="POST" action="contact.php" class="needs-validation" novalidate>
                            <div class='row g-3'>
                                <div class='col-md-6'>
                                    <label for='name' class='form-label'>Full Name <span class='text-danger'>*</span></label>
                                    <div class='input-group'>
                                        <span class='input-group-text'><i class='far fa-user'></i></span>
                                        <input type='text' class='form-control' id='name' name='name' value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                        <div class='invalid-feedback'>Please enter your name.</div>
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <label for='email' class='form-label'>Email <span class='text-danger'>*</span></label>
                                    <div class='input-group'>
                                        <span class='input-group-text'><i class='far fa-envelope'></i></span>
                                        <input type='email' class='form-control' id='email' name='email' value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                        <div class='invalid-feedback'>Please enter a valid email address.</div>
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <label for='phone' class='form-label'>Phone Number</label>
                                    <div class='input-group'>
                                        <span class='input-group-text'><i class='fas fa-phone'></i></span>
                                        <input type='tel' class='form-control' id='phone' name='phone' value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <label for='subject' class='form-label'>Subject <span class='text-danger'>*</span></label>
                                    <div class='input-group'>
                                        <span class='input-group-text'><i class='fas fa-tag'></i></span>
                                        <select class='form-select' id='subject' name='subject' required>
                                            <option value='' disabled <?php echo empty($_POST['subject']) ? 'selected' : ''; ?>>Select a subject</option>
                                            <?php
                                            $subjects = ['Membership Inquiry', 'Financial Aid', 'Education Support', 'Events', 'Donation', 'Other'];
                                            foreach ($subjects as $s) {
                                                $selected = (isset($_POST['subject']) && $_POST['subject'] === $s) ? 'selected' : '';
                                                echo "<option value='$s' $selected>$s</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class='invalid-feedback'>Please select a subject.</div>
                                    </div>
                                </div>
                                <div class='col-12'>
                                    <label for='message' class='form-label'>Your Message <span class='text-danger'>*</span></label>
                                    <textarea class='form-control' id='message' name='message' rows='5' required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                    <div class='invalid-feedback'>Please enter your message.</div>
                                </div>
                                <div class='col-12'>
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' id='privacyPolicy' name="privacyPolicy" required>
                                        <label class='form-check-label' for='privacyPolicy'>
                                            I agree to the <a href='#'>Privacy Policy</a> and consent to NSCT contacting me regarding my inquiry.
                                        </label>
                                        <div class='invalid-feedback'>You must agree before submitting.</div>
                                    </div>
                                </div>
                                <div class='col-12'>
                                    <button type='submit' class='btn btn-primary px-4'>
                                        <i class='fas fa-paper-plane me-2'></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Office Hours -->
        <div class='row mb-5'>
            <div class='col-lg-8 mx-auto'>
                <div class='card border-0 shadow-sm'>
                    <div class='card-body p-4'>
                        <div class='row align-items-center'>
                            <div class='col-md-4 text-center mb-3 mb-md-0'>
                                <div class='icon-box bg-warning bg-opacity-10 text-warning d-inline-flex mb-3'>
                                    <i class='fas fa-clock fa-2x'></i>
                                </div>
                                <h4>Office Hours</h4>
                            </div>
                            <div class='col-md-8'>
                                <div class='table-responsive'>
                                    <table class='table table-borderless mb-0'>
                                        <tbody>
                                            <tr>
                                                <td><strong>Monday - Friday</strong></td>
                                                <td>9:00 AM - 6:00 PM</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Saturday</strong></td>
                                                <td>10:00 AM - 4:00 PM</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sunday</strong></td>
                                                <td>Closed</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Public Holidays</strong></td>
                                                <td>Closed</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map -->
        <div class='row'>
            <div class='col-12 mb-4'>
                <div class='card border-0 shadow-sm'>
                    <div class='card-body p-0'>
                        <div class='ratio ratio-21x9'>
                            <iframe 
                                src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3611.835310144185!2d82.1119168759616!3d25.1412558777477!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399a97b66b1b757f%3A0x356e887a33483758!2sMishrapur%2C%20Uttar%20Pradesh%20212305!5e0!3m2!1sen!2sin!4v1689682855321!5m2!1sen!2sin' 
                                style='border:0;' 
                                allowfullscreen='' 
                                loading='lazy'>
                            </iframe>
                        </div>
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
        margin: 0 auto 1rem;
    }
    .contact-section .form-control, .contact-section .form-select {
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
    }
    .contact-section .form-control:focus, .contact-section .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .contact-section .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }
    .contact-section .form-control:not(:first-child) {
        border-left: none;
    }
    .contact-section .btn-primary {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }
    .contact-section table td {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .contact-section table tr:last-child td {
        border-bottom: none;
    }
    .social-icon {
        transition: color 0.3s ease, transform 0.2s ease;
    }
    .social-icon:hover {
        color: var(--bs-primary) !important;
        transform: translateY(-3px);
    }
    </style>
    
    <script>
    // Form validation
    (function () {
        'use strict'
        
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
            })
    })()
    </script>
<?php
$page_content = ob_get_clean(); // Get buffered content
include 'templates/page-template.php';
?>
