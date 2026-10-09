
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$name = '';
$email = '';
$mobile = '';
$gender = '';
$dob = '';
$address = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = trim($_POST['address'] ?? '');

    $date = DateTime::createFromFormat('!Y-m-d', $dob);
    $validGenders = ['Male', 'Female', 'Other'];

    if (
        $name === '' ||
        mb_strlen($name) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 150 ||
        !preg_match('/^[0-9]{10,15}$/', $mobile) ||
        !in_array($gender, $validGenders, true) ||
        !$date ||
        $date->format('Y-m-d') !== $dob ||
        $date > new DateTime('today') ||
        $address === '' ||
        mb_strlen($address) > 2000
    ) {
        $error = 'Please enter valid details in all fields.';
    } else {
        try {
            $sql = "INSERT INTO submissions
                    (full_name, email, mobile, gender, dob, address)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $name, $email, $mobile, $gender, $dob, $address
            ]);

            header('Location: dashboard.php?added=1');
            exit;
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            $error = 'Unable to save the record. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: #f3f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 12px;
        }

        .text-center{
            color: #1a86f1;
        }
        .form-card {
            width: 100%;
            max-width: 620px;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(0,0,0,.09);
        }
    </style>
</head>
<body>
<div class="card form-card">
    <div class="card-body p-4 p-md-5">
        <h2 class="text-center mb-2">Add New Submission</h2>
        <p class="text-center text-secondary mb-4">
            Enter the registration details below
        </p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="add.php">
            <div class="mb-3">
                <label class="form-label" for="full_name">Full Name</label>
                <input class="form-control" id="full_name"
                       type="text" name="full_name" maxlength="100"
                       value="<?= e($name) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email"
                       type="email" name="email" maxlength="150"
                       value="<?= e($email) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="mobile">Mobile Number</label>
                <input class="form-control" id="mobile"
                       type="tel" name="mobile"
                       pattern="[0-9]{10,15}" maxlength="15"
                       value="<?= e($mobile) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="gender">Gender</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="dob">Date of Birth</label>
                <input class="form-control" id="dob"
                       type="date" name="dob" max="<?= date('Y-m-d') ?>"
                       value="<?= e($dob) ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label" for="address">Address</label>
                <textarea class="form-control" id="address"
                          name="address" rows="3" maxlength="2000"
                          required><?= e($address) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Save Submission
            </button>

            <a href="dashboard.php" class="btn btn-outline-secondary w-100 mt-2">
                Cancel
            </a>
        </form>
    </div>
</div>
</body>
</html>
