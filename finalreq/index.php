<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Closet</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body>

    <h1 class="main-title">✪ Digital Closet ✪</h1>
    <div class="top-sections">
        <div class="main">
            <form action="add.php" method="post" enctype="multipart/form-data">
                <center>
                    <h2>Add Clothes</h2>
                </center>
                <label>Name:</label>
                <input type="text" name="name" id="name" required><br><br>
                <label>Type:</label>
                <select id="type" name="type">
                    <option value="">-- Choose a Type --</option>
                    <option value="Top">Top</option>
                    <option value="Bottom">Bottom</option>
                    <option value="Outerwear">Outerwear</option>
                    <option value="Shoes">Shoes</option>
                    <option value="Accessories">Accessories</option>
                </select><br><br>
                <label>Price:</label>
                <input type="text" name="price" id="price" required><br><br>
                <label>Choose Image:</label>
                <input type="file" name="image" id="image" accept="image/*" required><br><br>
                <input type="submit" name="upload" value="Upload">
            </form>
        </div>

        <!-- OOTD Section-->
        <div class="ootd-section">
            <h2 style="text-align:center;">Outfit of the Day</h2>
            <form method="post" action="" id="ootdForm">
                <label for="ootd_date">Pick a date:</label>
                <input type="date" name="ootd_date" id="ootd_date"
                    value="<?php echo isset($_POST['ootd_date']) ? $_POST['ootd_date'] : $today; ?>"
                    required onchange="document.getElementById('ootdForm').submit();">
                <?php
                require 'db_connect.php';
                $today = date('Y-m-d');

                if (isset($_POST['remove_ootd'])) {
                    $ootd_date = $_POST['ootd_date'];
                    $clothes_id = intval($_POST['clothes_id']);
                    $del = $conn->prepare("DELETE FROM ootd WHERE ootd_date=? AND clothes_id=?");
                    $del->execute([$ootd_date, $clothes_id]);
                }
                ?>
                <?php
                if (isset($_POST['save_ootd'])) {
                    $ootd_date = $_POST['ootd_date'];
                    $clothes_id = intval($_POST['ootd_clothes']);
                    // Prevent duplicate for the same date and item
                    $exists = $conn->prepare("SELECT * FROM ootd WHERE ootd_date=? AND clothes_id=?");
                    $exists->execute([$ootd_date, $clothes_id]);
                    if ($exists->rowCount() == 0) {
                        $stmt = $conn->prepare("INSERT INTO ootd (ootd_date, clothes_id) VALUES (?, ?)");
                        $stmt->execute([$ootd_date, $clothes_id]);
                        echo "<div style='color:green;'>OOTD saved!</div>";
                    } else {
                        echo "<div style='color:orange;'>Already added for this date.</div>";
                    }
                }
                ?>
                <label for="ootd_clothes">Select Clothes:</label>
                <select name="ootd_clothes" id="ootd_clothes" required>
                    <option value="">-- Choose from Closet --</option>
                    <?php
                    $clothes = $conn->query("SELECT * FROM tblclothes");
                    while ($item = $clothes->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$item['id']}'>{$item['name']} ({$item['type']})</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="save_ootd" class="btn btn-primary btn-sm">Save OOTD</button>
            </form>
            <?php
            // Show OOTD for selected date (default: today)
            $show_date = isset($_POST['ootd_date']) ? $_POST['ootd_date'] : $today;
            $ootd = $conn->prepare(
                "SELECT tblclothes.* FROM ootd JOIN tblclothes ON ootd.clothes_id=tblclothes.id WHERE ootd.ootd_date=?"
            );
            $ootd->execute([$show_date]);
            if ($ootd->rowCount() > 0) {
                echo "<h4 style='margin-top:20px;'>Outfit for " . date('F j, Y', strtotime($show_date)) . ":</h4>";
                echo "<div style='display:flex;gap:20px;flex-wrap:wrap;'>";
                while ($item = $ootd->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='closet-item'>

                            <img src='uploads/{$item['image']}' alt='{$item['name']}'>
                            <form method='post' style='margin-top:5px;'>
                                <input type='hidden' name='ootd_date' value='" . htmlspecialchars($show_date) . "'>
                                <button type='submit' name='remove_ootd' class='remove-x-btn' title='Remove'></button>
                                <input type='hidden' name='clothes_id' value='{$item['id']}'>
                            </form>
                          </div>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <div class="utility-bar">
        <div class="total-price">
            <?php
            $total = $conn->query("SELECT SUM(price) AS total_price FROM tblclothes")->fetch(PDO::FETCH_ASSOC);
            echo "Total Price: ₱" . number_format($total['total_price'] ?? 0, 2);
            ?>
        </div>
        <form method="get" style="margin:0; display:flex; gap:10px; align-items:center;">
            <input class="search-bar" type="text" name="search" placeholder="Search..."
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <select name="type_filter" class="type-filter">
                <option value="">All Types</option>
                <option value="Top" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='Top') echo 'selected'; ?>>Top</option>
                <option value="Bottom" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='Bottom') echo 'selected'; ?>>Bottom</option>
                <option value="Outerwear" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='Outerwear') echo 'selected'; ?>>Outerwear</option>
                <option value="Shoes" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='Shoes') echo 'selected'; ?>>Shoes</option>
                <option value="Accessories" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='Accessories') echo 'selected'; ?>>Accessories</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm filter-btn">Filter</button>
        </form>
    </div>
    <?php
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $type_filter = isset($_GET['type_filter']) ? trim($_GET['type_filter']) : '';
    if ($search !== '' && $type_filter !== '') {
        $stmt = $conn->prepare("SELECT * FROM tblclothes WHERE (name LIKE :search OR type LIKE :search) AND type = :type");
        $stmt->execute(['search' => "%$search%", 'type' => $type_filter]);
    } elseif ($search !== '') {
        $stmt = $conn->prepare("SELECT * FROM tblclothes WHERE name LIKE :search OR type LIKE :search");
        $stmt->execute(['search' => "%$search%"]);
    } elseif ($type_filter !== '') {
        $stmt = $conn->prepare("SELECT * FROM tblclothes WHERE type = :type");
        $stmt->execute(['type' => $type_filter]);
    } else {
        $stmt = $conn->query("SELECT * FROM tblclothes");
    }
    ?>
    <div class="container">
        <div class="closet-grid">
            <?php
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "
        <a href='edit.php?id={$row['id']}' style='text-decoration:none;color:inherit;'>
            <div class='closet-item' style='cursor:pointer;'>
                <img src='uploads/{$row['image']}' alt='{$row['name']}'>
                <div class='item-info'>{$row['name']}</div>
                <div class='item-info' style='font-size:14px;color:#888;'>{$row['type']} | ₱{$row['price']}</div>
            </div>
        </a>
    ";
            }
            ?>
        </div>
    </div>
</body>

</html>