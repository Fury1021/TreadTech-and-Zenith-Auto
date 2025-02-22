<?php
include 'supplierdb_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    
    // Handle profile picture upload
    $target_dir = "../storage/profile_pictures/";
    $profilepicture = basename($_FILES["profilepicture"]["name"]);
    $target_file = $target_dir . $profilepicture;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profilepicture"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profilepicture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" 
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profilepicture"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["profilepicture"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    if ($uploadOk == 1) {
        if ($role == 'admin') {
            $stmt = $conn->prepare("INSERT INTO admins (firstname, lastname, email, password, contact_number, address, role, profilepicture) VALUES (:firstname, :lastname, :email, :password, :contact_number, :address, :role, :profilepicture)");
        } else if ($role == 'admin{
            $stmt = $conn->prepare("INSERT INTO admins (firstname, lastname, email, password, contact_number, address, role, profilepicture) VALUES (:firstname, :lastname, :email, :password, :contact_number, :address, :role, :profilepicture)");
        } else {
            echo "Invalid role specified.";
            exit;
        }

        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':profilepicture', $profilepicture);

        if ($stmt->execute()) {
            if ($role == 'admin') {
                header("Location: admin_login.php");
            } else if ($role == 'admin') {
                header("Location: admin_login.php");
            }
            exit;
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}
?>
