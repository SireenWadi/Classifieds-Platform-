<?php
session_start();
require_once __DIR__ . "/../src/config/db.php";

// ===============================
// Fetch categories
// ===============================
$cat_result = $conn->query("SELECT * FROM categories");

// ===============================
// Handle search query
// ===============================
$ads_result = null;
$searchPerformed = false;

if(isset($_GET['q']) && $_GET['q'] != ""){
    $q = $conn->real_escape_string($_GET['q']);
    $ads_stmt = $conn->prepare("SELECT ads.id, ads.title, ads.description, ads.price, ads.main_image 
                                FROM ads 
                                WHERE title LIKE CONCAT('%',?,'%') OR description LIKE CONCAT('%',?,'%') 
                                ORDER BY ads.id DESC");
    $ads_stmt->bind_param("ss", $q, $q);
    $ads_stmt->execute();
    $ads_result = $ads_stmt->get_result();
    $searchPerformed = true; // علامة على أن البحث تم
} else {
    $ads_result = $conn->query("SELECT ads.*, categories.name AS category_name 
                                FROM ads 
                                JOIN categories ON ads.category_id = categories.id 
                                ORDER BY ads.id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Classifieds</title>
<link href="../assets/css/style.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.hero-section { position: relative; text-align: center; color: white; }
.hero-section img { width: 100%; max-height: 400px; object-fit: cover; filter: brightness(0.6);}
.hero-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);}
.hero-content input[type="text"] { height: 55px; font-size: 1.2rem;}
.category-card { position: relative; overflow: hidden; border-radius: 8px; transition: transform 0.3s;}
.category-card:hover { transform: scale(1.05);}
.category-card h5 { position: absolute; bottom: 0; width: 100%; padding: 10px 0; background-color: rgba(0,0,0,0.5); color: white; text-align: center; margin: 0;}
.ad-card img { height: 220px; object-fit: cover; }
.ad-card .btn-view { background-color: orange; border-color: orange; color: white; }
.ad-card .btn-view:hover { background-color: darkorange; }
.contact-section .card { border: none; }
</style>
</head>
<body>

<?php include __DIR__ . "/../includes/navbar.php"; ?>

<!-- Hero Section -->
<section class="hero-section">
  <img src="../assets/images/hero.jpg" alt="Hero Image">
  <div class="hero-content">
    <h1 class="fw-bold display-5">Find Your Next Deal</h1>
    <p class="lead">Browse, Search and Post Ads Instantly</p>
    <form class="d-flex justify-content-center mt-4" action="index.php" method="GET">
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control w-75 me-2" placeholder="Search by title or description">
      <button class="btn btn-warning px-4">Search</button>
    </form>
  </div>
</section>

<!-- Categories -->
<section class="container py-5">
  <h2 class="mb-4">Categories</h2>
  <div class="row g-4">
    <?php while($cat = $cat_result->fetch_assoc()): ?>
    <div class="col-md-3 col-sm-6">
      <div class="category-card shadow-sm">

      <img src="../assets/images/categories/<?= htmlspecialchars($cat['image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($cat['name']) ?>">
        <h5><?= htmlspecialchars($cat['name']) ?></h5>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- Latest Ads -->
<section class="container py-5" id="latest-ads">
  <h2 class="mb-4">Latest Ads</h2>
  <div class="row g-4">
    <?php if($ads_result->num_rows == 0): ?>
      <div class="alert alert-info">No ads found.</div>
    <?php endif; ?>

    <?php while($ad = $ads_result->fetch_assoc()): ?>
      <div class="col-md-4 col-sm-6">
        <div class="card ad-card h-100 shadow-sm">
          <img src="../assets/uploads/<?= htmlspecialchars($ad['main_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($ad['title']) ?>">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($ad['title']); ?></h5>
            <p class="card-text small text-muted"><?= substr(htmlspecialchars($ad['description']),0,90); ?>...</p>
            <div class="mt-auto">
              <p class="fw-bold text-success">€<?= number_format($ad['price'],2); ?></p>
              <a href="../ads/view.php?id=<?= $ad['id'] ?>" class="btn btn-view w-100">View Details</a>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5 bg-light" id="contact">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-md-6">
        <h2 class="fw-bold mb-3">Contact Us</h2>
    <!--     <img src="../assets/images/1.jpeg" class="" >-->
        <p class="text-muted">Have a question or feedback? Send us a message.</p>
      </div>
      <div class="col-md-6">
        <form id="contactForm" class="card p-4 shadow-sm">
          <input type="text" name="name" class="form-control mb-3" placeholder="Your Name" required>
          <input type="email" name="email" class="form-control mb-3" placeholder="Your Email" required>
          <textarea name="message" rows="4" class="form-control mb-3" placeholder="Your Message" required></textarea>
          <button class="btn btn-warning w-100" type="submit">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="contactToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastBody"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Scroll to Latest Ads if search performed
<?php if($searchPerformed): ?>
document.getElementById('latest-ads')?.scrollIntoView({ behavior: 'smooth' });
<?php endif; ?>

// AJAX Contact Form Submission
document.getElementById('contactForm').addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('contact_ajax', '1');

    fetch('index.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            const toastEl = document.getElementById('contactToast');
            document.getElementById('toastBody').innerText = data.success;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            document.getElementById('contactForm').reset();
        });
});
</script>
</body>
</html>
