<?php
session_name('system2_session');
session_start();

// Check if user is logged in as customer
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: customer_login.php");
    exit();
}

include 'retailerdb_connect.php';

$added_to_cart = false; // Initialize variable to track if item was added to cart

try {
    // Fetch PCT tires data
    if (isset($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%';
        $stmt = $conn->prepare("SELECT * FROM tires WHERE type = 'ORAT' AND (item_name LIKE :search_term OR brand LIKE :search_term)");
        $stmt->bindParam(':search_term', $search_term);
    } else {
        $stmt = $conn->prepare("SELECT * FROM tires WHERE type = 'ORAT'");
    }
    $stmt->execute();
    $pct_tires = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle adding to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $tire_id = $_POST['tire_id'];
    $user_email = $_SESSION['email'];

    try {
        $stmt = $conn->prepare("INSERT INTO cart (user_email, tire_id, amount) VALUES (:user_email, :tire_id, 1)");
        $stmt->bindParam(':user_email', $user_email);
        $stmt->bindParam(':tire_id', $tire_id);
        $stmt->execute();
        $added_to_cart = true; // Set variable to true when item is added to cart
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORAT Tires</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar (Include as needed) -->
<?php include 'customer_header.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="customer_dashboard.php">Home</a>
            <a class="navbar-brand" href="customer_all_tires.php">All Tires</a>
            <a class="navbar-brand" href="customer_my_order_logs.php">My Order Logs</a>
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
                            <a class="dropdown-item" href="customer_tire_type_PCT.php">PCT</a>
                            <a class="dropdown-item" href="customer_tire_type_TSUVT.php">TSUVT</a>
                            <a class="dropdown-item" href="customer_tire_type_PT.php">PT</a>
                            <a class="dropdown-item" href="customer_tire_type_ST.php">ST</a>
                            <a class="dropdown-item" href="customer_tire_type_CHDT.php">CHDT</a>
                            <a class="dropdown-item" href="customer_tire_type_ORAT.php">ORAT</a>
                        </div>
                    </li>
                </ul>
            </div>
            
            <form class="form-inline my-2 my-lg-0" method="GET" action="">
                <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search for anything" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

<div class="container mt-4">
    <h2>ORAT Tires</h2>

    <div class="row">
        <?php if (empty($pct_tires)): ?>
            <p>No tires found matching your search criteria.</p>
        <?php else: ?>
            <?php foreach ($pct_tires as $tire): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <img class="card-img-top" src="<?php echo htmlspecialchars($tire['image']); ?>" alt="<?php echo htmlspecialchars($tire['item_name']); ?>">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($tire['item_name']); ?></h4>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($tire['brand']); ?></h6>
                            <p class="card-text"><?php echo htmlspecialchars($tire['description']); ?></p>
                            <h5 class="card-text">Price: $<?php echo htmlspecialchars($tire['price']); ?></h5>
                            <p class="card-text">Amount Available: <?php echo htmlspecialchars($tire['amount']); ?></p>
                        </div>
                        <div class="card-footer">
                            <form method="post" action="">
                                <input type="hidden" name="tire_id" value="<?php echo htmlspecialchars($tire['id']); ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Added to Cart</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                The item has been added to your cart.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php if ($added_to_cart): ?>
<script>
    $(document).ready(function() {
        $('#cartModal').modal('show');
    });
</script>
<?php endif; ?>
<?php include 'footer.php'; ?>

</body>
</html>
