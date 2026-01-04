<?php
session_start();
$db = new mysqli('localhost', 'root', '', 'restaurant');


if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

function escape($value) {
    global $db;
    return mysqli_real_escape_string($db, $value);
}
if (isset($_GET['editbtn'])){
    $food_title = escape($_GET['edit_id']);
    $_SESSION['edit_id'] = $food_title;
    header("Location:editproduct.php");
}


if (isset($_POST['deletebtn'])) {
    $food_title = escape($_POST['delete_id']);
    $deletesql = "DELETE FROM `products` WHERE `title`='$food_title'";
    $res = mysqli_query($db, $deletesql);
    if ($res) {
        echo "<script>alert('Product deleted successfully');</script>";
        echo "<script>window.location = 'foodmenu.php';</script>";
    } else {
        echo "<script>alert('Error deleting product');</script>";
    }
}


$items_sql = "SELECT * FROM `products`";
$items_result = mysqli_query($db, $items_sql);

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
            /*margin-right: 85px;*/



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
            <a href="adminwelcomep.html" style="color: red;font-size:27px;">Home</a>
            <a href="addproduct.php" class="order-btn">ADD</a>

        </header>
    </div>
    <div class="bxxcont" style="margin-top: 70px">
        <div class="bxx-grid">

            <?php
            if ($items_result) {
                while ($row = mysqli_fetch_assoc($items_result)) {
                    $food_title = $row['title'];
                    $food_description = $row['description'];
                    $food_img_path = $row['proimage'];
                    $food_price = $row['price'];

                    echo "<div class='bxx'>
                            <h1 class='food-title'>$food_title</h1>
                            <img src='../images/productimg/" . htmlspecialchars($food_img_path) . "' alt='" . htmlspecialchars($food_title) . "'>
                            <div class='details'>
                                <div class='details-sub'>
                                    <h5 class='cost'>$food_price$</h5>
                                </div>
                                <p class='description'>" . htmlspecialchars($food_description) . "</p>
                                <form action='' method='POST' class='delete-form' onsubmit='return confirmDelete();'>
                               
                                    <input type='hidden' name='delete_id' value='" . htmlspecialchars($food_title) . "'>
                                    <input type='submit' name='deletebtn' id='delete' class='delete-btn' value='Delete'>
                                    
                                </form>
                                 <form action='' method='get' class='delete-form' >
                                <input type='hidden' name='edit_id' value='" . htmlspecialchars($food_title) . "'>
                                <input type='submit' name='editbtn' class='delete-btn' value='Edit'>
                                   
                                    
                                </form>
                            </div>
                          </div>";
                }
            } else {
                echo "Error fetching products: " . mysqli_error($db);
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
mysqli_close($db);
?>







<?php
//session_start();
//$db = new mysqli('localhost', 'root', '', 'restaurant');
//$items_sql = "SELECT * FROM `products` ";
//$items_result = mysqli_query($db, $items_sql);
//if ($items_result) {
//
//while ($row = mysqli_fetch_assoc($items_result)) {
//
//$food_title = $row['title'];
//$food_description = $row['description'];
//$food_img_path = $row['proimage'];
//$food_price = $row['price'];
//echo "<body id='body2'><section class='pop' id='pop'><div class='header'> <header>
//          <h1 id='he2'  style='margin-left: 10px'>Our <span>Popular</span> Types</h1> <a href='addproduct.php' class='order-btn' STYLE='font-size:20px;font-family: 'Snap ITC'>ADD</a></header></div><div  class='bxxcont' style='margin-top: 70px'><div  class='bxx '>
//                    <img  src='../images/productimg/" . $food_img_path . "'>
//                        <div class='details'>
//                          <div class='details-sub'>
//                            <h1> $food_title</h1>
//                           <h5 class='cost'>$food_price$</h5>
//                          </div>
//                          <p>
//                            '$food_description'
//                          </p>
//                     <form action='' method='POST'>
//                                    <input type='hidden' name='delete_id' >
//                                    <input type='submit' name='deletebtn' value='Delete'>
//                                </form>
//                        </div>
//                      </div>
//                      </div>
//                      </div></section></body>";
//}
//} else {
//    echo "Error fetching products: " . mysqli_error($db);
//}
//if(isset($_POST['deletebtn'])) {
//$image_name=$_GET['image_name'];
//            $deletesql = "DELETE FROM `products` WHERE `title`='$food_title'";
//            $res = mysqli_query($db, $deletesql);
//
//            if ($res == true) {
//
//            }
//        }
//
//
//
//
//
//?>
<!---->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">-->
<!--<link rel="stylesheet" href="styleCss.css" />-->
<!--<style>-->
<!--    .order-btn {-->
<!--        display: inline-block;-->
<!--        padding: 1px 5px;-->
<!--        border: red 3px solid;-->
<!--        color: red;-->
<!--        cursor: pointer;-->
<!--        font-size: 20px;-->
<!--        border-radius: 15px;-->
<!--        position: relative;-->
<!--        overflow: hidden;-->
<!--        z-index: 0;-->
<!--        margin-top: 15px;-->
<!--        align-items: center;-->
<!--        margin-left: 20px;-->
<!--    }-->
<!--    .order-btn::before {-->
<!--        position: absolute;-->
<!--        content: '';-->
<!--        width: 0%;-->
<!--        height: 100%;-->
<!--        right: 0;-->
<!--        top: 0;-->
<!--        background: #ffbb99;-->
<!--        transition: 0.5s linear;-->
<!--        z-index: -1;-->
<!--    }-->
<!--    .order-btn:hover::before {-->
<!--        width: 100%;-->
<!--        right: 100%;-->
<!--        left: 0%;-->
<!--    }-->
<!--    .order-btn:hover {-->
<!--        color: #ffffff;-->
<!--        border-color: #ffbb99;-->
<!--    }-->
<!--    .header {-->
<!--        text-align: center;-->
<!--        margin-bottom: 20px;-->
<!--    }-->
<!--    .bxx{-->
<!--        padding: 1.3rem;-->
<!--        background: white;-->
<!--        border: solid 5px red;-->
<!--        border-radius: 50px;-->
<!--        text-align: center;-->
<!--        flex: 2 2 40rem;-->
<!--        position: relative;-->
<!--    }-->
<!---->
<!--    .bxx img{-->
<!--        height: 50rem;-->
<!--        object-fit: cover;-->
<!--        width: 100%;-->
<!--        border-radius: 40px;-->
<!--    }-->
<!--</style>-->
<!---->
