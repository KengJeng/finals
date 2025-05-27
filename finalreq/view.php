<?php
require("db_connect.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM tblclothes WHERE id = :id";
    $result = $conn->prepare($sql);
    $result->execute([':id' => $id]);
    $item = $result->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo "<script>alert('Item not found'); window.location='index.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No valid Item ID provided'); window.location='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Clothes</title>
</head>

<body>
    <div>
        <table align="center" border="1" cellpadding="10">
            <tr>
                <td colspan="2"><strong>Clothes Details</strong></td>
            </tr>
            <tr>
                <td>Name:</td>
                <td><?= htmlspecialchars($item['name']); ?></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td><?= htmlspecialchars($item['type']); ?></td>
            </tr>
            <tr>
                <td>Price:</td>
                <td>₱<?= htmlspecialchars($item['price']); ?></td>
            </tr>
            <tr>
                <td>Image:</td>
                <td>
                    <img src="uploads/<?= htmlspecialchars($item['image']); ?>" width="150" height="150" alt="<?= htmlspecialchars($item['name']); ?>">
                </td>
            </tr>
        </table>
    </div>

    <div align="center" style="margin-top: 20px;" class="button">
        <a href="edit.php?id=<?= $item['id']; ?>" class="edit-btn">Edit</a>
        <a href="delete.php?id=<?= $item['id']; ?>" class="delete-btn">Delete</a>
        <br>
        <a href="index.php" class="back-btn">Back to List</a>
    </div>
</body>
</html>