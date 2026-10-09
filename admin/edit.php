
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

// Validate record ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid record ID.');
}

// Fetch existing record
$stmt = $pdo->prepare('SELECT * FROM submissions WHERE id = ?');
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    http_response_code(404);
    exit('Record not found.');
}

$error = '';

// Update record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = trim($_POST['address'] ?? '');

    $date = DateTime::createFromFormat('!Y-m-d', $dob);
    $validDate = $date &&
        $date->format('Y-m-d') === $dob &&
        $date <= new DateTime('today');

    if (
        $name === '' ||
        mb_strlen($name) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 150 ||
        !preg_match('/^[0-9]{10,15}$/', $mobile) ||
        !in_array($gender, ['Male', 'Female', 'Other'], true) ||
        $address === '' ||
        mb_strlen($address) > 2000 ||
        !$validDate
    ) {
        $error = 'Please enter valid details in all fields.';
    } else {
        try {
            $update = $pdo->prepare(
                'UPDATE submissions
                 SET full_name = ?, email = ?, mobile = ?,
                     gender = ?, dob = ?, address = ?
                 WHERE id = ?'
            );

            $update->execute([
                $name,
                $email,
                $mobile,
                $gender,
                $dob,
                $address,
                $id
            ]);

            header('Location: dashboard.php?updated=1');
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Unable to update the record. Please try again.';
        }
    }

    // Keep entered values if validation fails
    $record['full_name'] = $name;
    $record['email'] = $email;
    $record['mobile'] = $mobile;
    $record['gender'] = $gender;
    $record['dob'] = $dob;
    $record['address'] = $address;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Submission #<?= (int)$id ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 30px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f5f9;
            font-family: Arial, sans-serif;
        }

        .edit-card {
            width: 100%;
            max-width: 530px;
            padding: 26px;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
        }

        .edit-title {
            margin-bottom: 24px;
            font-size: 26px;
            font-weight: 600;
            color: #212529;
        }

        .form-label {
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            min-height: 40px;
            font-size: 14px;
        }

        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }

        .btn {
            padding: 9px 14px;
            font-size: 14px;
        }

        @media (max-width: 576px) {
            body {
                padding: 16px 12px;
            }

            .edit-card {
                padding: 20px;
            }

            .edit-title {
                font-size: 22px;
            }
        }

        @media (max-height: 700px) {
            body {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <main class="edit-card">
        <h1 class="edit-title">
            Edit Submission #<?= (int)$id ?>
        </h1>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?= (int)$id ?>">

            <div class="mb-3">
                <label for="full_name" class="form-label">
                    Full Name
                </label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    class="form-control"
                    maxlength="100"
                    required
                    value="<?= e($record['full_name']) ?>"
                >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    maxlength="150"
                    required
                    value="<?= e($record['email']) ?>"
                >
            </div>

            <div class="mb-3">
                <label for="mobile" class="form-label">
                    Mobile Number
                </label>
                <input
                    type="tel"
                    id="mobile"
                    name="mobile"
                    class="form-control"
                    pattern="[0-9]{10,15}"
                    maxlength="15"
                    required
                    value="<?= e($record['mobile']) ?>"
                >
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select
                    id="gender"
                    name="gender"
                    class="form-select"
                    required
                >
                    <option value="">Select Gender</option>

                    <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                        <option
                            value="<?= e($g) ?>"
                            <?= $record['gender'] === $g ? 'selected' : '' ?>
                        >
                            <?= e($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="dob" class="form-label">
                    Date of Birth
                </label>
                <input
                    type="date"
                    id="dob"
                    name="dob"
                    class="form-control"
                    max="<?= date('Y-m-d') ?>"
                    required
                    value="<?= e($record['dob']) ?>"
                >
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea
                    id="address"
                    name="address"
                    class="form-control"
                    maxlength="2000"
                    required
                ><?= e($record['address']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Update Record
                </button>

                <a href="dashboard.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>
    </main>

</body>
</html>
