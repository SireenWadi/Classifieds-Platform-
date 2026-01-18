<?php
// Start session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to homepage after logout
header("Location: ../public/index.php");
exit();
?>
