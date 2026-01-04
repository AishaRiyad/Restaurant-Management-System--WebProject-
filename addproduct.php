<?php
session_start();


$title = "";
$description = "";
$price = "";


if (isset($_POST['submit'])) {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];


    $db = new mysqli('localhost', 'root', '', 'restaurant');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }


    $check_stmt = $db->prepare("SELECT COUNT(*) FROM `products` WHERE `title` = ?");
    $check_stmt->bind_param("s", $title);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $_SESSION['add_error'] = "A product with the name '$title' already exists.";

        header("Location: addproduct.php");
        exit;
    }


    $image_name = "";
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $image_name = $_FILES['image']['name'];
        $random_num = rand(000, 999);
        $image_name = "Food_menu_" . $random_num . '.' . pathinfo($image_name, PATHINFO_EXTENSION);

        $upload_dir = "../images/productimg/";
        $destination_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination_path)) {

        } else {
            $_SESSION['upload'] = "<div class='error'>Product image upload failed.</div>";
            header("Location: addproduct.php");
            exit;
        }
    } else {
        $_SESSION['upload'] = "<div class='error'>Please select an image.</div>";
        header("Location: addproduct.php");
        exit;
    }


    $stmt = $db->prepare("INSERT INTO `products` (`title`, `description`, `price`, `proimage`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $price, $image_name);
    if ($stmt->execute()) {
        $_SESSION['add'] = "<div class='success'>Product added successfully.</div>";
        header("Location: foodmenu.php");
        exit;
    } else {
        $_SESSION['add'] = "<div class='error'>Product failed to be added.</div>";
        header("Location: addproduct.php");
        exit;
    }

    $stmt->close();
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Snap ITC", sans-serif;
            color: deeppink;
            background:url("images/stra.jpg");
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
    </style>
</head>
<body>
<div class="header">
    <header>

        <a href="adminwelcomep.html" style="color: red;font-size:27px;">Home</a>
        <a href="foodmenu.php" style="color: red;font-size:27px;">Food menu</i></a>

    </header>
</div>
<div class="container">
    <div class="form-container">

        <h1>Add Product</h1>


        <!-- Display error message if title already exists -->
        <?php if (isset($_SESSION['add_error'])): ?>
            <div class="error"><?php echo $_SESSION['add_error']; ?></div>
            <?php unset($_SESSION['add_error']); ?>
        <?php endif; ?>

        <form action="addproduct.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Product name" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Select Image:</label>
                <input type="file" id="image" name="image" required>
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
                <button type="submit" name="submit" class="btn">Add Product</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>




<?php
//session_start();
//?>
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <title>Product</title>-->
<!--</head>-->
<!--<body>-->
<!---->
<!---->
<!---->
<!--<div class="main-content">-->
<!--    <div class="wrapper">-->
<!--        <h1>ADD Product</h1>-->
<!--        <br><br>-->
<!---->
<!--        <form action="addproduct.php" method="post" enctype="multipart/form-data">-->
<!--            <table class="tbl-30">-->
<!--                <tr>-->
<!--                    <td>Title:</td>-->
<!--                    <td>-->
<!--                        <input type="text" name="title" placeholder="product name">-->
<!--                    </td>-->
<!---->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td>Select Image:</td>-->
<!--                    <td><input type="file" name="image"></td>-->
<!---->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td>Description</td>-->
<!--                    <td><input type="text" name="description" placeholder="Description"></td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td>Price</td>-->
<!--                    <td><input type="number" name="price" placeholder="price"></td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <td colspan="2">-->
<!--                        <input type="submit" name="submit" value="Add product" class="btn-secondary">-->
<!--                    </td>-->
<!--                </tr>-->
<!---->
<!--            </table>-->
<!--        </form>-->
<!--    </div>-->
<!--</div>-->
<!--</body>-->
<!--</html>-->
<!---->
<!---->
<?php
//if (isset($_POST['submit'])) {
//    $title = $_POST['title'];
//    $descreption = $_POST['description'];
//    $price = $_POST['price'];
//    $db = new mysqli('localhost', 'root', '', 'restaurant');
//
//    if (isset($_FILES['image']['name'])) {
//        $image_name = $_FILES['image']['name'];
//
//        $image_parts = explode('.', $image_name);
//        $ext = end($image_parts);
//        $random_num = rand(000, 999);
//        $image_name = "Food_menu_" . $random_num . '.' . $ext;
//
//
//        $source_path = $_FILES['image']['tmp_name'];
//
//
//        $upload_dir = "../images/productimg/";
//        if (!file_exists($upload_dir)) {
//            mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist
//        }
//
//        $destination_path = $upload_dir . $image_name;
//        $upload = move_uploaded_file($source_path, $destination_path);
//        if ($upload == false) {
//            $_SESSION['upload'] = "<div class='error'>Product failed to be added successfully.</div>";
//        }
//
//    } else {
//        $image_name = "";
//    }
//
//
//    $sql = "INSERT INTO `products` (`title`, `description`, `price`,`proimage`) VALUES ('" . $title . "', '" . $descreption . "', '" . $price . "','" . $image_name . "')";
//    $result = mysqli_query($db, $sql);
//    if ($result == 1) {
//header("Location:foodmenu.php");
////        $_SESSION['add'] = "<div class='success'>Product added successfully.</div>";
//    } else {
//
//        $_SESSION['add'] = "<div class='success'>Product Failed to added successfully.</div>";
//    }
//}
//// $items_sql = "SELECT * FROM `products` ";
////    $items_result = mysqli_query($db, $items_sql);
////    if ($items_result) {
////
////        while ($row = mysqli_fetch_assoc($items_result)) {
////            $food_id = $row['id'];
////            $food_title = $row['title'];
////            $food_description = $row['description'];
////            $food_img_path = $row['proimage'];
////            $food_price = $row['price'];
////            echo "<body id='body2'><section class='pop' id='pop'><div class='header'> <header>-->
////         <h1 id='he2'  style='margin-left: 10px'>Our <span>Popular</span> Types</h1> <a href='adminproduct.html' class='order-btn' STYLE='font-size:20px;font-family: 'Snap ITC'>ADD</a></header></div><div  class='bxxcont' style='margin-top: 70px'><div  class='bxx '>-->
////                  <img width='500px'; height='400px'; style='' src='../images/productimg/" . $food_img_path . "'>-->
////                       <div class='details'>-->
////                          <div class='details-sub'>-->
////                           <h1> $food_title</h1>-->
////                           <h5 class='cost'>$food_price</h5>-->
////                          </div>-->
////                        <p>-->
////                          '$food_description'-->
////                         </p>-->
////                    <form action='' method='POST'>-->
////                                 <input type='hidden' name='delete_id' >-->
////                                  <input type='submit' name='deletebtn' value='Delete'>-->
////                            </form>-->
////                     </div>-->
////                   </div>-->
////                  </div>-->
////                      </div></section></body>";
////        }
////    }
//?>
<!---->
