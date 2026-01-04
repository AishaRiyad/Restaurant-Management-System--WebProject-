<?php
session_start();
$message = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST['username'];
    $upass = $_POST['password1'];

    try {
        $db = new mysqli('localhost', 'root', '', 'restaurant');
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }


        $qryStr = "SELECT * FROM login WHERE UserName = '$uname'";
        $res = $db->query($qryStr);

        if ($res->num_rows > 0) {
            $rows = $res->fetch_object();

            if (sha1($upass) == $rows->Password) {
                $_SESSION['UserName'] = $uname;
                if ($rows->Type == 0) {
                    header('Location: userwelcome.html');
                    exit();
                } else if ($rows->Type == 1) {
                    header('Location: adminwelcomep.html');
                    exit();
                }
            } else {
                $message = "Invalid Username or Password";
            }
        } else {
            $message = "Invalid Username or Password";
        }
        $db->close();
    } catch (Exception $e) {
        $message = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styleCss.css"/>
    <style>
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body id="body4">

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="blur-bg-overlay"></div>
    <div class="p-form" id="formp">
        <span class="close-btn material-symbols-rounded">Close</span>
        <div class="form-box login">
            <div class="form-details">
                <h1 class="h11" style="font-family: 'Snap ITC';color: #4d0099;font-size: 30px; margin-top: 15px;">
                    Welcome To You</h1>
                <p style="color: #9900cc;">Please log in using your information to see our updated</p>
            </div>
            <div class="form-content">
                <h1 style="font-family: 'Snap ITC';font-size: 30px;color: #ff1a1a">LOGIN</h1>

                <!-- Error message display -->
                <?php if (!empty($message)): ?>
                    <div class="error-message"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="in-field">
                    <input type="text" name="username" required>
                    <label style="font-size: 15px;">Username</label>
                </div>
                <div class="in-field">
                    <input type="password" name="password1" required>
                    <label style="font-size: 15px">Password</label>
                </div>
                <a href="forgot-password.html" class="forget">Forgot Password?</a>
                <button type="submit" name="submitbtn">LOG IN</button>

                <div class="buttom-link" style="font-size: 1.4rem;">
                    Don't have an account?
                    <a href="signup.php" id="signup-link">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="jsstyle.js"></script>
</body>
</html>




<?php
//session_start();
//$message = '';
//if (isset($_POST['username']) && isset($_POST['password1'])) {
//    $uname = $_POST['username'];
//    $upass = $_POST['password1'];
//
//    try {
//        $db = new mysqli('localhost', 'root', '', 'restaurant');
//        if ($db->connect_error) {
//            die("Connection failed: " . $db->connect_error);
//        }
//        $qryStr = "select *from login";
//        $res = $db->query($qryStr);
//        $isValidUser = false;
//        for ($i = 0; $i < $res->num_rows; $i++) {
//            $rows = $res->fetch_object();
//            if ($rows->UserName == $uname && sha1($upass) == $rows->Password) {
//                $_SESSION['UserName'] = $uname;
//                if ($rows->Type == 0) {
//                    header('Location:userwelcome.html');
//                    exit();
//                } else if ($rows->Type == 1) {
//                    header('Location:adminwelcomep.html');
//                    exit();
//                }
//            }
//            $message = "INVALID Username or Password";
//        }
//        $db->close();
//
//    } catch (Exception $e) {
//        $message = "An error occurred: " . $e->getMessage();
//    }
//}
//
//
//?>
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <title>Login</title>-->
<!--    <link rel="stylesheet" href="styleCss.css"/>-->
<!--</head>-->
<!--<body id="body4">-->
<?php
//if (!empty($message)) {
//    echo "<script>alert('$message');</script>";
//}
//?>
<!---->
<!---->
<!--<form action="login.php" method="post">-->
<!--    <div class="blur-bg-overlay"></div>-->
<!--    <div class="p-form" id="formp">-->
<!--        <span class="close-btn material-symbols-rounded">Close</span>-->
<!--        <div class="form-box login">-->
<!--            <div class="form-details">-->
<!--                <h1 class="h11" style="font-family: 'Snap ITC';color: #4d0099;font-size: 30px; margin-top: 15px;">-->
<!--                    Welcome To You</h1>-->
<!--                <p style="color: #9900cc;">Please log in using your information to see our updated</p>-->
<!--            </div>-->
<!--            <div class="form-content">-->
<!--                <h1 style="font-family: 'Snap ITC';font-size: 30px;color: #ff1a1a">LOGIN</h1>-->
<!--                <form action="#">-->
<!--                    <div class="in-field">-->
<!--                        <input type="text" name="username" required>-->
<!--                        <label style="font-size: 15px;">Username</label>-->
<!--                    </div>-->
<!--                    <div class="in-field">-->
<!--                        <input type="password" name="password1" required>-->
<!--                        <label style="font-size: 15px">Password</label>-->
<!--                    </div>-->
<!--                    <a href="forgot-password.html" class="forget">Forgot Password?</a>-->
<!--                    <button type="submit" name="submitbtn">LOG IN</button>-->
<!---->
<!--                </form>-->
<!--                <div class="buttom-link" style="font-size: 1.4rem;">-->
<!--                    Don't have an account?-->
<!--                    <a href="signup.php" id="signup-link">Sign Up</a>-->
<!---->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <script src="jsstyle.js"></script>-->
<!--</form>-->
<!--</body>-->
<!--</html>-->
