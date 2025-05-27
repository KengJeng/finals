<?php
require 'db_connect.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Handle delete
if (isset($_POST['delete'])) {
    // First, delete from ootd where clothes_id = ?
    $delOotd = $conn->prepare("DELETE FROM ootd WHERE clothes_id=?");
    $delOotd->execute([$id]);

    // Then, delete from tblclothes
    $del = $conn->prepare("DELETE FROM tblclothes WHERE id=?");
    $del->execute([$id]);
    header('Location: index.php');
    exit;
}

// Fetch item
$stmt = $conn->prepare("SELECT * FROM tblclothes WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "Item not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $price = trim($_POST['price']);
    $image = $item['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgName = basename($_FILES['image']['name']);
        $target = "uploads/" . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $imgName;
        }
    }

    $update = $conn->prepare("UPDATE tblclothes SET name=?, type=?, price=?, image=? WHERE id=?");
    $update->execute([$name, $type, $price, $image, $id]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="edit-wrapper">
  <div class="edit-image-col">
    <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="" class="edit-image">
  </div>
  <div class="edit_clothes">
    <h2>Edit Clothes</h2>
    <form method="post" enctype="multipart/form-data">
      <label for="name">Name:</label>
      <input class="form-control" type="text" name="name" id="name" value="<?php echo htmlspecialchars($item['name']); ?>" required><br>
      <label for="type">Type:</label>
      <select class="form-control" id="type" name="type" required>
        <option value="">Select</option>
        <option value="Top" <?php if($item['type']=='Top') echo 'selected'; ?>>Top</option>
        <option value="Bottom" <?php if($item['type']=='Bottom') echo 'selected'; ?>>Bottom</option>
        <option value="Outerwear" <?php if($item['type']=='Outerwear') echo 'selected'; ?>>Outerwear</option>
        <option value="Shoes" <?php if($item['type']=='Shoes') echo 'selected'; ?>>Shoes</option>
        <option value="Accessories" <?php if($item['type']=='Accessories') echo 'selected'; ?>>Accessories</option>
      </select><br>
      <label for="price">Price:</label>
      <input class="form-control" type="text" name="price" id="price" value="<?php echo htmlspecialchars($item['price']); ?>" required><br>
      <label for="image">Change Image (optional):</label>
      <input class="form-control" type="file" name="image" id="image" accept="image/*"><br>
      <div style="display: flex; gap: 4%; margin-bottom: 10px;">
        <button class="btn btn-success" type="submit" style="width:48%;">Update</button>
        <button type="submit" name="delete" class="btn btn-danger" style="width:48%;">Delete</button>
      </div>
      <a href="index.php" class="btn btn-secondary" style="width:100%;text-align:center;display:inline-block;text-decoration:none;">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>