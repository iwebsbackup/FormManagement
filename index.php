
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Form</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

          <style>
    body.form-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 15px;
        box-sizing: border-box;
    }

    .text-center{
        color: #1a86f1;
    }
    .form-page .container {
        width: 100%;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .form-page .card {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        border-radius: 12px;
    }

    .form-page .card-body {
        padding: 30px !important;
    }

    @media (max-width: 576px) {
        body.form-page {
            padding: 20px 12px;
        }

        .form-page .card-body {
            padding: 20px !important;
        }
    }
</style>

</head>
<body class="bg-light form-page">

<div class="container py-5">
    <div class="card shadow mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <h2 class="text-center mb-4">Registration Form</h2>

            <form action="submit.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile Number</label>
                    <input type="tel" name="mobile"
                           class="form-control"
                           pattern="[0-9]{10,15}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control"
                              rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Submit Form
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
