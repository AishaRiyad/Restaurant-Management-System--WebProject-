<?php
session_start();
$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$sql="SELECT * FROM `aboutus` ";
$res = $db->prepare($sql);
$res->execute();
$result = $res->get_result();




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewreport" content="width-device-width, initial-scale=1">
    <link rel="stylesheet" type ="text/css" href="style1.css">
    <title>About Us</title>
    <style>
        .order-btn {
            display: inline-block;
            padding: 1px 5px;
            border: red 3px solid;
            color: red;
            cursor: pointer;
            font-size: 20px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            z-index: 0;
            margin-top: 15px;
            align-items: center;
            margin-left: 20px;
        }
        .order-btn::before {
            position: absolute;
            content: '';
            width: 0%;
            height: 100%;
            right: 0;
            top: 0;
            background: #ffbb99;
            transition: 0.5s linear;
            z-index: -1;
        }
        .order-btn:hover::before {
            width: 100%;
            right: 100%;
            left: 0%;
        }
        .order-btn:hover {
            color: #ffffff;
            border-color: #ffbb99;
        }
    </style>
</head>
<body>
<section class="hero">
    <div class="heading">

        <h1>About Us</h1>
    </div>
    <?php
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $head=$row['head'];
            $para=$row['para'];



      echo  "<div class ='container' >
        <div class='content2' >
            <h2 >$head</h2 >
            <p >$para</p >
            <a href = 'userwelcome.html' class='order-btn' > Back To Main Page </a >
        </div >

    </div >";
    }
    }
    ?>
</section>

</body>
</html>

