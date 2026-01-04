<?php

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST['usename1'];
    $datee = $_POST['date1'];
    $emaill = $_POST['email1'];
    $password = $_POST['pass'];
    $confirmpass = $_POST['cpass'];



    if (empty($uname) || empty($datee) || empty($emaill) || empty($password) || empty($confirmpass)) {
        $errors[] = "All fields are required";
    }

    if (!filter_var($emaill, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is not valid";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    if ($password !== $confirmpass) {
        $errors[] = "Password and Confirm Password do not match";
    }

    $db = new mysqli('localhost', 'root', '', 'restaurant');

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $sql = "SELECT * FROM login WHERE UserName='$uname'";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        $errors[] = "Choose a unique username";
    }

    if (count($errors) === 0) {
        $passwordhash = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO `login` (`UserName`, `Password`, `Birthdate`, `Email`, `Type`) 
                        VALUES ('$uname', '$passwordhash', '$datee', '$emaill', '0')";

        if ($db->query($insertQuery) === TRUE) {
            $db->close();
            header('Location: userwelcome.html');
            exit;
        } else {
            echo "<h1>Error: " . $insertQuery . "<br>" . $db->error . "</h1>";
        }
    }

    $db->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign-Up</title>
    <link rel="stylesheet" href="styleCss.css">
    <script src="jsstyle.js"></script>
    <style>
        .error {
            color: red;
            border: 1px solid red;
            padding: 5px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body id="body4">

<form action="signup.php" method="post">
    <div class="blur-bg-overlay"></div>
    <div class="p-form" id="formp">
        <span class="close-btn material-symbols-rounded">Close</span>
        <div class="form-box signup">
            <div class="form-details">
                <h1 class="h11" style="font-family: 'Snap ITC';color: #4d0099;font-size: 30px; margin-top: 15px;">Create
                    Account</h1>
                <p style="color: #9900cc;">Please sign up to become a part of us</p>
            </div>
            <div class="form-content">
                <h1 style="font-family: 'Snap ITC';font-size: 30px;color: #ff1a1a">SignUp</h1>
                <div class="error-container">
                    <?php if (!empty($errors)): ?>
                        <div class="error-container">
                            <?php foreach ($errors as $error): ?>
                                <div class="error"><?php echo $error; ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
                <div class="in-field">
                    <input type="text" name="usename1" required>
                    <label style="font-size: 15px;">User name</label>
                </div>
                <div class="in-field">
                    <input type="date" name="date1" required>
                    <label style="font-size: 15px;">Birthdate</label>
                </div>
                <div class="in-field">
                    <input type="email" name="email1" required>
                    <label style="font-size: 15px;">Enter The Email</label>
                </div>
                <div class="in-field">
                    <input type="password" name="pass" id="pass" required>
                    <label style="font-size: 15px;">Write Password</label>
                </div>
                <div class="in-field">
                    <input type="password" name="cpass" id="cpass" required>
                    <label style="font-size: 15px;">Confirm Password</label>
                </div>
                <div class="policy">
                    <input type="checkbox" id="policy" required>
                    <label for="policy">I accept the <a href="termsandconditions.html">Conditions and Terms</a></label>
                </div>
                <button type="submit" name="submitbtn">Sign Up</button>
                <div class="buttom-link" style="font-size: 1.4rem;">
                    Already have an account? <a href="login.php" id="login-link">Login</a>
                </div>
            </div>
        </div>
    </div>
</form>

</body>
</html>


<!---->
<!---->
<!---->
<?php
//if (isset($_POST['submitbtn'])) {
//    $uname = $_POST['usename1'];
//    $datee = $_POST['date1'];
//    $emaill = $_POST['email1'];
//    $password = $_POST['pass'];
//    $confirmpass = $_POST['cpass'];
//    $passwordhash = password_hash($password, PASSWORD_DEFAULT);
//
//    $errors = array();
//
//    if (empty($uname) or empty($datee) or empty($emaill) or empty($password) or empty($confirmpass)) {
//        $message = "All fields are required";
//        array_push($errors, "All fields are required");
//        echo "<script>alert('$message');</script>";
//    }
//    if (!filter_var($emaill, FILTER_VALIDATE_EMAIL)) {
//        $message = "Email is not valid";
//        echo "<script>alert('$message');</script>";
//    }
//
//    if (strlen($password) < 8) {
//        $message = "Password must be at least 8 characters";
//        echo "<script>alert('$message');</script>";
//    }
//
//    if ($password !== $confirmpass) {
//        $message = "Password field should equal Confirm password";
//        echo "<script>alert('$message');</script>";
//    }
//    $db = new mysqli('localhost', 'root', '', 'restaurant');
//
//    $sql = "select *from login WHERE UserName='$uname'";
//    $result = mysqli_query($db, $sql);
//    $rowcount = mysqli_num_rows($result);
//    if ($rowcount > 0) {
//        $message = "Choose a unique username";
//        echo "<script>alert('$message');</script>";
//
//    }
//
//
//    if (count($errors) > 0) {
//        foreach ($errors as $error) {
//            echo "<div class='alert alert-danger'>$error</div>";
//        }
//    }
//    try {
//
//        $db = new mysqli('localhost', 'root', '', 'restaurant');
//        if ($db->connect_error) {
//            die("Connection failed: " . $db->connect_error);
//        }
//        $qryStr = "INSERT INTO `login` (`UserName`, `Password`, `Birthdate`, `Email`, `Type`) VALUES ('" . $uname . "', sha1('" . $password . "'), '" . $datee . "', '" . $emaill . "', '0')";
//        $rs = $db->query($qryStr);
//        $db->commit();
//        $db->close();
//        if ($rs == 1) {
//            header('Location:login.html');
//        } else {
//            echo "<h1>Choose another user name</h1>";
//        }
//
//
//    } catch (Exception $e) {
//
//    }
//
//
//}
//
//
//?>
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en" xmlns="http://www.w3.org/1999/html">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <title>Sign-Up</title>-->
<!--    <link rel="stylesheet" href="styleCss.css"/>-->
<!--</head>-->
<!--<body id="body4">-->
<!---->
<!---->
<!--<form action="signup.php" method="post">-->
<!--    <div class="blur-bg-overlay"></div>-->
<!--    <div class="p-form" id="formp">-->
<!--        <span class="close-btn material-symbols-rounded">Close</span>-->
<!--        <div class="form-box signup">-->
<!--            <div class="form-details">-->
<!--                <h1 class="h11" style="font-family: 'Snap ITC';color: #4d0099;font-size: 30px; margin-top: 15px;">Create-->
<!--                    Account</h1>-->
<!--                <p style="color: #9900cc;">Please sign up to become a part of us</p>-->
<!--            </div>-->
<!--            <div class="form-content">-->
<!--                <h1 style="font-family: 'Snap ITC';font-size: 30px;color: #ff1a1a">SignUp</h1>-->
<!--                <form action="#">-->
<!--                    <div class="in-field" class="in1">-->
<!--                        <input type="text" name="usename1" required>-->
<!--                        <label style="font-size: 15px;"> User name</label>-->
<!---->
<!---->
<!--                    </div>-->
<!--                    <div class="in-field" class="in1">-->
<!--                        <input type="date" name="date1" required>-->
<!--                        <label style="font-size: 15px;"> Birthdate </label>-->
<!---->
<!--                    </div>-->
<!---->
<!---->
<!--                    <div class="in-field">-->
<!--                        <input type="email" name="email1" required>-->
<!--                        <label style="font-size: 15px;">Enter The Email</label>-->
<!--                    </div>-->
<!--                    <div class="in-field">-->
<!--                        <input type="password" name="pass" id="pass" required>-->
<!--                        <label style="font-size: 15px">Write Password</label>-->
<!--                    </div>-->
<!--                    <div class="in-field">-->
<!--                        <input type="password" name="cpass" id="cpass" required>-->
<!--                        <label style="font-size: 15px">Confirm Password</label>-->
<!--                    </div>-->
<!---->
<!--                    <div class="policy">-->
<!--                        <input type="checkbox" id="policy">-->
<!--                        <label for="policy">-->
<!--                            I accept the-->
<!--                            <a href="termsandconditions.html">Conditions and Terms</a>-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <button type="submit" name="submitbtn">Sign Up</button>-->
<!---->
<!--                </form>-->
<!--                <div class="buttom-link" style="font-size: 1.4rem;">-->
<!--                    Already have an account?-->
<!--                    <a href="login.php" id="login-link">Login</a>-->
<!---->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <script src="jsstyle.js"></script>-->
<!--</form>-->
<!--</body>-->
<!--</html>-->
<!---->
<!---->
<!---->
