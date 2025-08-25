<?php
$page_title = 'My Profile';
require_once 'includes/header.php';

// Get current user data
$user_id = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid CSRF token');
        header('Location: profile.php');
        exit;
    }
    
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $errors[] = 'Email is already taken';
    }
    
    // If changing password
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = 'Current password is required to change password';
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect';
        }
        
        if (empty($new_password)) {
            $errors[] = 'New password is required';
        } elseif (strlen($new_password) < 8) {
            $errors[] = 'New password must be at least 8 characters long';
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = 'New password and confirm password do not match';
        }
    }
    
    // If no errors, update user
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Update user data
            $update_data = [
                'name' => $name,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // If changing password
            if (!empty($new_password)) {
                $update_data['password'] = password_hash($new_password, PASSWORD_BCRYPT);
            }
            
            $update_fields = [];
            foreach ($update_data as $key => $value) {
                $update_fields[] = "$key = :$key";
            }
            
            $update_query = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = :id";
            $update_data['id'] = $user_id;
            
            $stmt = $pdo->prepare($update_query);
            $stmt->execute($update_data);
            
            // Update session data
            $_SESSION['admin_username'] = $name;
            $_SESSION['admin_email'] = $email;
            
            $pdo->commit();
            
            setFlashMessage('success', 'Profile updated successfully');
            header('Location: profile.php');
            exit;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            setFlashMessage('error', 'Error updating profile: ' . $e->getMessage());
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            setFlashMessage('error', $error);
        }
    }
}
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
            </div>
            <div class="card-body">
                <form method="post" action="profile.php" enctype="multipart/form-data">
                    <?php echo generateCsrfTokenField(); ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i> Leave password fields blank to keep current password
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="col-md-4">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="col-md-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Account Activity</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Account Created</th>
                                <td><?php echo date('F j, Y, g:i a', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td><?php echo !empty($user['updated_at']) ? date('F j, Y, g:i a', strtotime($user['updated_at'])) : 'Never'; ?></td>
                            </tr>
                            <tr>
                                <th>Last Login</th>
                                <td><?php echo !empty($user['last_login']) ? date('F j, Y, g:i a', strtotime($user['last_login'])) : 'Never'; ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td><?php echo ucfirst($user['role']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Profile Picture</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <?php
                    $avatar_url = !empty($user['avatar']) 
                        ? BASE_URL . '/uploads/avatars/' . $user['avatar']
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=4e73df&color=fff&size=200';
                    ?>
                    <img src="<?php echo $avatar_url; ?>" alt="Profile Picture" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <form action="upload_avatar.php" method="post" enctype="multipart/form-data" id="avatarForm">
                    <div class="mb-3">
                        <input type="file" class="form-control d-none" id="avatar" name="avatar" accept="image/*">
                        <label for="avatar" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-upload me-1"></i> Upload New Photo
                        </label>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="removeAvatar" <?php echo empty($user['avatar']) ? 'disabled' : ''; ?>>
                            <i class="fas fa-trash me-1"></i> Remove
                        </button>
                    </div>
                    <div class="small text-muted">
                        Allowed JPG, GIF or PNG. Max size 2MB
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Danger Zone</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold text-danger">Deactivate Account</h6>
                    <p class="small">Your account will be deactivated and you won't be able to log in again.</p>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                        <i class="fas fa-user-slash me-1"></i> Deactivate Account
                    </button>
                </div>
                
                <hr>
                
                <div>
                    <h6 class="font-weight-bold text-danger">Delete Account</h6>
                    <p class="small">Permanently delete your account and all associated data. This action cannot be undone.</p>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt me-1"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Account Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deactivateModalLabel">Deactivate Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to deactivate your account? You will be logged out immediately and won't be able to log in again.</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="deactivate_account.php" method="post" style="display: inline;">
                    <?php echo generateCsrfTokenField(); ?>
                    <button type="submit" class="btn btn-danger">Yes, Deactivate My Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteModalLabel">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i> Warning</h5>
                    <p class="mb-0">This action will permanently delete your account and all associated data. This cannot be undone.</p>
                </div>
                
                <p>To confirm, please type <strong>DELETE MY ACCOUNT</strong> in the box below:</p>
                
                <form id="deleteAccountForm" action="delete_account.php" method="post">
                    <?php echo generateCsrfTokenField(); ?>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="confirmText" name="confirm_text" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmCheck" name="confirm_check" required>
                        <label class="form-check-label" for="confirmCheck">
                            I understand that this action cannot be undone
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger" id="deleteAccountBtn" disabled>
                    <i class="fas fa-trash-alt me-1"></i> Permanently Delete My Account
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview for avatar upload
    const avatarInput = document.getElementById('avatar');
    const avatarForm = document.getElementById('avatarForm');
    const avatarImg = document.querySelector('.rounded-circle');
    const removeAvatarBtn = document.getElementById('removeAvatar');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    avatarImg.src = e.target.result;
                    
                    // Submit the form
                    const formData = new FormData(avatarForm);
                    
                    fetch('upload_avatar.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update avatar URL
                            avatarImg.src = data.avatar_url;
                            removeAvatarBtn.disabled = false;
                            
                            // Show success message
                            const toast = document.createElement('div');
                            toast.className = 'position-fixed bottom-0 end-0 p-3';
                            toast.style.zIndex = '11';
                            toast.innerHTML = `
                                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="toast-header bg-success text-white">
                                        <strong class="me-auto">Success</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        Profile picture updated successfully.
                                    </div>
                                </div>
                            `;
                            document.body.appendChild(toast);
                            
                            // Remove toast after 3 seconds
                            setTimeout(() => {
                                toast.remove();
                            }, 3000);
                        } else {
                            alert(data.message || 'Error uploading image');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error uploading image');
                    });
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Remove avatar
    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                fetch('remove_avatar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'remove_avatar=1&<?php echo generateCsrfTokenField('name_and_value'); ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset to default avatar
                        avatarImg.src = 'https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=4e73df&color=fff&size=200';
                        removeAvatarBtn.disabled = true;
                        
                        // Show success message
                        const toast = document.createElement('div');
                        toast.className = 'position-fixed bottom-0 end-0 p-3';
                        toast.style.zIndex = '11';
                        toast.innerHTML = `
                            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header bg-success text-white">
                                    <strong class="me-auto">Success</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    Profile picture removed successfully.
                                </div>
                            </div>
                        `;
                        document.body.appendChild(toast);
                        
                        // Remove toast after 3 seconds
                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    } else {
                        alert(data.message || 'Error removing profile picture');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing profile picture');
                });
            }
        });
    }
    
    // Delete account confirmation
    const confirmText = document.getElementById('confirmText');
    const confirmCheck = document.getElementById('confirmCheck');
    const deleteAccountBtn = document.getElementById('deleteAccountBtn');
    
    function updateDeleteButtonState() {
        const isTextMatch = confirmText.value.trim().toUpperCase() === 'DELETE MY ACCOUNT';
        deleteAccountBtn.disabled = !(isTextMatch && confirmCheck.checked);
    }
    
    if (confirmText && confirmCheck && deleteAccountBtn) {
        confirmText.addEventListener('input', updateDeleteButtonState);
        confirmCheck.addEventListener('change', updateDeleteButtonState);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
