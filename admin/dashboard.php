<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Search
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare(
        "SELECT * FROM submissions
         WHERE full_name LIKE ?
            OR email LIKE ?
            OR mobile LIKE ?
         ORDER BY id DESC"
    );

    $term = '%' . $search . '%';
    $stmt->execute([$term, $term, $term]);
} else {
    $stmt = $pdo->query(
        "SELECT * FROM submissions ORDER BY id DESC"
    );
}

$records = $stmt->fetchAll();

// Total records (search se independent)
$countStmt = $pdo->query("SELECT COUNT(*) FROM submissions");
$total = (int)$countStmt->fetchColumn();

// Feedback messages
$notice = '';

if (isset($_GET['updated'])) {
    $notice = 'Record updated successfully.';
} elseif (isset($_GET['deleted'])) {
    $notice = 'Record deleted successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Form Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: .3px;
        }

        .dashboard-title {
            font-weight: 700;
            color: #1a86f1;
        }

        .stat-card {
            border: 0;
            border-left: 5px solid #0d6efd;
            border-radius: 10px;
        }

        .table thead th {
            white-space: nowrap;
            font-size: 14px;
        }

        .table tbody td {
            font-size: 14px;
        }

        .address-cell {
            min-width: 140px;
            max-width: 220px;
            overflow-wrap: anywhere;
        }

        .actions-cell {
            min-width: 145px;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-3 px-md-4">
            <a class="navbar-brand" href="dashboard.php">
                Form Management Admin
            </a>

            <a href="logout.php" class="btn btn-outline-light btn-sm">
                Logout
            </a>
        </div>
    </nav>

    <main class="container-fluid px-3 px-md-4 py-4">

        <?php if ($notice !== ''): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= e($notice) ?>
                <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="dashboard-title mb-1">Admin Dashboard</h2>
                <p class="text-secondary mb-0">
                    Manage all registration submissions
                </p>
            </div>

            <!-- <a href="../index.php" class="btn btn-primary">
            + Add New Submission
        </a> -->

            <a href="add.php" class="btn btn-primary">
                + Add New Submission
            </a>

        </div>

        <!-- Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-secondary mb-2">Total Submissions</div>
                        <h2 class="mb-0"><?= $total ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-secondary mb-2">Matching Results</div>
                        <h2 class="mb-0"><?= count($records) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md">
                        <input
                            type="search"
                            name="search"
                            class="form-control"
                            placeholder="Search by name, email or mobile..."
                            value="<?= e($search) ?>">
                    </div>

                    <div class="col-6 col-md-auto">
                        <button type="submit" class="btn btn-primary w-100">
                            Search
                        </button>
                    </div>

                    <div class="col-6 col-md-auto">
                        <a href="dashboard.php"
                            class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submissions table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Form Submissions</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Gender</th>
                                <th>DOB</th>
                                <th>Address</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="9"
                                        class="text-center py-4 text-secondary">
                                        No submissions found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($records as $row): ?>
                                    <tr>
                                        <td><?= (int)$row['id'] ?></td>
                                        <td><?= e($row['full_name']) ?></td>
                                        <td><?= e($row['email']) ?></td>
                                        <td><?= e($row['mobile']) ?></td>
                                        <td><?= e($row['gender']) ?></td>
                                        <td><?= e($row['dob']) ?></td>
                                        <td class="address-cell">
                                            <?= e($row['address']) ?>
                                        </td>
                                        <td><?= e($row['created_at']) ?></td>

                                        <td class="actions-cell">
                                            <a
                                                href="edit.php?id=<?= (int)$row['id'] ?>"
                                                class="btn btn-sm btn-warning">
                                                Edit
                                            </a>

                                            <form
                                                action="delete.php"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to permanently delete this record?');">
                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int)$row['id'] ?>">

                                                <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white text-secondary small">
                Showing <?= count($records) ?> result(s)
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>