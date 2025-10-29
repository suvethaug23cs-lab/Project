<?php
$message = '';
$messageClass = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $number    = trim($_POST['number'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (empty($firstName) || empty($lastName) || empty($gender) || empty($number) || empty($email) || empty($password)) {
        $message = "All fields are required.";
        $messageClass = "alert-danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageClass = "alert-danger";
    } elseif (!preg_match("/^[0-9]{7,15}$/", $number)) {
        $message = "Invalid phone number. Only digits allowed (7-15 digits).";
        $messageClass = "alert-danger";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $conn = new mysqli('localhost', 'root', '', 'sign');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO sign (firstName, lastName, gender, number, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstName, $lastName, $gender, $number, $email, $hashed_password);

        if ($stmt->execute()) {
            $message = "Registration successful!";
            $messageClass = "alert-success";

            // Clear form values after successful registration
            $firstName = $lastName = $gender = $number = $email = $password = '';
        } else {
            $message = "Error: " . $stmt->error;
            $messageClass = "alert-danger";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bootstrap Registration Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="mb-4 text-center">Register</h2>

    <?php if ($message): ?>
        <div class="alert <?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form id="registrationForm" method="post" novalidate>
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="firstName" class="form-control" required value="<?php echo htmlspecialchars($firstName ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="lastName" class="form-control" required value="<?php echo htmlspecialchars($lastName ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select" required>
                <option value="">Select</option>
                <option value="Male" <?php if(($gender ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if(($gender ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if(($gender ?? '') == 'Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="number" class="form-control" required value="<?php echo htmlspecialchars($number ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
</div>

<!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Client-side validation
    const form = document.getElementById('registrationForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            alert("Please fill all fields correctly.");
        }
        form.classList.add('was-validated');
    }, false);
</script>

</body>
</html>