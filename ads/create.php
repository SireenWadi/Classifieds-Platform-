<?php
session_start();
require_once __DIR__ . "/../src/config/db.php";

// Protect page (only logged-in users)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $user_id = $_SESSION['user_id'];

    // Upload image
    $uploadDir = __DIR__ . "/../assets/uploads/";
    $imageName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['image']['name']);
    $targetPath = $uploadDir . $imageName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {

        $stmt = $conn->prepare("
            INSERT INTO ads (user_id, category_id, title, description, price, main_image)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissds", $user_id, $category_id, $title, $description, $price, $imageName);
        $stmt->execute();

        // Redirect to homepage ads section
        header("Location: ../public/index.php#ads");
        exit();

    } else {
        $error = "Image upload failed.";
    }
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Ad</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-lg border-0">
        <div class="card-body p-5">

          <h2 class="mb-4 text-center fw-bold">Post a New Ad</h2>

          <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
              <label class="form-label">Ad Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" rows="4" class="form-control" required></textarea>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Price (€)</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                  <option value="">Select Category</option>
                  <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>">
                      <?= htmlspecialchars($cat['name']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label">Main Image</label>
              <input type="file" name="image" class="form-control" required>
            </div>

            <button class="btn btn-warning w-100 py-2 fw-bold">
              Publish Ad
            </button>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
