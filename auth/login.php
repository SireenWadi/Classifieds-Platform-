<?php
session_start();
require_once __DIR__ . "/../src/config/db.php";

// Check if form is submitted
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        // Verify password
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: ../public/index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link href="../assets/css/style.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center align-items-center">
    
    <!-- Image Column -->
    <div class="col-md-6 d-none d-md-block">
      <img src="../assets/images/login.jpg" class="img-fluid rounded" alt="Login Image">
    </div>
    
    <!-- Form Column -->
    <div class="col-md-6">
      <div class="card shadow p-4">
        <h3 class="mb-4 text-center">Login to Your Account</h3>

        <!-- Display Error -->
        <?php if(isset($error)): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-warning w-100">Login</button>
        </form>

        <p class="mt-3 text-center">
          Don't have an account? <a href="register.php">Register here</a>
        </p>
      </div>
    </div>
    
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
