<?php

session_name('system2_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: customer_login.php");
    exit();
}

include 'retailerdb_connect.php';

// Initialize variables
$orders = []; // Initialize an empty array to hold orders

// Fetch all orders for the logged-in customer or filtered by search query
try {
    $user_email = $_SESSION['email'];
    
    if (isset($_GET['search'])) {
        $search_term = '%' . $_GET['search'] . '%'; // Get search term and prepare for SQL LIKE query
        $stmt = $conn->prepare("
            SELECT id, total_price, created_at
            FROM orders
            WHERE user_email = :user_email AND (id LIKE :search_term OR total_price LIKE :search_term OR created_at LIKE :search_term)
            ORDER BY created_at DESC
        ");
        $stmt->bindParam(':search_term', $search_term);
    } else {
        $stmt = $conn->prepare("
            SELECT id, total_price, created_at
            FROM orders
            WHERE user_email = :user_email
            ORDER BY created_at DESC
        ");
    }
    
    $stmt->bindParam(':user_email', $user_email);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all matching orders
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order Logs</title>
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
            
            <!-- Search form -->
            <form class="form-inline my-2 my-lg-0" method="get" action="">
                <input class="form-control mr-sm-2" type="search" placeholder="Search for anything" aria-label="Search" name="search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

<!-- Main Content -->
<div class="container mt-4">
    <h2>My Order Logs</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Total Price</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td>$<?php echo htmlspecialchars($order['total_price']); ?></td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
