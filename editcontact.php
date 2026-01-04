<?php
session_start();

$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$stmt = $db->prepare("SELECT * FROM `contactus` ");

$stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc();
$stmt->close();
if ($contact) {
    $facebook = $contact['facebook'];
    $instagram = $contact['Instagram'];
    $twitter=$contact['twitter'];
    $phone=$contact['phone'];
    $email=$contact['email'];
    $location=$contact['location'];

} else {
    $_SESSION['edit_error'] = " not found.";
    header("Location: admincontact.php");
    exit;
}
if (isset($_POST['updatebtn'])) {
    $facebook = $_POST['facebook'];
    $instagram = $_POST['inistgram'];
    $twitter=$_POST['twitter'];
    $phone=$_POST['phone'];
    $email=$_POST['email'];
    $location=$_POST['location'];
    $stmt = $db->prepare("UPDATE `contactus` SET `facebook`=?, `Instagram`=?,`twitter`=?,`phone`=?,`email`=?,`location`=?");
    $stmt->bind_param("ssssss", $facebook, $instagram,$twitter,$phone,$email,$location);
    $res2 = $stmt->execute();
    $stmt->close();


    if ($res2) {

        echo "<script>alert('Contact updated successfully');</script>";
        echo "<script>window.location = 'admincontact.php';</script>";
    } else {
        echo "<script>alert('Error updating Contact');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact</title>
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


        function confirmUpdate(event) {
            if (confirm("Are you sure you want to update this contact?")) {
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
    <a href="admincontact.php" style="color: red;font-size:27px;">Back</a>

</header>
<div class="container">
    <div class="form-container">
        <h1>Edit Contact</h1>


        <?php if (isset($_SESSION['edit_error'])): ?>
            <div class="error"><?php echo $_SESSION['edit_error']; ?></div>
            <?php unset($_SESSION['edit_error']); ?>
        <?php endif; ?>

        <form id="editForm" action="editcontact.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Facebook:</label>
                <input type="text" id="title" name="facebook" placeholder="Facebook" value="<?php echo htmlspecialchars($facebook); ?>" >

            </div>
            <div class="form-group">
                <label for="title">Inistagram:</label>
                <input type="text" id="title" name="inistgram" placeholder="Instagram" value="<?php echo htmlspecialchars($instagram); ?>" >

            </div>
            <div class="form-group">
                <label for="title">Twitter:</label>
                <input type="text" id="title" name="twitter" placeholder="Twitter" value="<?php echo htmlspecialchars($twitter); ?>" >

            </div>
            <div class="form-group">
                <label for="title">Phone:</label>
                <input type="text" id="title" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($phone); ?>" >

            </div>
            <div class="form-group">
                <label for="title">Email:</label>
                <input type="text" id="title" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" >

            </div>

            <div class="form-group">
                <label for="description">Location:</label>
                <input type="text" id="description" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="updatebtn" class="btn" onclick="confirmUpdate(event)">Edit Contact</button>
            </div>

        </form>
    </div>
</div>
</body>
</html>

