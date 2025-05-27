<?php
require("db_connect.php");
if(isset($_POST['upload'])){
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $image = $_FILES['image'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image["name"]);

    if(move_uploaded_file($image["tmp_name"], $target_file)){
        $stmt = $conn->prepare("INSERT INTO tblclothes (name, type, price, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $type, $price, $image["name"]]);
        echo "Product added successfully!";
    } else {
        echo "Failed to upload image.";
    }
}
header("location:index.php");
?>