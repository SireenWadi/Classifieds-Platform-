<?php
session_start();
require_once __DIR__ . "/../src/config/db.php"; // صححنا المسار

// Handle form submission
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            $successMessage = "Message sent successfully!";
        } else {
            $successMessage = "Failed to send message. Try again!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow p-4">
                <h2 class="mb-4 text-center">Contact Us</h2>
                
                <form method="POST" id="contactForm">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="message" rows="4" class="form-control" placeholder="Your Message" required></textarea>
                    </div>
                    <button class="btn btn-warning w-100" type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="contactToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastBody">
        <?= htmlspecialchars($successMessage) ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show toast only if there's a success message
let toastMessage = "<?= $successMessage ?>";
if(toastMessage.trim() !== "") {
    const toastEl = document.getElementById('contactToast');
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
</script>
</body>
</html>
