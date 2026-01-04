<?php
session_start();


if (isset($_GET['order_id'])) {
    $_SESSION['order_id'] = $_GET['order_id'];
}
$total_price = 0;

if (isset($_SESSION['order_id'])) {
    $order_id = $_SESSION['order_id'];
    $uname = $_SESSION['UserName'];


    $db = new mysqli('localhost', 'root', '', 'restaurant');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $sql_delete = "DELETE FROM `forder` WHERE `id` = ? AND `status`='initial'";
        $stmt_delete = $db->prepare($sql_delete);
        $stmt_delete->bind_param("i", $delete_id);
        $stmt_delete->execute();
        $stmt_delete->close();
    }
    if (isset($_POST['confirm_cart'])) {
        $sql_confirm = "UPDATE `forder` SET `status` = 'waiting' WHERE `UserName` = ? AND `status` = 'initial'";
        $stmt_confirm = $db->prepare($sql_confirm);
        $stmt_confirm->bind_param("s", $uname);
        $stmt_confirm->execute();
        $stmt_confirm->close();
    }


    $cart_data = [];
    $sql = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='initial' ORDER BY `order_id`";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $res = $stmt->get_result();


    while ($row = $res->fetch_assoc()) {
        $cart_data[$row['order_id']][] = $row;
    }

    $sql_price = "SELECT `title`, `price` FROM `products` WHERE `title` IN (SELECT `title` FROM `forder` WHERE `UserName` = ?)";
    $stmt_price = $db->prepare($sql_price);
    $stmt_price->bind_param("s", $uname);
    $stmt_price->execute();
    $res_price = $stmt_price->get_result();

    while ($row_price = $res_price->fetch_assoc()) {
        $price_data[$row_price['title']] = $row_price['price'];
    }
    foreach ($cart_data as $order) {
        foreach ($order as $item) {
            if (isset($price_data[$item['title']])) {
                $total_price += $price_data[$item['title']] * $item['quantity'];
            }
        }
    }

    $cartwait_data = [];
    $sqlwaiting = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='waiting' ORDER BY `order_id`";
    $stmtwait = $db->prepare($sqlwaiting);
    $stmtwait->bind_param("s", $uname);
    $stmtwait->execute();
    $reswait = $stmtwait->get_result();


    while ($rowwait = $reswait->fetch_assoc()) {
        $cartwait_data[$rowwait['order_id']][] = $rowwait;
    }

    $cartaccept_data = [];
    $sqlaccept = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='accepted' ORDER BY `order_id`";
    $stmtaccept = $db->prepare($sqlaccept);
    $stmtaccept->bind_param("s", $uname);
    $stmtaccept->execute();
    $resaccept = $stmtaccept->get_result();


    while ($rowaccept = $resaccept->fetch_assoc()) {
        $cartaccept_data[$rowaccept['order_id']][] = $rowaccept;
    }

    $cartregect_data = [];
    $sqlregect = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='rejected' ORDER BY `order_id`";
    $stmtregect = $db->prepare($sqlregect);
    $stmtregect->bind_param("s", $uname);
    $stmtregect->execute();
    $resregect = $stmtregect->get_result();


    while ($rowregect = $resregect->fetch_assoc()) {
        $cartregect_data[$rowregect['order_id']][] = $rowregect;
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
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styleCss.css">
    <style>
        body {
            font-family: "Snap ITC", sans-serif;
           background: url("images/pink.jpg")center/cover;
            color: #333;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #444;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: deeppink;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-price {
            text-align: right;
            font-size: 1.2em;
            margin-top: 10px;
        }
        .order-section {
            margin-bottom: 40px;
        }
        .order-id {
            background-color: deeppink;
            color: white;
            padding: 10px;
            font-size: 1.5em;
            text-align: left;
        }
        .btn {
            background-color: deeppink;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 1em;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="header">
    <header>
        <h1 id="he2" style="margin-left: 10px">Our <span>Popular</span> Types</h1>
        <a href="userwelcome.html" style="color: red;font-size:27px;">Home</a>
        <a href="userorder.php" class="icoon"><i class="fas fa-cart-plus" style="color: red; font-size:27px;"></i></a>
        <a href="profile.php" class="icoon"><i class="fa fa-address-book" style="color: red;font-size:27px"></i>Profile</a>
    </header>
<div class="container">
    <form method="post">
        <div class="order-section">
            <h1>Initial Orders</h1>
            <?php if (!empty($cart_data)): ?>
                <?php foreach ($cart_data as $order_id => $order_items): ?>
                    <div class="order-id">Order ID: <?php echo htmlspecialchars($order_id); ?></div>
                    <table>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Food Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['UserName']); ?></td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo isset($price_data[$item['title']]) ? htmlspecialchars($price_data[$item['title']]) : ''; ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['status']); ?></td>
                                <td>
                                    <button type="submit" name="delete_id" value="<?php echo $item['id']; ?>" class="btn">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
                <button type="submit" name="confirm_cart" class="btn">Confirm</button>
                <div class="total-price">
                    Total Price: $<?php echo number_format($total_price, 2); ?>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <div class="order-section">
            <h1>Waiting Orders</h1>
            <?php if (!empty($cartwait_data)): ?>
                <?php foreach ($cartwait_data as $order_id => $order_items): ?>
                    <div class="order-id">Order ID: <?php echo htmlspecialchars($order_id); ?></div>
                    <table>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Food Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['UserName']); ?></td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo isset($price_data[$item['title']]) ? htmlspecialchars($price_data[$item['title']]) : ''; ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No waiting orders.</p>
            <?php endif; ?>
        </div>

        <div class="order-section">
            <h1>Accepted Orders</h1>
            <?php if (!empty($cartaccept_data)): ?>
                <?php foreach ($cartaccept_data as $order_id => $order_items): ?>
                    <div class="order-id">Order ID: <?php echo htmlspecialchars($order_id); ?></div>
                    <table>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Food Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['UserName']); ?></td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo isset($price_data[$item['title']]) ? htmlspecialchars($price_data[$item['title']]) : ''; ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No accepted orders.</p>
            <?php endif; ?>
        </div>

        <div class="order-section">
            <h1>Rejected Orders</h1>
            <?php if (!empty($cartregect_data)): ?>
                <?php foreach ($cartregect_data as $order_id => $order_items): ?>
                    <div class="order-id">Order ID: <?php echo htmlspecialchars($order_id); ?></div>
                    <table>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Food Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['UserName']); ?></td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo isset($price_data[$item['title']]) ? htmlspecialchars($price_data[$item['title']]) : ''; ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No rejected orders.</p>
            <?php endif; ?>
        </div>
    </form>
</div>
</body>
</html>




<?php
//session_start();
//
//// Check if order_id is passed in the URL and set it in session
//if (isset($_GET['order_id'])) {
//    $_SESSION['order_id'] = $_GET['order_id'];
//}
//$total_price=0;
//// Check if order_id is set in session
//if (isset($_SESSION['order_id'])) {
//    $order_id = $_SESSION['order_id'];
//    $uname = $_SESSION['UserName'];
//
//    // Establish database connection
//    $db = new mysqli('localhost', 'root', '', 'restaurant');
//    if ($db->connect_error) {
//        die("Connection failed: " . $db->connect_error);
//    }
//    if (isset($_POST['delete_id'])) {
//        $delete_id = $_POST['delete_id'];
//        $sql_delete = "DELETE FROM `forder` WHERE `id` = ? AND `status`='initial'";
//        $stmt_delete = $db->prepare($sql_delete);
//        $stmt_delete->bind_param("i", $delete_id);
//        $stmt_delete->execute();
//        $stmt_delete->close();
//    }
//    if (isset($_POST['confirm_cart'])) {
//        $sql_confirm = "UPDATE `forder` SET `status` = 'waiting' WHERE `UserName` = ? AND `status` = 'initial'";
//        $stmt_confirm = $db->prepare($sql_confirm);
//        $stmt_confirm->bind_param("s", $uname);
//        $stmt_confirm->execute();
//        $stmt_confirm->close();
//    }
//
//    // Prepare and execute SQL query to fetch orders for the user
//    $cart_data = [];
//    $sql = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='initial'";
//    $stmt = $db->prepare($sql);
//    $stmt->bind_param("s", $uname);
//    $stmt->execute();
//    $res = $stmt->get_result();
//
//    // Fetch data and store in $cart_data array
//    while ($row = $res->fetch_assoc()) {
//        $statuss=$row['status'];
//        $cart_data[] = $row;
//    }
//
//    $sql_price = "SELECT `title`, `price` FROM `products` WHERE `title` IN (SELECT `title` FROM `forder` WHERE `UserName` = ?)";
//    $stmt_price = $db->prepare($sql_price);
//    $stmt_price->bind_param("s", $uname);
//    $stmt_price->execute();
//    $res_price = $stmt_price->get_result();
//
//    while ($row_price = $res_price->fetch_assoc()) {
//        $price_data[$row_price['title']] = $row_price['price'];
//    }
//    foreach ($cart_data as $item) {
//        if (isset($price_data[$item['title']])) {
//            $total_price += $price_data[$item['title']] * $item['quantity'];
//        }
//    }
//
//    $cartwait_data = [];
//    $sqlwaiting = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='waiting' ORDER BY `order_id`";
//    $stmtwait = $db->prepare($sqlwaiting);
//    $stmtwait->bind_param("s", $uname);
//    $stmtwait->execute();
//    $reswait = $stmtwait->get_result();
//
//    // Fetch data and store in $cart_data array
//    while ($rowwait = $reswait->fetch_assoc()) {
//        $cartwait_data[] = $rowwait;
//    }
//
//    $cartaccept_data = [];
//    $sqlaccept = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='accepted' ORDER BY `order_id`";
//    $stmtaccept = $db->prepare($sqlaccept);
//    $stmtaccept->bind_param("s", $uname);
//    $stmtaccept->execute();
//    $resaccept = $stmtaccept->get_result();
//
//    // Fetch data and store in $cart_data array
//    while ($rowaccept = $resaccept->fetch_assoc()) {
//        $cartaccept_data[] = $rowaccept;
//    }
//
//    $cartregect_data = [];
//    $sqlregect = "SELECT * FROM `forder` WHERE `UserName` = ? AND `status`='regected' ORDER BY `order_id`";
//    $stmtregect = $db->prepare($sqlregect);
//    $stmtregect->bind_param("s", $uname);
//    $stmtregect->execute();
//    $resregect = $stmtregect->get_result();
//
//    // Fetch data and store in $cart_data array
//    while ($rowregect = $resregect->fetch_assoc()) {
//        $cartregect_data[] = $rowregect;
//    }
//
//
//    // Close prepared statement and database connection
//    $stmt->close();
//    $db->close();
//}
//?>
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>Shopping Cart</title>-->
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">-->
<!--    <link rel="stylesheet" href="styleCss.css">-->
<!--    <style>-->
<!--        table {-->
<!--            width: 80%;-->
<!--            margin: 0 auto;-->
<!--            border-collapse: collapse;-->
<!--        }-->
<!--        th, td {-->
<!--            padding: 10px;-->
<!--            border: 1px solid #ddd;-->
<!--            text-align: center;-->
<!--        }-->
<!--        th {-->
<!--            background-color: #f4f4f4;-->
<!--        }-->
<!--    </style>-->
<!--</head>-->
<!--<body>-->
<!--<!--<h1>Initial</h1>-->-->
<!---->
<!--<form method="post">-->
<?php //if (!empty($cart_data)): ?>
<!--    <table>-->
<!--        <thead>-->
<!--        <tr>-->
<!--            <th>Name</th>-->
<!--            <th>Food Name</th>-->
<!--            <th>Price</th>-->
<!--            <th>Quantity</th>-->
<!--            <th>Status</th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody>-->
<!--        --><?php //foreach ($cart_data as $item): ?>
<!--            <tr>-->
<!--                <td>--><?php //echo htmlspecialchars($item['UserName']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item['title']); ?><!--</td>-->
<!--                <td>--><?php //echo isset($price_data[$item['title']]) ? htmlspecialchars($price_data[$item['title']]) : ''; ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item['quantity']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item['status']); ?><!--</td>-->
<!--                <td>-->
<!---->
<!---->
<!--                    <button type="submit" name="delete_id" value="--><?php //echo $item['id']; ?><!--" >Delete</button>-->
<!--                </td>-->
<!--            </tr>-->
<!---->
<!--        --><?php //endforeach; ?>
<!--        <td>-->
<!--            <button type="submit" name="confirm_cart">Confirm</button>-->
<!--        </tbody>-->
<!--    </table>-->
<!--    <div class="total-price">-->
<!--        Total Price: $--><?php //echo number_format($total_price, 2); ?>
<!--    </div>-->
<?php //else: ?>
<!--    <p>Your cart is empty.</p>-->
<?php //endif; ?>
<!--</form>-->
<!---->
<!--<h1>Waiting</h1>-->
<?php //if (!empty($cartwait_data)): ?>
<!--    <table>-->
<!--        <thead>-->
<!--        <tr>-->
<!--            <th>Name</th>-->
<!--            <th>Food Name</th>-->
<!--            <th>Price</th>-->
<!--            <th>Quantity</th>-->
<!--            <th>Status</th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody>-->
<!--        --><?php //foreach ($cartwait_data as $item2): ?>
<!--            <tr>-->
<!--                <td>--><?php //echo htmlspecialchars($item2['UserName']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item2['title']); ?><!--</td>-->
<!--                <td>--><?php //echo isset($price_data[$item2['title']]) ? htmlspecialchars($price_data[$item2['title']]) : ''; ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item2['quantity']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item2['status']); ?><!--</td>-->
<!---->
<!--            </tr>-->
<!---->
<!--        --><?php //endforeach; ?>
<!---->
<!--        </tbody>-->
<!--    </table>-->
<!---->
<?php //else: ?>
<!--    <p>Your cart is empty.</p>-->
<?php //endif; ?>
<!---->
<!--<h1>Accepted</h1>-->
<?php //if (!empty($cartaccept_data)): ?>
<!--    <table>-->
<!--        <thead>-->
<!--        <tr>-->
<!--            <th>Name</th>-->
<!--            <th>Food Name</th>-->
<!--            <th>Price</th>-->
<!--            <th>Quantity</th>-->
<!--            <th>Status</th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody>-->
<!--        --><?php //foreach ($cartaccept_data as $item3): ?>
<!--            <tr>-->
<!--                <td>--><?php //echo htmlspecialchars($item3['UserName']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item3['title']); ?><!--</td>-->
<!--                <td>--><?php //echo isset($price_data[$item3['title']]) ? htmlspecialchars($price_data[$item3['title']]) : ''; ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item3['quantity']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item3['status']); ?><!--</td>-->
<!---->
<!--            </tr>-->
<!---->
<!--        --><?php //endforeach; ?>
<!---->
<!--        </tbody>-->
<!--    </table>-->
<!---->
<?php //else: ?>
<!--    <p>Your cart is empty.</p>-->
<?php //endif; ?>
<!---->
<!--<h1>Regicted</h1>-->
<?php //if (!empty($cartregect_data)): ?>
<!--    <table>-->
<!--        <thead>-->
<!--        <tr>-->
<!--            <th>Name</th>-->
<!--            <th>Food Name</th>-->
<!--            <th>Price</th>-->
<!--            <th>Quantity</th>-->
<!--            <th>Status</th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody>-->
<!--        --><?php //foreach ($cartregect_data as $item4): ?>
<!--            <tr>-->
<!--                <td>--><?php //echo htmlspecialchars($item4['UserName']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item4['title']); ?><!--</td>-->
<!--                <td>--><?php //echo isset($price_data[$item4['title']]) ? htmlspecialchars($price_data[$item4['title']]) : ''; ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item4['quantity']); ?><!--</td>-->
<!--                <td>--><?php //echo htmlspecialchars($item4['status']); ?><!--</td>-->
<!---->
<!--            </tr>-->
<!---->
<!--        --><?php //endforeach; ?>
<!---->
<!--        </tbody>-->
<!--    </table>-->
<!---->
<?php //else: ?>
<!--    <p>Your cart is empty.</p>-->
<?php //endif; ?>
<!--</body>-->
<!--</html>-->
