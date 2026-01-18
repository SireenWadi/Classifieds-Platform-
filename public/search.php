<?php
require_once __DIR__ . "/../src/config/db.php";

// Get search inputs safely
$keyword   = $_GET['q'] ?? '';
$category  = $_GET['category'] ?? '';

// Base SQL
$sql = "SELECT ads.*, categories.name AS category_name
        FROM ads
        JOIN categories ON ads.category_id = categories.id
        WHERE 1";

$params = [];

// Search by title or description
if (!empty($keyword)) {
    $sql .= " AND (ads.title LIKE :keyword OR ads.description LIKE :keyword)";
    $params[':keyword'] = "%" . $keyword . "%";
}

// Filter by category
if (!empty($category)) {
    $sql .= " AND categories.id = :category";
    $params[':category'] = $category;
}

$sql .= " ORDER BY ads.created_at DESC";

// Prepare & execute
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for filter dropdown
$catStmt = $conn->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Search Results</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . "/../includes/navbar.php"; ?>

<!-- Search Form -->
<section class="container py-5">
    <h2 class="mb-4 text-center">Search Ads</h2>

    <form method="GET" action="search.php" class="row g-3 justify-content-center">
        <div class="col-md-4">
            <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>"
                   class="form-control" placeholder="Search by title or description">
        </div>

        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id']; ?>"
                        <?= ($category == $cat['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-warning">Search</button>
        </div>
    </form>
</section>

<!-- Search Results -->
<section class="container pb-5" id="search-results">
    <h3 class="mb-4">Results</h3>

    <?php if (count($ads) === 0): ?>
        <div class="alert alert-info">
            No ads found matching your search.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($ads as $ad): ?>
                <div class="col-md-4">
                    <div class="card ad-card h-100 shadow-sm">
                        <img src="../assets/uploads/<?= htmlspecialchars($ad['main_image']); ?>"
                             class="card-img-top"
                             alt="<?= htmlspecialchars($ad['title']); ?>">

                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-secondary mb-2">
                                <?= htmlspecialchars($ad['category_name']); ?>
                            </span>

                            <h5 class="card-title">
                                <?= htmlspecialchars($ad['title']); ?>
                            </h5>

                            <p class="card-text small text-muted">
                                <?= substr(htmlspecialchars($ad['description']), 0, 90); ?>...
                            </p>

                            <div class="mt-auto">
                                <p class="fw-bold text-success">
                                    €<?= number_format($ad['price'], 2); ?>
                                </p>

                                <a href="../ads/view.php?id=<?= $ad['id']; ?>"
                                   class="btn btn-outline-primary w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>

<!-- Auto Scroll to Results -->
<script>
    const hasSearch =
        new URLSearchParams(window.location.search).has('q') ||
        new URLSearchParams(window.location.search).has('category');

    if (hasSearch) {
        document.getElementById('search-results')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
