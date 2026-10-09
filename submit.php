
<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name    = trim($_POST['full_name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$mobile  = trim($_POST['mobile'] ?? '');
$gender  = $_POST['gender'] ?? '';
$dob     = $_POST['dob'] ?? '';
$address = trim($_POST['address'] ?? '');

$validGenders = ['Male', 'Female', 'Other'];
$date = DateTime::createFromFormat('!Y-m-d', $dob);

if (
    $name === '' ||
    mb_strlen($name) > 100 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 150 ||
    !preg_match('/^[0-9]{10,15}$/', $mobile) ||
    !in_array($gender, $validGenders, true) ||
    $address === '' ||
    mb_strlen($address) > 2000 ||
    !$date ||
    $date->format('Y-m-d') !== $dob ||
    $date > new DateTime('today')
) {
    exit('Invalid form details. Please go back and check your entries.');
}

try {
    $sql = "INSERT INTO submissions
            (full_name, email, mobile, gender, dob, address)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $name, $email, $mobile, $gender, $dob, $address
    ]);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Form Submitted</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
              rel="stylesheet">

              
<style>
    body.success-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 15px;
        box-sizing: border-box;
    }

    .text-success  {
        color: #1a86f1 !important;
    }

    .success-page .container {
        width: 100%;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .success-page .card {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border-radius: 12px;
    }

    .success-page .card-body {
        padding: 28px !important;
    }

    @media (max-width: 576px) {
        .success-page .card-body {
            padding: 20px !important;
        }
    }
</style>


    </head>
<body class="bg-light success-page">
            <div class="container py-5 text-center">
            <div class="card shadow mx-auto p-4" style="max-width: 500px;">
                <h2 class="text-success">Form Submitted!</h2>
                <p>Your information has been saved successfully.</p>
                <a href="index.php" class="btn btn-primary">
                    Back to Form
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Unable to save your data. Please check the database connection.');
}
