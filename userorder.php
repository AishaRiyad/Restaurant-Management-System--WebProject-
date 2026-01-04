<?php
session_start();


$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

function escape($value)
{
    global $db;
    return mysqli_real_escape_string($db, $value);
}

function getProductByTitleAndUser($title, $username)
{
    global $db;
    $query = $db->prepare("SELECT * FROM `forder` WHERE `title` = ? AND `UserName` = ? AND `status` = 'initial'");
    $query->bind_param("ss", $title, $username);
    $query->execute();
    return $query->get_result();
}

function updateProductQuantity($quantity, $username, $title)
{
    global $db;
    $query = $db->prepare("UPDATE `forder` SET `quantity` = ? WHERE `UserName` = ? AND `title` = ? AND `status` = 'initial'");
    $query->bind_param("iss", $quantity, $username, $title);
    return $query->execute();
}

function insertNewOrder($title, $quantity, $username, $orderid)
{
    global $db;
    $query = $db->prepare("INSERT INTO `forder` (`title`, `quantity`, `UserName`, `order_id`, `status`) VALUES (?, ?, ?, ?, 'initial')");
    $query->bind_param("siss", $title, $quantity, $username, $orderid);
    return $query->execute();
}

if (isset($_GET['orderbtn'])) {
    $food_title = escape($_GET['order_id']);
    $_SESSION['order_id'] = $food_title;

    if (isset($_SESSION['UserName'])) {
        $uname = $_SESSION['UserName'];
        $result = getProductByTitleAndUser($food_title, $uname);

        if ($result->num_rows > 0) {
            $row2 = $result->fetch_assoc();
            $food_quantity = $row2['quantity'] + 1;
            updateProductQuantity($food_quantity, $uname, $food_title);
        } else {
            $status_query = $db->prepare("SELECT `order_id` FROM `forder` WHERE `UserName` = ? AND `status` = 'initial'");
            $status_query->bind_param("s", $uname);
            $status_query->execute();
            $result_s = $status_query->get_result();

            if ($result_s->num_rows > 0) {
                $row_status = $result_s->fetch_assoc();
                $orderid = $row_status['order_id'];
            } else {
                $timestamp = time();
                $orderid = $uname . $timestamp;
            }

            $food_quantity = 1;
            insertNewOrder($food_title, $food_quantity, $uname, $orderid);
        }
    }
}


$items_sql = "SELECT * FROM `products`";
$items_result = $db->query($items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styleCss.css">
    <style>
        .bxx-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            justify-items: center;
            align-items: center;
            margin: 70px auto 0;
            max-width: 1200px;
            padding: 0 20px;
        }
        .order-btn {
            display: inline-block;
            padding: 12px 20px;
            border: 2px solid red;
            color: red;
            cursor: pointer;
            font-size: 20px;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
            z-index: 0;
            margin-top: 15px;
            align-items: center;
            margin-left: 20px;
            text-decoration: none;
            background-color: transparent;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .order-btn:hover {
            color: #ffffff;
            background-color: red;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .bxx-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 100px;
            justify-items: center;
        }
        .bxx {
            padding: 1.2rem;
            background: white;
            border: solid 5px red;
            border-radius: 30px;
            text-align: center;
            box-sizing: content-box;
            position: relative;
            width: 100%;
        }
        .food-title {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .bxx img {
            height: 350px;
            object-fit: cover;
            width: 100%;
            border-radius: 20px;
        }
        .details {
            text-align: left;
        }
        .description {
            text-align: left;
            font-size: 18px;
        }
        .delete-form {
            text-align: right;
            display: inline;
        }
        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body id="body2">
<section class="pop" id="pop">
    <div class="header">
        <header>
            <h1 id="he2" style="margin-left: 10px">Our <span>Popular</span> Types</h1>
            <a href="userwelcome.html" style="color: red;font-size:27px;">Home</a>
            <a href="cart.php" class="icoon"><i class="fas fa-cart-plus" style="color: red; font-size:27px;"></i></a>
            <a href="profile.php" class="icoon"><i class="fa fa-address-book" style="color: red;font-size:27px"></i>Profile</a>
        </header>
    </div>
    <div class="bxxcont" style="margin-top: 70px">
        <div class="bxx-grid">
            <?php
            if ($items_result) {
                while ($row = $items_result->fetch_assoc()) {
                    $food_title = htmlspecialchars($row['title']);
                    $food_description = htmlspecialchars($row['description']);
                    $food_img_path = htmlspecialchars($row['proimage']);
                    $food_price = htmlspecialchars($row['price']);

                    echo "<div class='bxx'>
                            <h1 class='food-title'>$food_title</h1>
                            <img src='../images/productimg/$food_img_path' alt='$food_title'>
                            <div class='details'>
                                <div class='details-sub'>
                                    <h5 class='cost'>$food_price$</h5>
                                </div>
                                <p class='description'>$food_description</p>
                                <form action='' method='get' class='delete-form'>
                                    <input type='hidden' name='order_id' value='$food_title'>
                                    <input type='submit' name='orderbtn' class='delete-btn' value='Order now'>
                                </form>
                            </div>
                          </div>";
                }
            } else {
                echo "Error fetching products: " . $db->error;
            }
            ?>
        </div>
    </div>
</section>
<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this product?");
    }
</script>
</body>
</html>

<?php
// Close database connection
$db->close();
?>




<?php
//session_start();
//$db = new mysqli('localhost', 'root', '', 'restaurant');
//
//// Check connection
//if ($db->connect_error) {
//    die("Connection failed: " . $db->connect_error);
//}
//
//function escape($value)
//{
//    global $db;
//    return mysqli_real_escape_string($db, $value);
//}
//
//if (isset($_GET['orderbtn'])) {
//    $food_title = escape($_GET['order_id']);
//    $_SESSION['order_id'] = $food_title;
//    if (isset($_SESSION['UserName'])) {
//        $uname = $_SESSION['UserName'];
//        $product_query = $db->prepare("SELECT * FROM `forder` WHERE `title` = ? AND `UserName` = ? AND `status` = 'initial'");
//        $product_query->bind_param("ss", $food_title, $uname);
//        $product_query->execute();
//        $result = $product_query->get_result();
//
//        if ($result->num_rows > 0) {
//            while ($row2 = $result->fetch_assoc()) {
//                $food_quantity = $row2['quantity'] + 1;
//                $update_query = $db->prepare("UPDATE `forder` SET `quantity` = ? WHERE `UserName` = ? AND `title` = ? AND `status` = 'initial'");
//                $update_query->bind_param("iss", $food_quantity, $uname, $food_title);
//                $update_query->execute();
//            }
//        } else {
//            $status_query = $db->prepare("SELECT `order_id` FROM `forder` WHERE `UserName` = ? AND `status` = 'initial'");
//            $status_query->bind_param("s", $uname);
//            $status_query->execute();
//            $result_s = $status_query->get_result();
//
//            echo "<script>alert('results: $result_s->num_rows');</script>";
//            if ($result_s->num_rows > 0) {
//                $row_status = $result_s->fetch_assoc();
//                $orderid = $row_status['order_id'];
//            } else {
//                $timestamp = time();
//                $orderid = $uname . $timestamp;
//            }
//            $food_quantity = 1;
//            $insert_query = $db->prepare("INSERT INTO `forder` (`title`, `quantity`, `UserName`, `order_id`, `status`) VALUES (?, ?, ?, ?, 'initial')");
//            $insert_query->bind_param("siss", $food_title, $food_quantity, $uname, $orderid);
//            $insert_query->execute();
//        }
//    }
//}
//
//// Fetch all products
//$items_sql = "SELECT * FROM `products`";
//$items_result = mysqli_query($db, $items_sql);
//?>
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>Restaurant Menu</title>-->
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">-->
<!--    <link rel="stylesheet" href="styleCss.css">-->
<!--    <style>-->
<!--        .bxx-grid {-->
<!--            display: grid;-->
<!--            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));-->
<!--            gap: 20px;-->
<!--            justify-items: center;-->
<!--            align-items: center;-->
<!--            margin: 70px auto 0;-->
<!--            max-width: 1200px;-->
<!--            padding: 0 20px;-->
<!--        }-->
<!--        .order-btn {-->
<!--            display: inline-block;-->
<!--            padding: 12px 20px;-->
<!--            border: 2px solid red;-->
<!--            color: red;-->
<!--            cursor: pointer;-->
<!--            font-size: 20px;-->
<!--            border-radius: 30px;-->
<!--            position: relative;-->
<!--            overflow: hidden;-->
<!--            z-index: 0;-->
<!--            margin-top: 15px;-->
<!--            align-items: center;-->
<!--            margin-left: 20px;-->
<!--            text-decoration: none;-->
<!--            background-color: transparent;-->
<!--            transition: background-color 0.3s ease, color 0.3s ease;-->
<!--        }-->
<!--        .order-btn:hover {-->
<!--            color: #ffffff;-->
<!--            background-color: red;-->
<!--        }-->
<!--        .header {-->
<!--            text-align: center;-->
<!--            margin-bottom: 20px;-->
<!--        }-->
<!--        .bxx-grid {-->
<!--            display: grid;-->
<!--            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));-->
<!--            gap: 100px;-->
<!--            justify-items: center;-->
<!--        }-->
<!--        .bxx {-->
<!--            padding: 1.2rem;-->
<!--            background: white;-->
<!--            border: solid 5px red;-->
<!--            border-radius: 30px;-->
<!--            text-align: center;-->
<!--            box-sizing: content-box;-->
<!--            position: relative;-->
<!--            width: 100%;-->
<!--        }-->
<!--        .food-title {-->
<!--            font-size: 20px;-->
<!--            margin-bottom: 10px;-->
<!--        }-->
<!--        .bxx img {-->
<!--            height: 350px;-->
<!--            object-fit: cover;-->
<!--            width: 100%;-->
<!--            border-radius: 20px;-->
<!--        }-->
<!--        .details {-->
<!--            text-align: left;-->
<!--        }-->
<!--        .description {-->
<!--            text-align: left;-->
<!--            font-size: 18px;-->
<!--        }-->
<!--        .delete-form {-->
<!--            text-align: right;-->
<!--            display: inline;-->
<!--        }-->
<!--        .delete-btn {-->
<!--            background-color: red;-->
<!--            color: white;-->
<!--            border: none;-->
<!--            padding: 10px;-->
<!--            border-radius: 5px;-->
<!--            cursor: pointer;-->
<!--            transition: background-color 0.3s ease;-->
<!--        }-->
<!--        .delete-btn:hover {-->
<!--            background-color: darkred;-->
<!--        }-->
<!--    </style>-->
<!--</head>-->
<!--<body id="body2">-->
<!--<section class="pop" id="pop">-->
<!--    <div class="header">-->
<!--        <header>-->
<!--            <h1 id="he2" style="margin-left: 10px">Our <span>Popular</span> Types</h1>-->
<!--            <a href="cart.php" class="icoon"><i class="fas fa-cart-plus" style="color: red; font-size:27px;"></i></a>-->
<!--            <a href="profile.html" class="icoon"><i class="fa fa-address-book" style="color: red;font-size:27px"></i>Profile</a>-->
<!--        </header>-->
<!--    </div>-->
<!--    <div class="bxxcont" style="margin-top: 70px">-->
<!--        <div class="bxx-grid">-->
<!--            --><?php
//            if ($items_result) {
//                while ($row = mysqli_fetch_assoc($items_result)) {
//                    $food_title = htmlspecialchars($row['title']);
//                    $food_description = htmlspecialchars($row['description']);
//                    $food_img_path = htmlspecialchars($row['proimage']);
//                    $food_price = htmlspecialchars($row['price']);
//
//                    echo "<div class='bxx'>
//                            <h1 class='food-title'>$food_title</h1>
//                            <img src='../images/productimg/$food_img_path' alt='$food_title'>
//                            <div class='details'>
//                                <div class='details-sub'>
//                                    <h5 class='cost'>$food_price$</h5>
//                                </div>
//                                <p class='description'>$food_description</p>
//                                <form action='' method='get' class='delete-form'>
//                                    <input type='hidden' name='order_id' value='$food_title'>
//                                    <input type='submit' name='orderbtn' class='delete-btn' value='Order now'>
//                                </form>
//                            </div>
//                          </div>";
//                }
//            } else {
//                echo "Error fetching products: " . mysqli_error($db);
//            }
//            ?>
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
<!--<script>-->
<!--    function confirmDelete() {-->
<!--        return confirm("Are you sure you want to delete this product?");-->
<!--    }-->
<!--</script>-->
<!--</body>-->
<!--</html>-->
<!---->
<?php
//// Close database connection
//mysqli_close($db);
//?>
<!---->
