<?php
session_start();
require_once __DIR__ . "/../src/config/db.php";

if(!isset($_GET['id'])){ header("Location: ../public/index.php"); exit; }
$ad_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT ads.*, categories.name AS category_name, users.email AS seller_email FROM ads JOIN categories ON ads.category_id = categories.id JOIN users ON ads.user_id = users.id WHERE ads.id=?");
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){ die("Ad not found"); }
$ad = $result->fetch_assoc();

$contact_message = "";
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['contact_message'])){
    $msg = trim($_POST['contact_message']);
    if($msg!=""){ $contact_message="Message sent successfully!"; } else { $contact_message="Message cannot be empty."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($ad['title']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . "/../includes/navbar.php"; ?>
<div class="container py-5">
  <div class="row">
    <div class="col-lg-6">
      <img src="../assets/uploads/<?= htmlspecialchars($ad['main_image']) ?>" class="d-block w-100 rounded" alt="Ad Image">
    </div>
    <div class="col-lg-6">
      <h2><?= htmlspecialchars($ad['title']) ?></h2>
      <span class="badge bg-secondary"><?= htmlspecialchars($ad['category_name']) ?></span>
      <p class="text-warning fs-4 fw-bold">€<?= number_format($ad['price'],2) ?></p>
      <p><?= nl2br(htmlspecialchars($ad['description'])) ?></p>

      <h4>Contact Seller</h4>
      <?php if($contact_message): ?><div class="alert alert-info"><?= $contact_message ?></div><?php endif; ?>
      <form method="POST">
        <textarea name="contact_message" class="form-control mb-3" rows="4" placeholder="Write your message here..." required></textarea>
        <button class="btn btn-primary">Send Message</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . "/../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
