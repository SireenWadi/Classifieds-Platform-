<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
  <div class="container">

    <!-- Logo -->
    <a class="navbar-brand fw-bold" href="../public/index.php">
      <span style="color:#ffc107">Classi</span>Market
    </a>

    <!-- Mobile toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Links -->
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

        <li class="nav-item">
          <a class="nav-link" href="../public/index.php">Home</a>
        </li>
<!--
        <li class="nav-item">
          <a class="nav-link" href="../public/AD.php">Browse Ads</a>
        </li>-->

        <li class="nav-item">
          <a class="nav-link" href="../public/contact.php">Contact</a>
        </li>

        <?php if(isset($_SESSION['user_id'])): ?>

          <!-- Post Ad Button for logged-in users -->
          <li class="nav-item">
            <a class="btn btn-warning px-3" href="../ads/create.php">
              + Post Ad
            </a>
          </li>

          <!-- Logout -->
          <li class="nav-item">
            <a class="nav-link text-danger" href="../auth/logout.php">
              Logout
            </a>
          </li>

          <?php if($_SESSION['user_role'] === 'admin'): ?>
          <!-- Admin Panel Link -->
          <li class="nav-item">
            <a class="nav-link text-info" href="../admin/dashboard.php">
              Admin Panel
            </a>
          </li>
          <?php endif; ?>

        <?php else: ?>

          <li class="nav-item">
            <a class="nav-link" href="../auth/login.php">Login</a>
          </li>

          <li class="nav-item">
            <a class="btn btn-outline-warning px-3" href="../auth/register.php">
              Register
            </a>
          </li>

        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>
