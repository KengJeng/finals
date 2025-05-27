<?php
require("db_connect.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // First, delete from ootd where clothes_id = $id
    $delOotd = $conn->prepare("DELETE FROM ootd WHERE clothes_id = :id");
    $delOotd->execute([':id' => $id]);

    // Then, delete from tblclothes
    $stmt = $conn->prepare("DELETE FROM tblclothes WHERE id = :id");
    if ($stmt->execute([':id' => $id])) {
        header('Location: index.php');
        exit;
    } else {
        echo "Failed to delete. Error: ";
        print_r($stmt->errorInfo());
    }
} else {
    echo "Invalid ID.";
}
?>