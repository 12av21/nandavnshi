<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config/database.php';

$states = [
    'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
    'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
    'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
    'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
    'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
    'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Andaman and Nicobar Islands',
    'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi',
    'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
];

$bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $pan_aadhar = trim($_POST['pan_aadhar'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $father_husband_name = trim($_POST['father_husband_name'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $state = $_POST['state'] ?? '';
    $district = $_POST['district'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $nominee_name = trim($_POST['nominee_name'] ?? '');
    $nominee_relation = trim($_POST['nominee_relation'] ?? '');
    $other_relation = trim($_POST['other_relation'] ?? '');
    $nominee_mobile = trim($_POST['nominee_mobile'] ?? '');
    $blood_group = $_POST['blood_group'] ?? '';
    $family_members = !empty($_POST['family_members']) ? (int)$_POST['family_members'] : null;
    
    // If 'Other' is selected, use the custom relation
    if ($nominee_relation === 'Other' && !empty($other_relation)) {
        $nominee_relation = $other_relation;
    }

    // Validation
    if (empty($pan_aadhar)) {
        $errors[] = "PAN/Aadhar number is required";
    } elseif (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$|^[2-9]{1}[0-9]{11}$/i', $pan_aadhar)) {
        $errors[] = "Please enter a valid PAN or Aadhar number";
    }

    if (empty($mobile)) {
        $errors[] = "Mobile number is required";
    } elseif (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        $errors[] = "Please enter a valid 10-digit mobile number";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (empty($name)) {
        $errors[] = "Full name is required";
    }

    if (empty($father_husband_name)) {
        $errors[] = "Father's/Husband's name is required";
    }

    if (empty($dob)) {
        $errors[] = "Date of birth is required";
    } else {
        $dobObj = DateTime::createFromFormat('d/m/Y', $dob);
        if (!$dobObj) {
            $errors[] = "Invalid date format. Please use DD/MM/YYYY";
        } else {
            $dob = $dobObj->format('Y-m-d');
        }
    }

    if (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Please select a valid gender";
    }

    if (empty($address)) {
        $errors[] = "Address is required";
    }

    if (empty($state) || !in_array($state, $states)) {
        $errors[] = "Please select a valid state";
    }

    if (empty($district)) {
        $errors[] = "Please select a valid district";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    if (empty($nominee_name)) {
        $errors[] = "Nominee name is required";
    }

    if (empty($nominee_relation)) {
        $errors[] = "Nominee relation is required";
    }
    
    if (empty($nominee_mobile)) {
        $errors[] = "Nominee's mobile number is required";
    } elseif (!preg_match('/^[6-9]\d{9}$/', $nominee_mobile)) {
        $errors[] = "Please enter a valid 10-digit mobile number for nominee";
    }

    if ($family_members !== null && ($family_members < 1 || $family_members > 50)) {
        $errors[] = "Please enter a valid number of family members (1-50)";
    }

    if (!isset($_POST['declaration'])) {
        $errors[] = "You must accept the declaration to proceed with registration";
    }

    // If no validation errors, proceed with registration
    if (empty($errors)) {
        try {
            // Check if mobile or PAN/Aadhar already exists
            $stmt = $pdo->prepare("SELECT id FROM members WHERE mobile = ? OR pan_aadhar = ?");
            $stmt->execute([$mobile, $pan_aadhar]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Mobile number or PAN/Aadhar already registered";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO members 
                    (pan_aadhar, mobile, password, name, father_husband_name, 
                    dob, gender, address, state, district, email, nominee_name, 
                    nominee_relation, nominee_mobile, blood_group, family_members)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $pan_aadhar, $mobile, $hashed_password, $name, 
                    $father_husband_name, $dob, $gender, $address, 
                    $state, $district, $email ?: null, $nominee_name, 
                    $nominee_relation, $nominee_mobile, $blood_group ?: null,
                    $family_members
                ]);
                
                $success = true;
                // Clear form data after successful submission
                $_POST = [];
            }
        } catch (PDOException $e) {
            $errors[] = "Registration failed. Please try again later.";
            error_log("Registration Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration - NSCT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 2.5rem 3rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.92rem;
            letter-spacing: 0.2px;
        }
        .form-label.required:after {
            content: ' *';
            color: #dc3545;
        }
        .form-title {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            position: relative;
        }
        .form-title:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: #0d6efd;
        }
        .required:after {
            content: " *";
            color: red;
        }
        .invalid-feedback {
            font-size: 0.83rem;
            color: #dc3545;
            margin-top: 0.35rem;
            font-weight: 400;
            display: block;
            padding-left: 4px;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .success-message {
            color: #0f5132;
            font-size: 1.05em;
            text-align: center;
            margin-bottom: 25px;
            padding: 12px 15px;
            background-color: #d1e7dd;
            border-radius: 8px;
            border: 1px solid #badbcc;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .success-message i {
            font-size: 1.2em;
        }
        .card-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            font-weight: 600;
            padding: 1.1rem 1.75rem;
            border-radius: 12px 12px 0 0 !important;
            border: none;
            font-size: 1.05rem;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none;
            padding: 0.9rem 2.5rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 200px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.4rem;
            display: block;
            line-height: 1.4;
        }
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            border: 1px solid #f5c2c7;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }
        .alert-danger li {
            margin-bottom: 0.25rem;
        }
        @media (max-width: 767.98px) {
            .form-container {
                padding: 1.5rem;
                margin: 15px auto;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include 'includes/header.php';?>

    <div class="container-fluid py-5">
        <div class="form-container">
            <h2 class="form-title mb-4">Member Registration</h2>
            <p class="text-center text-muted mb-4">Please fill in all the required fields to create your account</p>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle me-2"></i>
                    Registration successful! You can now login with your mobile number and password.
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="registrationForm" class="needs-validation" novalidate>
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <!-- Login Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Login Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="pan_aadhar" class="form-label required">PAN or Aadhar Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="pan_aadhar" name="pan_aadhar" 
                                               placeholder="Enter PAN or Aadhar number" 
                                               value="<?php echo htmlspecialchars($_POST['pan_aadhar'] ?? ''); ?>" 
                                               required
                                               onblur="validateAndFetchDetails(this.value)">
                                        <button class="btn btn-outline-secondary" type="button" id="verifyIdBtn" onclick="validateAndFetchDetails(document.getElementById('pan_aadhar').value)">
                                            <i class="fas fa-search"></i> Verify
                                        </button>
                                    </div>
                                    <div class="form-text">Enter either your PAN (e.g., ABCDE1234F) or Aadhar (12 digits) number</div>
                                    <div id="idValidationFeedback" class="invalid-feedback">Please enter a valid PAN or Aadhar number</div>
                                    <div id="idVerificationStatus" class="mt-2"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="mobile" class="form-label required">Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+91</span>
                                        <input type="tel" class="form-control" id="mobile" name="mobile" 
                                               pattern="[6-9]\d{9}" 
                                               maxlength="10" 
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                               value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>" 
                                               required>
                                    </div>
                                    <div class="invalid-feedback">Please enter a valid 10-digit mobile number starting with 6-9</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email (Optional)</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label required">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" 
                                                   name="password" required>
                                            <button class="btn btn-outline-secondary toggle-password" 
                                                    type="button" data-target="#password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">Minimum 8 characters</div>
                                        <div class="invalid-feedback">Password must be at least 8 characters long</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label required">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" 
                                                   id="confirm_password" name="confirm_password" required>
                                            <button class="btn btn-outline-secondary toggle-password" 
                                                    type="button" data-target="#confirm_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Passwords do not match</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Address Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="address" class="form-label required">Full Address</label>
                                    <textarea class="form-control" id="address" name="address" 
                                              rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                                    <div class="invalid-feedback">Please enter your address</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="state" class="form-label required">State</label>
                                    <select class="form-select" id="state" name="state" required onchange="loadDistricts(this.value)">
                                        <option value="">-- Select State --</option>
                                        <?php 
                                        $states = [
                                            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
                                            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
                                            'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
                                            'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
                                            'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
                                            'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Andaman and Nicobar Islands',
                                            'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi',
                                            'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
                                        ];
                                        foreach ($states as $state): 
                                            $selected = (isset($_POST['state']) && $_POST['state'] === $state) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo htmlspecialchars($state); ?>" <?php echo $selected; ?>>
                                                <?php echo htmlspecialchars($state); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select your state</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="district" class="form-label required">District</label>
                                    <select class="form-select" id="district" name="district" required>
                                        <option value="" disabled selected>-- Select State First --</option>
                                        <?php if (isset($_POST['state']) && !empty($_POST['state'])): ?>
                                            <option value="<?php echo htmlspecialchars($_POST['district'] ?? ''); ?>" selected>
                                                <?php echo htmlspecialchars($_POST['district'] ?? 'Select District'); ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select your district</div>
                                    <div class="form-text" id="district-loading" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i> Loading districts...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <!-- Personal Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                           required
                                           oninput="this.classList.remove('is-invalid', 'is-valid');">
                                    <div class="invalid-feedback">Please enter your full name</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="father_husband_name" class="form-label required">Father's/Husband's Name</label>
                                    <input type="text" class="form-control" id="father_husband_name" name="father_husband_name" 
                                           value="<?php echo htmlspecialchars($_POST['father_husband_name'] ?? ''); ?>" 
                                           required
                                           oninput="this.classList.remove('is-invalid', 'is-valid');">
                                    <div class="invalid-feedback">Please enter father's/husband's name</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="dob" class="form-label required">Date of Birth</label>
                                        <input type="text" class="form-control datepicker" id="dob" 
                                               name="dob" placeholder="DD/MM/YYYY" 
                                               value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Please enter your date of birth</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Gender</label>
                                        <div class="d-flex gap-4 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" 
                                                       id="male" value="Male" 
                                                       <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'checked' : ''; ?> required>
                                                <label class="form-check-label" for="male">Male</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" 
                                                       id="female" value="Female"
                                                       <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="female">Female</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" 
                                                       id="other" value="Other"
                                                       <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="other">Other</label>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" style="display: none;">Please select your gender</div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="blood_group" class="form-label">Blood Group (Optional)</label>
                                    <select class="form-select" id="blood_group" name="blood_group">
                                        <option value="">-- Select Blood Group --</option>
                                        <?php foreach ($bloodGroups as $group): ?>
                                            <option value="<?php echo $group; ?>"
                                                <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] === $group) ? 'selected' : ''; ?>>
                                                <?php echo $group; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="family_members" class="form-label">Number of Family Members (Optional)</label>
                                    <input type="number" class="form-control" id="family_members" name="family_members" 
                                           min="1" max="50" 
                                           value="<?php echo htmlspecialchars($_POST['family_members'] ?? ''); ?>">
                                    <div class="form-text">Enter the total number of family members including yourself</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Nominee Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nominee_name" class="form-label required">Nominee Name</label>
                                    <input type="text" class="form-control" id="nominee_name" 
                                           name="nominee_name" 
                                           value="<?php echo htmlspecialchars($_POST['nominee_name'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please enter nominee's name</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nominee_relation" class="form-label required">Relation with Nominee</label>
                                    <select class="form-select" id="nominee_relation" name="nominee_relation" required onchange="toggleOtherRelationField(this.value)">
                                        <option value="" <?php echo empty($_POST['nominee_relation']) || $_POST['nominee_relation'] === 'Other' ? '' : 'selected'; ?>>-- Select Relation --</option>
                                        <?php
                                        $relations = ['Father', 'Mother', 'Wife', 'Son', 'Daughter', 'Other'];
                                        $selectedRelation = $_POST['nominee_relation'] ?? '';
                                        $otherRelation = '';
                                        
                                        if ($selectedRelation === 'Other' && !empty($_POST['other_relation'])) {
                                            $otherRelation = htmlspecialchars($_POST['other_relation']);
                                        }
                                        
                                        foreach ($relations as $relation) {
                                            $selected = ($selectedRelation === $relation) ? 'selected' : '';
                                            echo "<option value=\"$relation\" $selected>$relation</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Please select your relation with nominee</div>
                                </div>
                                
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nominee_mobile" class="form-label required">Nominee's Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+91</span>
                                        <input type="tel" class="form-control" id="nominee_mobile" name="nominee_mobile" 
                                               pattern="[6-9]\d{9}" 
                                               maxlength="10" 
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                               value="<?php echo htmlspecialchars($_POST['nominee_mobile'] ?? ''); ?>" 
                                               required>
                                    </div>
                                    <div class="invalid-feedback">Please enter a valid 10-digit mobile number starting with 6-9</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Declaration -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                                    <label class="form-check-label" for="declaration">
                                        <strong>मेरी घोषणा (My Declaration)</strong><br>
                                        मैं घोषणा करता/करती हूँ कि NSCT की नियमावली पढ़कर समझी है, वैधानिक सदस्यता हेतु नियमित सहयोग दूँगा/दूँगी तथा असहाय परिवारों को आर्थिक संबल प्रदान करने के इस पवित्र उद्देश्य में सक्रिय रहूँगा/रहूँगी।
                                        <div class="invalid-feedback">
                                            कृपया इस घोषणा को स्वीकार करें (Please accept this declaration)
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for datepicker) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI for datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
    <script>
        // Initialize datepicker
        $(function() {
            $(".datepicker").datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0',
                maxDate: '0',
                showButtonPanel: true,
                showAnim: 'fadeIn',
                showOtherMonths: true,
                selectOtherMonths: true,
                showOn: 'both',
                buttonImageOnly: true,
                buttonImage: 'data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>',
                buttonText: 'Select Date',
                buttonImageOnly: true
            });
            
        // Function to validate PAN/Aadhar and fetch details
        function validateAndFetchDetails(id) {
            const panAadharInput = document.getElementById('pan_aadhar');
            const verifyBtn = document.getElementById('verifyIdBtn');
            const statusDiv = document.getElementById('idVerificationStatus');
            
            // Reset previous states
            panAadharInput.classList.remove('is-valid', 'is-invalid');
            statusDiv.innerHTML = '';
            
            // Basic format validation
            const isPan = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i.test(id);
            const isAadhar = /^[2-9]\d{11}$/.test(id);
            
            if (!isPan && !isAadhar) {
                panAadharInput.classList.add('is-invalid');
                statusDiv.innerHTML = '<div class="text-danger">Please enter a valid PAN (e.g., ABCDE1234F) or Aadhar (12 digits) number</div>';
                return;
            }
            
            // Show loading state
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';
            statusDiv.innerHTML = '<div class="text-info">Verifying ID, please wait...</div>';
            
            // Call API to verify ID
            // Use absolute URL to avoid path issues
            const apiUrl = window.location.origin + '/NSCT/api/validate_id.php';
            console.log('Calling API:', apiUrl, 'with ID:', id);
            
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id }),
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('API Response:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Parsed Data:', data);
                if (data.success) {
                    panAadharInput.classList.remove('is-invalid');
                    panAadharInput.classList.add('is-valid');
                    statusDiv.innerHTML = '<div class="text-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    
                    // Auto-fill name and father's name if available
                    if (data.data) {
                        const nameInput = document.getElementById('name');
                        const fatherNameInput = document.getElementById('father_husband_name');
                        
                        if (data.data.name && !nameInput.value) {
                            nameInput.value = data.data.name;
                            nameInput.dispatchEvent(new Event('input')); // Trigger validation
                        }
                        
                        if (data.data.father_name && !fatherNameInput.value) {
                            fatherNameInput.value = data.data.father_name;
                            fatherNameInput.dispatchEvent(new Event('input')); // Trigger validation
                        }
                    }
                } else {
                    panAadharInput.classList.add('is-invalid');
                    statusDiv.innerHTML = '<div class="text-danger"><i class="fas fa-times-circle"></i> ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<div class="text-danger">Error: ' + error.message + '</div>';
            })
            .finally(() => {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-search"></i> Verify';
            });
        }
        
        // Function to load districts based on selected state
            function loadDistricts(state) {
                const districtSelect = document.getElementById('district');
                const loadingIndicator = document.getElementById('district-loading');
                
                if (!state) {
                    districtSelect.innerHTML = '<option value="" disabled selected>-- Select State First --</option>';
                    districtSelect.disabled = true;
                    return;
                }
                
                // Show loading indicator
                loadingIndicator.style.display = 'block';
                districtSelect.disabled = true;
                
                // Make AJAX request to get districts
                fetch(`includes/get_districts.php?state=${encodeURIComponent(state)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        
                        // Clear existing options
                        districtSelect.innerHTML = '<option value="" disabled selected>-- Select District --</option>';
                        
                        // Add new district options
                        data.districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district;
                            option.textContent = district;
                            
                            // Keep selected value if form was submitted with error
                            if (district === '<?php echo $_POST['district'] ?? ''; ?>') {
                                option.selected = true;
                            }
                            
                            districtSelect.appendChild(option);
                        });
                        
                        // Enable the select
                        districtSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading districts:', error);
                        districtSelect.innerHTML = '<option value="" disabled selected>Error loading districts</option>';
                    })
                    .finally(() => {
                        loadingIndicator.style.display = 'none';
                    });
            }
            
            // Function to toggle other relation field
            function toggleOtherRelationField(relation) {
                const otherRelationContainer = document.getElementById('otherRelationContainer');
                const otherRelationInput = document.getElementById('other_relation');
                
                if (relation === 'Other') {
                    otherRelationContainer.style.display = 'block';
                    otherRelationInput.required = true;
                } else {
                    otherRelationContainer.style.display = 'none';
                    otherRelationInput.required = false;
                }
            }
            
            // Initialize districts if state is already selected
            document.addEventListener('DOMContentLoaded', function() {
                const stateSelect = document.getElementById('state');
                if (stateSelect && stateSelect.value) {
                    loadDistricts(stateSelect.value);
                }
                
                // Initialize other relation field if 'Other' is selected
                const relationSelect = document.getElementById('nominee_relation');
                if (relationSelect && relationSelect.value === 'Other') {
                    toggleOtherRelationField('Other');
                }
            });
            
            // Toggle password visibility
            $('.toggle-password').click(function() {
                const target = $($(this).data('target'));
                const type = target.attr('type') === 'password' ? 'text' : 'password';
                target.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });
            
            // Form validation
            (function() {
                'use strict';
                
                // Fetch the form we want to apply custom validation styles to
                const form = document.getElementById('registrationForm');
                
                // Prevent submission if there are invalid fields
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    // Custom validation for gender
                    const genderSelected = document.querySelector('input[name="gender"]:checked');
                    if (!genderSelected) {
                        document.querySelector('.invalid-feedback[style*="display: none"]').style.display = 'block';
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        document.querySelector('.invalid-feedback[style*="display: none"]').style.display = 'none';
                    }
                    
                    // Custom validation for password match
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('confirm_password');
                    
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity("Passwords do not match");
                        confirmPassword.classList.add('is-invalid');
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        confirmPassword.setCustomValidity("");
                    }
                    
                    // Add was-validated class to show validation messages
                    form.classList.add('was-validated');
                }, false);
                
                // Real-time validation for PAN/Aadhar
                const panAadhar = document.getElementById('pan_aadhar');
                panAadhar.addEventListener('input', function() {
                    const value = this.value.trim();
                    const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i;
                    const aadharRegex = /^[2-9]{1}[0-9]{11}$/;
                    
                    if (value.match(panRegex) || value.match(aadharRegex)) {
                        this.setCustomValidity("");
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.setCustomValidity("Please enter a valid PAN or Aadhar number");
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });
                
                // Real-time validation for nominee mobile number
                const nomineeMobile = document.getElementById('nominee_mobile');
                nomineeMobile.addEventListener('input', function() {
                    const value = this.value.trim();
                    
                    // Only allow numeric input
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Limit to 10 digits
                    if (this.value.length > 10) {
                        this.value = this.value.slice(0, 10);
                    }
                    
                    // Validate format
                    if (value.length === 10 && /^[6-9]\d{9}$/.test(value)) {
                        this.setCustomValidity("");
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else if (value.length > 0) {
                        this.setCustomValidity("Please enter a valid 10-digit mobile number starting with 6-9");
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    } else {
                        this.setCustomValidity("");
                        this.classList.remove('is-valid', 'is-invalid');
                    }
                });
                
                // Real-time validation for mobile number
                const mobile = document.getElementById('mobile');
                mobile.addEventListener('input', function() {
                    const value = this.value.trim();
                    const mobileRegex = /^[6-9]\d{0,9}$/;
                    
                    // Only allow numeric input
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Limit to 10 digits
                    if (this.value.length > 10) {
                        this.value = this.value.slice(0, 10);
                    }
                    
                    // Validate format
                    if (value.length === 10 && /^[6-9]\d{9}$/.test(value)) {
                        this.setCustomValidity("");
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else if (value.length > 0) {
                        this.setCustomValidity("Please enter a valid 10-digit mobile number starting with 6-9");
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    } else {
                        this.setCustomValidity("");
                        this.classList.remove('is-valid', 'is-invalid');
                    }
                });
                
                // Real-time validation for password match
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('confirm_password');
                
                [password, confirmPassword].forEach(field => {
                    field.addEventListener('input', function() {
                        if (password.value !== confirmPassword.value) {
                            confirmPassword.setCustomValidity("Passwords do not match");
                            confirmPassword.classList.add('is-invalid');
                            confirmPassword.classList.remove('is-valid');
                        } else if (confirmPassword.value) {
                            confirmPassword.setCustomValidity("");
                            confirmPassword.classList.remove('is-invalid');
                            confirmPassword.classList.add('is-valid');
                        }
                        
                        if (password.value.length >= 8) {
                            password.setCustomValidity("");
                            password.classList.remove('is-invalid');
                            password.classList.add('is-valid');
                        } else if (password.value) {
                            password.setCustomValidity("Password must be at least 8 characters long");
                            password.classList.add('is-invalid');
                            password.classList.remove('is-valid');
                        }
                    });
                });
                
                // Add validation on blur for required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    field.addEventListener('blur', function() {
                        if (this.value.trim() === '') {
                            this.classList.add('is-invalid');
                        } else {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                        }
                    });
                });
                
                // Add validation for gender on change
                const genderRadios = form.querySelectorAll('input[name="gender"]');
                genderRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const feedback = document.querySelector('.invalid-feedback[style*="display: none"]');
                        if (document.querySelector('input[name="gender"]:checked')) {
                            feedback.style.display = 'none';
                        } else {
                            feedback.style.display = 'block';
                        }
                    });
                });
                
            })();
        });
    </script>
</body>
</html>
