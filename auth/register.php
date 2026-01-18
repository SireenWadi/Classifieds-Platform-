<?php
session_start();
require_once __DIR__ . "/../src/config/db.php";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $error = "Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email,password,role) VALUES (?,?,?)");
        $stmt->bind_param("sss", $email, $password, $role);
        if($stmt->execute()){
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<link href="../assets/css/style.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include_once "../includes/navbar.php"; ?>

<div class="container py-5">
  <div class="row justify-content-center align-items-center">
    <div class="col-md-6 d-none d-md-block">
      <img src="../assets/images/register.jpeg" class="img-fluid rounded" alt="Register Image">
    </div>
    <div class="col-md-6">
      <div class="card shadow p-4">
        <h3 class="mb-4 text-center">Create Your Account</h3>
        <?php if(isset($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-warning w-100">Register</button>
        </form>
        <p class="mt-3 text-center">
          Already have an account? <a href="login.php">Login here</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include_once "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
