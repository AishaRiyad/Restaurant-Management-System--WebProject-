<?php
session_start();
$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
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
    header("Location: profile.php");
    exit;
}
if(isset($_GET['update'])){
    header("Location:editprofile.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!--    <link rel="stylesheet" href="styleCss.css" />-->
    <style>
        body{
            background:linear-gradient(to right,#2ed573,#f9ca24);
            overflow-x: hidden;
        }
        .container{
            background: #fff;
            width: 600px;
            height: 550px;
            margin: 0 auto;
            position: relative;
            margin-top: 7%;
            box-shadow: 2px 5px 20px rgba(119,119,119,0.5);
        }
        .left-box{
            float: left;
            top: -5%;
            left: 5%;
            position: absolute;
            width: 15%;
            height: 110%;
            background: #2ed573;
            box-shadow: 2px 5px 20px rgba(119,119,119,0.5);
            border: 0.1em solid #fff;
        }

        nav a{
            list-style: none;
            padding: 35px;
            color: #fff;
            font-size: 1.1em;
            display: block;
            transition: all 0.3s ease-in-out;
        }
        nav a:hover{
            color: #10ac84;
            cursor: pointer;
            transform: scale(1.2);
        }
        nav a:first-child{
            margin-top: 7px;

        }
        nav a.active{
            color: #10ac84 ;
        }
        .rigtbox{
            width: 60%;
            margin-left: 27%;

        }
        .tab{
            transition: all 0.5s ease-in-out;
            width: 80%;
        }
        h1{
            font-family: "Snap ITC";
            color: #7ed386;
            font-size: 37px;
            margin-top: 40px;
            margin-bottom: 35px;
        }
        h2{
            color: #777;
            font-family: "Bookman Old Style";
            font-size: 23px;
            margin-left: 2px;
            margin-top: 10px;
        }
        .btn{
            display: block;
            font-family: "Snap ITC";
            text-transform: uppercase;
            font-size: 30px;
            color: #fff;
            border: 0;
            background: #7ed386;
            padding: 7px 15px;
            box-shadow: 0px 2px 4px 0px rgba(0, 0 ,0 ,0.2);
            cursor: pointer;
            margin-top: 15px;
        }

        .input-text{
            position: relative;
            border-bottom: 2px solid limegreen;
            margin: 15px 0;
        }

        .input-text label{
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            color: dodgerblue;
            font-size: 20px;
            pointer-events: none;
            transition: 0.13s ease;

        }

        .input-text input{
            width: 100%;
            height: 40px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 20px;
            color: deeppink;
        }

        .input-text input:focus~label,
        .input-text input:valid~label{
            font-size: 1.2rem;
            top: 15px;
            transform: translateY(-120%);
        }
    </style>
</head>
<body>
<header>

    <a href="userwelcome.html" style="color: red;font-size:27px;">Home</a>


</header>
<?php if (isset($_SESSION['edit_error'])): ?>
    <div class="error"><?php echo $_SESSION['edit_error']; ?></div>
    <?php unset($_SESSION['edit_error']); ?>
<?php endif; ?>
<form action="" method="get">
    <div class="container">
        <div class="left-box">
            <nav>
                <a onclick="tabs(0);" class="tab active" href="profile.php"><i class="fa fa-user"></i></a>
                <a onclick="tabs(1);" class="tab active" href="change-password.html"><i class="fas fa-user-lock"></i></a>
                <a onclick="tabs(2);" class="tab" href="login.php"><i class="fas fa-sign-out-alt"></i></a>
            </nav>
        </div>
        <div class="rigtbox">
            <h1>Personal Info</h1>
            <div class="input-text">
                <input type="text" name="usernamep" value="<?php echo htmlspecialchars($uname); ?>" required disabled>
            </div>
            <div class="input-text">
                <input type="date" name="Bdatep" value="<?php echo htmlspecialchars($profile['Birthdate']); ?>" disabled>
            </div>
            <div class="input-text">
                <input type="email" name="emailp" value="<?php echo htmlspecialchars($profile['Email']); ?>" disabled>
            </div>

            <button class="btn" name="update">Edit Profile</button>

        </div>

    </div>
</form>
</body>
</html>
