<?php
session_start();

$title = "";
$description = "";
$price = "";
$image_name = "";

if (isset($_GET['edit_id'])){
    $_SESSION['edit_id'] = $_GET['edit_id'];
}

if (isset($_SESSION['edit_id'])) {
    $edit_id = $_SESSION['edit_id'];

    $db = new mysqli('localhost', 'root', '', 'restaurant');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $stmt = $db->prepare("SELECT * FROM `products` WHERE `title` = ?");
    $stmt->bind_param("s", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        $title = $product['title'];
        $description = $product['description'];
        $price = $product['price'];
        $image_name = $product['proimage'];
    } else {
        $_SESSION['edit_error'] = "Product not found.";
        header("Location: foodmenu.php");
        exit;
    }
}

if (isset($_POST['updatebtn'])) {
    $original_title = $_POST['original_title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $new_image_name = $image_name;

    $db = new mysqli('localhost', 'root', '', 'restaurant');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $new_image_name = $_FILES['image']['name'];
        $random_num = rand(000, 999);
        $new_image_name = "Food_menu_" . $random_num . '.' . pathinfo($new_image_name, PATHINFO_EXTENSION);

        $upload_dir = "../images/productimg/";
        $destination_path = $upload_dir . $new_image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination_path)) {
            $_SESSION['upload'] = "<div class='error'>Product image upload failed.</div>";
            header("Location: editproduct.php");
            exit;
        }
    }

    $stmt = $db->prepare("UPDATE `products` SET `description`=?, `price`=?, `proimage`=? WHERE `title`=?");
    $stmt->bind_param("sdss", $description, $price, $new_image_name, $original_title);
    $res = $stmt->execute();
    $stmt->close();

    if ($res) {
        echo "<script>alert('Product updated successfully');</script>";
        echo "<script>window.location = 'foodmenu.php';</script>";
    } else {
        echo "<script>alert('Error updating product');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Snap ITC", sans-serif;
            color: deeppink;
            background: url("images/stra.jpg");
        }
        .container {
            max-width: 600px;
            margin: 110px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 3px solid darkred;
            border-radius: 15px;
            font-size: 14px;
            font-family: "Snap ITC";
        }
        .btn {
            background-color: pink;
            color: red;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 25px;
            font-family: "Snap ITC";
            margin-top: 10px;
            margin-left: 150px;
            cursor: pointer;
            border-radius: 15px;
        }
        .btn:hover {
            background-color: deeppink;
            color: white;
        }
        .error {
            color: red;
            margin-top: 5px;
        }
        .success {
            color: green;
            margin-top: 5px;
        }
        .image-preview {
            margin-top: 10px;
        }
        .image-preview img {
            max-width: 100%;
            border: 3px solid darkred;
            border-radius: 15px;
        }
    </style>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imagePreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function confirmUpdate(event) {
            if (confirm("Are you sure you want to update this product?")) {
                document.getElementById('editForm').submit();
            } else {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
<header>

    <a href="adminwelcomep.html" style="color: red;font-size:27px;">Home</a>
    <a href="foodmenu.php" style="color: red;font-size:27px;">Food menu</i></a>

</header>
<div class="container">
    <div class="form-container">
        <h1>Edit Product</h1>

        <!-- Display error message if title already exists -->
        <?php if (isset($_SESSION['edit_error'])): ?>
            <div class="error"><?php echo $_SESSION['edit_error']; ?></div>
            <?php unset($_SESSION['edit_error']); ?>
        <?php endif; ?>

        <form id="editForm" action="editproduct.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Product name" value="<?php echo htmlspecialchars($title); ?>" disabled>
                <input type="hidden" name="original_title" value="<?php echo htmlspecialchars($title); ?>">
            </div>
            <div class="form-group">
                <label for="image">Select Image:</label>
                <input type="file" id="image" name="image" onchange="previewImage(event)">
                <div class="image-preview">
                    <p>Current Image:</p>
                    <?php if ($image_name): ?>
                        <img id="imagePreview" src="../images/productimg/<?php echo htmlspecialchars($image_name); ?>" alt="Current Image">
                    <?php else: ?>
                        <p>No image available.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" placeholder="Description" value="<?php echo htmlspecialchars($description); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" placeholder="Price" value="<?php echo htmlspecialchars($price); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="updatebtn" class="btn" onclick="confirmUpdate(event)">Edit Product</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>







<!---->
<!---->
<?php
//session_start();
//
//$title = "";
//$description = "";
//$price = "";
//$image_name = "";
//
//if (isset($_GET['edit_id'])){
//    $edit_id = $_GET['edit_id'];
////    $title = $_POST['title'];
////    $description = $_POST['description'];
////    $price = $_POST['price'];
//    echo 'hi';
//    $db = new mysqli('localhost', 'root', '', 'restaurant');
//    if ($db->connect_error) {
//        die("Connection failed: " . $db->connect_error);
//    }
//
//    $stmt = $db->prepare("SELECT * FROM `products` WHERE `title` = ?");
//    $stmt->bind_param("s", $edit_id);
//    $stmt->execute();
//    $result = $stmt->get_result();
//    $product = $result->fetch_assoc();
//    $stmt->close();
//
//
//    if ($product) {
//        $title = $product['title'];
//        $description = $product['description'];
//        $price = $product['price'];
//        $image_name = $product['proimage'];
//    } else {
//        $_SESSION['edit_error'] = "Product not found.";
//        header("Location: foodmenu.php");
//        exit;
//    }
//
//    $image_name = "";
//    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
//        $image_name = $_FILES['image']['name'];
//        $random_num = rand(000, 999);
//        $image_name = "Food_menu_" . $random_num . '.' . pathinfo($image_name, PATHINFO_EXTENSION);
//
//        $upload_dir = "../images/productimg/";
//        $destination_path = $upload_dir . $image_name;
//
//        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination_path)) {
//
//        } else {
//            $_SESSION['upload'] = "<div class='error'>Product image upload failed.</div>";
//            header("Location: addproduct.php");
//            exit;
//        }
//    } else {
//        $_SESSION['upload'] = "<div class='error'>Please select an image.</div>";
//        header("Location: editproduct.php");
//        exit;
//    }
//}
//if (isset($_POST['updatebtn'])) {
//    $original_title = $_POST['original_title'];
//    $title = $_POST['title'];
//    $description = $_POST['description'];
//    $price = $_POST['price'];
//    $new_image_name = "";
//
//    $db = new mysqli('localhost', 'root', '', 'restaurant');
//    if ($db->connect_error) {
//        die("Connection failed: " . $db->connect_error);
//    }
//    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
//        $new_image_name = $_FILES['image']['name'];
//        $random_num = rand(000, 999);
//        $new_image_name = "Food_menu_" . $random_num . '.' . pathinfo($new_image_name, PATHINFO_EXTENSION);
//
//        $upload_dir = "../images/productimg/";
//        $destination_path = $upload_dir . $new_image_name;
//
//        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination_path)) {
//            $_SESSION['upload'] = "<div class='error'>Product image upload failed.</div>";
//            header("Location: editproduct.php");
//            exit;
//        }
//    } else {
//        $new_image_name = $image_name;
//    }
//    $stmt = $db->prepare("UPDATE `products` SET `title`=?, `description`=?, `price`=?, `proimage`=? WHERE `title`=?");
//    $stmt->bind_param("ssdss", $title, $description, $price, $new_image_name, $original_title);
//    $res = $stmt->execute();
//    echo $res;
//    $stmt->close();
//    if ($res) {
//        echo "<script>alert('Product updated successfully');</script>";
//        echo "<script>window.location = 'foodmenu.php';</script>";
//    } else {
//        echo "<script>alert('Error updating product');</script>";
//    }
//}
//
//
//?>
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>Edit Product</title>-->
<!--    <style>-->
<!---->
<!--        * {-->
<!--            margin: 0;-->
<!--            padding: 0;-->
<!--            box-sizing: border-box;-->
<!--        }-->
<!---->
<!--        body {-->
<!--            font-family: "Snap ITC", sans-serif;-->
<!--            color: deeppink;-->
<!--            background:url("images/stra.jpg");-->
<!--        }-->
<!---->
<!--        .container {-->
<!--            max-width: 600px;-->
<!--            margin: 110px auto;-->
<!--            background-color: #fff;-->
<!--            padding: 20px;-->
<!--            border-radius: 50px;-->
<!--            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);-->
<!--        }-->
<!---->
<!--        h1 {-->
<!--            text-align: center;-->
<!--            margin-bottom: 20px;-->
<!--        }-->
<!---->
<!--        .form-container {-->
<!--            padding: 20px;-->
<!--        }-->
<!---->
<!--        .form-group {-->
<!--            margin-bottom: 15px;-->
<!--        }-->
<!---->
<!--        label {-->
<!--            font-weight: bold;-->
<!--        }-->
<!---->
<!--        input[type="text"],-->
<!--        input[type="number"],-->
<!--        input[type="file"] {-->
<!--            width: calc(100% - 20px);-->
<!--            padding: 10px;-->
<!--            border: 3px solid darkred;-->
<!--            border-radius: 15px;-->
<!--            font-size: 14px;-->
<!--            font-family: "Snap ITC";-->
<!--        }-->
<!---->
<!--        .btn {-->
<!--            background-color: pink;-->
<!--            color: red;-->
<!--            border: none;-->
<!--            padding: 10px 20px;-->
<!--            text-align: center;-->
<!--            text-decoration: none;-->
<!--            display: inline-block;-->
<!--            font-size: 25px;-->
<!--            font-family: "Snap ITC";-->
<!--            margin-top: 10px;-->
<!--            margin-left: 150px;-->
<!--            cursor: pointer;-->
<!--            border-radius: 15px;-->
<!--        }-->
<!---->
<!--        .btn:hover {-->
<!--            background-color: deeppink;-->
<!--            color: white;-->
<!--        }-->
<!---->
<!--        .error {-->
<!--            color: red;-->
<!--            margin-top: 5px;-->
<!--        }-->
<!---->
<!--        .success {-->
<!--            color: green;-->
<!--            margin-top: 5px;-->
<!--        }-->
<!--    </style>-->
<!--</head>-->
<!--<body>-->
<!--<div class="container">-->
<!--    <div class="form-container">-->
<!--        <h1>Edit Product</h1>-->
<!---->
<!--        <!-- Display error message if title already exists -->-->
<!--        --><?php //if (isset($_SESSION['add_error'])): ?>
<!--            <div class="error">--><?php //echo $_SESSION['add_error']; ?><!--</div>-->
<!--            --><?php //unset($_SESSION['add_error']); ?>
<!--        --><?php //endif; ?>
<!---->
<!--        <form action="editproduct.php" method="post" enctype="multipart/form-data">-->
<!--            <div class="form-group">-->
<!--                <label for="title">Title:</label>-->
<!--                <input type="text" id="title" name="title" placeholder="Product name" value="--><?php //echo htmlspecialchars($title); ?><!--" required>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label for="image">Select Image:</label>-->
<!--                <input type="file" id="image" name="image" required>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label for="description">Description:</label>-->
<!--                <input type="text" id="description" name="description" placeholder="Description" value="--><?php //echo htmlspecialchars($description); ?><!--" required>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label for="price">Price:</label>-->
<!--                <input type="number" id="price" name="price" placeholder="Price" value="--><?php //echo htmlspecialchars($price); ?><!--" required>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <button type="submit" name="updatebtn" class="btn">Edit Product</button>-->
<!--            </div>-->
<!--        </form>-->
<!--    </div>-->
<!--</div>-->
<!--</body>-->
<!--</html>-->