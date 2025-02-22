<?php
session_name('system1_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'retailerdb_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['csv_file'])) {
    $fileName = $_FILES['csv_file']['tmp_name'];

    if ($_FILES['csv_file']['size'] > 0) {
        $file = fopen($fileName, "r");

        // Skip the header row
        fgetcsv($file);

        try {
            // Begin a transaction
            $conn->beginTransaction();

            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                $stmt = $conn->prepare("
                    INSERT INTO tires (id, item_name, type, price, description, brand, amount, image) 
                    VALUES (:id, :item_name, :type, :price, :description, :brand, :amount, :image)
                    ON DUPLICATE KEY UPDATE
                        item_name = VALUES(item_name),
                        type = VALUES(type),
                        price = VALUES(price),
                        description = VALUES(description),
                        brand = VALUES(brand),
                        amount = VALUES(amount),
                        image = VALUES(image)
                ");

                $stmt->bindParam(':id', $column[0], PDO::PARAM_INT);
                $stmt->bindParam(':item_name', $column[1], PDO::PARAM_STR);
                $stmt->bindParam(':type', $column[2], PDO::PARAM_STR);
                $stmt->bindParam(':price', $column[3], PDO::PARAM_STR);
                $stmt->bindParam(':description', $column[4], PDO::PARAM_STR);
                $stmt->bindParam(':brand', $column[5], PDO::PARAM_STR);
                $stmt->bindParam(':amount', $column[6], PDO::PARAM_INT);
                $stmt->bindParam(':image', $column[7], PDO::PARAM_STR);

                $stmt->execute();
            }

            // Commit the transaction
            $conn->commit();

            $_SESSION['message'] = "CSV file successfully uploaded.";
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $conn->rollBack();
            $_SESSION['error'] = "Error uploading CSV file: " . $e->getMessage();
        }

        fclose($file);
    }
    header("Location: customer_all_tires.php");
    exit();
} else {
    $_SESSION['error'] = "Please upload a valid CSV file.";
    header("Location: customer_all_tires.php");
    exit();
}
?>
