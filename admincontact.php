<?php
session_start();
$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$sql="SELECT * FROM `contactus` ";
$res = $db->prepare($sql);
$res->execute();
$result = $res->get_result();
if(isset($_GET['editbtn'])){
    header("Location:editcontact.php");
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact-us</title>
    <link rel="stylesheet" href="styleCss.css" />
</head>
<body id="body3">
<section class="contact" id="contact">
    <div class="main-contact">
        <?php
        $facebook = $instagram = $twitter = $phone = $email = $location = '';

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $facebook = htmlspecialchars($row['facebook']);
                $instagram = htmlspecialchars($row['Instagram']);
                $twitter = htmlspecialchars($row['twitter']);
                $phone = htmlspecialchars($row['phone']);
                $email = htmlspecialchars($row['email']);
                $location = htmlspecialchars($row['location']);}}
        ?>

        <div class='contact-content'>

            <h3 >Services</h3>
            <li><a href='adminwelcomep.html'>Home</a></li>
            <li><a href='foodmenu.php'>products</a></li>
            <li><a href='userorder.php'>Order</a></li>
            <li><a href='aboutus.php'>About US</a></li>
            <li><a href='adminprofile.php'>Profile</a></li>

        </div>



        <div class='contact-content'>
            <h3 >Follow Us</h3>
            <li><a href=''<?php echo $facebook; ?>'>Facebook</a></li>
            <li><a href='<?php echo $instagram; ?>'>Instagram</a></li>
            <li><a href='<?php echo $twitter; ?>'>Twitter</a></li>
        </div>

        <div class='contact-content'>
            <h3 >Contact</h3>
            <p> <?php echo $phone; ?></p>
            <p><?php echo $email; ?></p>
            <p><?php echo $location; ?></p>

        </div>
        <form action='' method='get' class='delete-form' >
            <input type='hidden' name='edit_id' '>
            <input type='submit' name='editbtn' class='order-btn' value='Edit'>


        </form>
        <a href="adminwelcomep.html">Back</a>






    </div>
</section>
</body>
</html>


