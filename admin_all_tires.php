<?php
session_name('system1_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'supplierdb_connect.php';

// Initialize variable to hold search query
$searchQuery = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    // Sanitize user input to prevent SQL injection
    $searchQuery = htmlspecialchars($_POST['search_query']);
    
    // Store search query in session
    $_SESSION['search_query'] = $searchQuery;
    
    try {
        // Fetch tires based on search query
        $stmt = $conn->prepare("
            SELECT *
            FROM tires
            WHERE item_name LIKE :searchQuery
            OR brand LIKE :searchQuery
            OR description LIKE :searchQuery
        ");
        $searchParam = "%{$searchQuery}%";
        $stmt->bindParam(':searchQuery', $searchParam, PDO::PARAM_STR);
        $stmt->execute();
        $tires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Fetch all tires if no search query or on initial load
    try {
        $stmt = $conn->prepare("SELECT * FROM tires");
        $stmt->execute();
        $tires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tires</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include 'admin_header.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">Home</a>
            <a class="navbar-brand" href="admin_all_tires.php">All Tires</a>
            <a class="navbar-brand" href="admin_add_tires.php">Add Tires</a>
            <a class="navbar-brand" href="admin_user_history_orders.php">See Customer Order Log</a>
            <a class="navbar-brand" href="admin_user_list.php">List of Customers</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Tire Types
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="admin_tire_type_PCT.php">PCT</a>
                            <a class="dropdown-item" href="admin_tire_type_TSUVT.php">TSUVT</a>
                            <a class="dropdown-item" href="admin_tire_type_PT.php">PT</a>
                            <a class="dropdown-item" href="admin_tire_type_ST.php">ST</a>
                            <a class="dropdown-item" href="admin_tire_type_CHDT.php">CHDT</a>
                            <a class="dropdown-item" href="admin_tire_type_ORAT.php">ORAT</a>
                        </div>
                    </li>
                </ul>
            </div>
        
            <form class="form-inline my-2 my-lg-0 mr-2" method="POST" action="">
                <input  class="form-control mr-sm-2" type="search" placeholder="Search for anything" aria-label="Search" name="search_query">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Search</button>
            </form>
        </div>
    </nav>

<!-- Main Content -->
<main class="container mt-4">
    <h2>All Tires</h2>
    <a href="download.php<?php echo (!empty($searchQuery) ? '?search_query=' . urlencode($searchQuery) : ''); ?>" class="btn btn-success">Download</a>
    <div class="row">
        <?php if (count($tires) > 0): ?>
            <?php foreach ($tires as $tire): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <img class="card-img-top" src="<?php echo htmlspecialchars($tire['image']); ?>" alt="<?php echo htmlspecialchars($tire['item_name']); ?>">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($tire['item_name']); ?></h4>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($tire['brand']); ?></h6>
                            <p class="card-text"><?php echo htmlspecialchars($tire['description']); ?></p>
                            <h5 class="card-text">Price: $<?php echo htmlspecialchars($tire['price']); ?></h5>
                            <p class="card-text">Available: <?php echo htmlspecialchars($tire['amount']); ?></p>
                        </div>
                        <div class="card-footer">
                            <!--to edit the product-->
                            <a href="admin_edit_tires.php?id=<?php echo $tire['id']; ?>" class="btn btn-primary">Edit</a>
                            <!--to delete the product-->
                            <a href="admin_delete_tires.php?id=<?php echo $tire['id']; ?>" class="btn btn-danger delete-button">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-lg-12">
                <p>No tires found.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    let deleteButtons = document.querySelectorAll('.delete-button');
    let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let confirmDeleteButton = document.getElementById('confirmDelete');
    let deleteUrl = '';

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            deleteUrl = event.target.getAttribute('href');
            deleteModal.show();
        });
    });

    confirmDeleteButton.addEventListener('click', () => {
        window.location.href = deleteUrl;
    });
});
</script>

</body>
</html>
