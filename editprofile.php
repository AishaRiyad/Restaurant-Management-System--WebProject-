<?php
session_start();
if (!isset($_SESSION['UserName'])) {
    $_SESSION['edit_error'] = "User not authenticated.";
    header("Location: login.php");
    exit;
}
$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
//var_dump($_SESSION);
if (isset($_SESSION['UserName'])) {
    $uname = $_SESSION['UserName'];
}
$uname = $_SESSION['UserName'];
$smt = $db->prepare("SELECT * FROM `login` WHERE `UserName`=?");
$smt->bind_param("s", $uname);
$smt->execute();
$result = $smt->get_result();
$profile = $result->fetch_assoc();

$smt->close();
if (!$profile) {

    $_SESSION['edit_error'] = "Profile not found.";
    header("Location:profile.php");
    exit;
}
else {
    $birthdate=$profile['Birthdate'];
    $email=$profile['Email'];
//    $_SESSION['edit_error'] = " not found.";
//    header("Location:profile.php");
//    exit;
}
if (isset($_POST['updatebtn'])) {
    $birthdate = $_POST['Bdatep'];
    $email = $_POST['emailp'];

    $stmt = $db->prepare("UPDATE `login` SET `Birthdate`=?, `Email`=? WHERE `UserName`=?");
    $stmt->bind_param("sss", $birthdate, $email, $uname);
    $res2 = $stmt->execute();
    $stmt->close();

    if ($res2) {
        echo "<script>alert('Profile Info updated successfully');</script>";
        echo "<script>window.location = 'profile.php';</script>";
    } else {
        echo "<script>alert('Error updating Profile Info');</script>";
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


        function confirmUpdate(event) {
            if (confirm("Are you sure you want to update your profile Info?")) {
                document.getElementById('editForm').submit();
            } else {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
<header>

    <a href="userwelcome.html" style="color: red;font-size:27px;">Home</a>
    <a href="profile.php" style="color: red;font-size:27px;">Back</a>

</header>
<div class="container">
    <div class="form-container">
        <h1>Edit Profile</h1>


        <?php if (isset($_SESSION['edit_error'])): ?>
            <div class="error"><?php echo $_SESSION['edit_error']; ?></div>
            <?php unset($_SESSION['edit_error']); ?>
        <?php endif; ?>

        <form id="editForm" action="editprofile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Header:</label>
                <input type="text" id="title" name="head" placeholder="Name"
                       value="<?php echo htmlspecialchars($uname); ?>" disabled>

            </div>

            <div class="form-group">
                <label for="birthdate">Birthdate:</label>
                <input type="date" id="birthdate" name="Bdatep" placeholder="Birthdate"
                       value="<?php echo htmlspecialchars($birthdate); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="emailp" placeholder="Email"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="updatebtn" class="btn" onclick="confirmUpdate(event)">Update</button>
            </div>

        </form>
    </div>
</div>
</body>
</html>
