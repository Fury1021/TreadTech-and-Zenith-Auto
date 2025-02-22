<?php

session_name('system2_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'retailerdb_connect.php';

// Fetch cart items for display on checkout page
try {
    $user_email = $_SESSION['email'];
    $stmt = $conn->prepare("
        SELECT t.*, c.id as cart_id, c.amount as cart_amount
        FROM cart c
        JOIN tires t ON c.tire_id = t.id
        WHERE c.user_email = :user_email
    ");
    $stmt->bindParam(':user_email', $user_email);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle checkout process
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    // Calculate total price
    $total_price = 0;
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['cart_amount'];
    }

    // Begin transaction
    $conn->beginTransaction();
    try {
        // Insert into orders table
        $created_at = date('Y-m-d H:i:s'); // Current timestamp
        $stmt = $conn->prepare("
            INSERT INTO orders (user_email, total_price, created_at)
            VALUES (:user_email, :total_price, :created_at)
        ");
        $stmt->bindParam(':user_email', $user_email);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->execute();

        // Update tire amounts
        foreach ($cart_items as $item) {
            $stmt = $conn->prepare("
                UPDATE tires
                SET amount = amount - :cart_amount
                WHERE id = :tire_id
            ");
            $stmt->bindParam(':cart_amount', $item['cart_amount']);
            $stmt->bindParam(':tire_id', $item['id']);
            $stmt->execute();
        }

        // Clear the cart after checkout
        clearCart($conn, $user_email);

        // Commit transaction
        $conn->commit();

        header("Location: customer_view_cart.php");
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo "Error saving order: " . $e->getMessage();
    }
}

// Function to clear the cart after checkout
function clearCart($conn, $user_email) {
    try {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_email = :user_email");
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error clearing cart: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
            
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search for anything" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>

<div class="container mt-4">
    <h2>Checkout</h2>
    <div class="row">
        <?php if (count($cart_items) > 0): ?>
        <?php foreach ($cart_items as $item): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <img class="card-img-top" src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h4>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($item['brand']); ?></h6>
                    <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                    <h5 class="card-text">Price: $<?php echo htmlspecialchars($item['price']); ?></h5>
                    <p class="card-text">Quantity: <?php echo htmlspecialchars($item['cart_amount']); ?></p>
                    <p class="card-text">Available: <?php echo htmlspecialchars($item['amount']); ?></p>
                    <p class="card-text">Total: $<?php echo htmlspecialchars($item['price'] * $item['cart_amount']); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="col-lg-12 mt-4">
            <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'checkout.php'; ?>" class="btn btn-secondary mr-2">Back</a>
            <form method="post" action="">
                <button type="submit" name="checkout" class="btn btn-primary">Confirm Checkout</button>
            </form>
        </div>
        <?php else: ?>
        <div class="col-lg-12">
            <p>Your cart is empty.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
