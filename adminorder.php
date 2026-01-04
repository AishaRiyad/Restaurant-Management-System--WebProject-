<?php
session_start();
$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}


function getWaitingOrders($db) {
    $sql = "SELECT * FROM `forder` WHERE `status`='waiting' GROUP BY `order_id`";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}


function getOrderDetails($db, $order_id, $uname) {
    $sql = "SELECT * FROM `forder` WHERE `order_id` = ? AND `UserName` = ? AND `status`='waiting'";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $order_id, $uname);
    $stmt->execute();
    return $stmt->get_result();
}


function getFoodPrice($db, $title) {
    $sql = "SELECT `price` FROM `products` WHERE `title` = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['price'];
}


if (isset($_POST['action']) && in_array($_POST['action'], ['Accept', 'Reject'])) {
    $uname = $_POST['uname'];
    $order_id = $_POST['order_id'];
    $status = ($_POST['action'] == 'Accept') ? 'accepted' : 'rejected';


    $sql = "UPDATE `forder` SET `status` = ? WHERE `order_id` = ? AND `UserName` = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sss", $status, $order_id, $uname);
    $stmt->execute();
    $stmt->close();
}


$waitingOrders = getWaitingOrders($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: "Snap ITC", sans-serif;

            padding: 20px;
            background:url("images/pink.jpg")center/cover ;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: deeppink;
        }
        .buttons {

            text-align: center;
            margin: 20px;
        }
        .buttons button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 1em;
            cursor: pointer;
            font-family: "Snap ITC";
            background: purple;
            color: white;
        }

        .order-group {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
<div class="header">
    <header>

        <a href="adminwelcomep.html" style="color: red;font-size:27px;">Home</a>

    </header>
<?php if ($waitingOrders->num_rows > 0): ?>
    <?php while ($order = $waitingOrders->fetch_assoc()): ?>
        <div class="order-group">
            <h2>Order ID: <?php echo htmlspecialchars($order['order_id']); ?> - User: <?php echo htmlspecialchars($order['UserName']); ?></h2>
            <table>
                <thead>
                <tr>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $order_id = $order['order_id'];
                $uname = $order['UserName'];
                $orderDetails = getOrderDetails($db, $order_id, $uname);
                while ($item = $orderDetails->fetch_assoc()):
                    $price = getFoodPrice($db, $item['title']);
                    $total_price = $price * $item['quantity'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($total_price, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <form method="post">
                <div class="buttons">
                    <button type="submit" name="action" value="Accept">Accept</button>
                    <button type="submit" name="action" value="Reject">Reject</button>
                    <input type="hidden" name="uname" value="<?php echo htmlspecialchars($order['UserName']); ?>">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                </div>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No waiting orders.</p>
<?php endif; ?>
</body>
</html>
