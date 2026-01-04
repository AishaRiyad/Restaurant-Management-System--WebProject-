<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
var_dump($_SESSION);

if (!isset($_SESSION['UserName'])) {
    echo "You are not logged in.";
    exit();
}


if(isset($_SESSION['UserName'])){
    $db = new mysqli('localhost', 'root', '', 'restaurant');
    if(isset($_POST['pass1'])&&isset($_POST['pass2'])&&isset($_POST['pass3'])){
        function validate($data)
        {
            $data=trim($data);
            $data=stripslashes($data);
            $data=htmlspecialchars($data);
            return $data;
        }
        $pass1=validate($_POST['pass1']);
        $pass2=validate($_POST['pass2']);
        $pass3=validate($_POST['pass3']);
        if(empty($pass1)){
            header("Location:change-password.html?error=Old password can not be empty");
            exit();
        }else if (empty($pass2)){
            header("Location:change-password.html?error=New password can not be empty");
            exit();
        }else if ($pass2!==$pass3){
            header("Location:change-password.html?error=New Password and Confirm New password should be match");
            exit();
        }else{
           $pass1=sha1($pass1);
           $pass2=sha1($pass2);
           $username=$_SESSION['UserName'];
//           $sql="SELECT Password FROM login WHERE 'UserName=$username AND Password=$pass1'";
           $sql = "SELECT `Password` FROM `login`  WHERE `UserName`='$username' AND `Password`='$pass1'";
            $result=mysqli_query($db,$sql);

           if (mysqli_num_rows($result)===1){
               $sql2="UPDATE `login` SET `Password`='$pass2' WHERE `UserName`='$username'";
               mysqli_query($db,$sql2);
               echo "correct";
//               header("Location:login.html");
           }else{
               echo "$pass1";
           }
        }

    }
//    $result=mysqli_query($db,$sql);

}
//if(isset($_POST['change'])) {
//
//}
//    $oldp = $_POST['pass1'];
//    $newp = $_POST['pass2'];
//    $cnewp = $_POST['pass3'];
//    $passwordhash = password_hash($newp, PASSWORD_DEFAULT);
//    $errors2 = array();
//    if (empty($oldp) or empty($newp) or empty($cnewp)) {
//        array_push($errors2, "All fields are required");
//    }
//    if (strlen($newp) < 8) {
//        array_push($errors2, "Password must be at least 8 characters");
//    }
//    if ($newp !== $cnewp) {
//        array_push($errors2, "Password field should equal Confirm password");
//    }
//    if (count($errors2) > 0) {
//        foreach ($errors2 as $error) {
//            echo "<div class='alert alert-danger'>$error</div>";
//        }
//    }
//    try {
//
//        $db = new mysqli('localhost', 'root', '', 'restaurant');
//        if ($db->connect_error) {
//            die("Connection failed: " . $db->connect_error);
//        }
//        $sql="select *from login ";
//        $result=mysqli_query($db,$sql);
//        $rowcount=mysqli_num_rows($result);
//        if ($rowcount>0){
//            array_push($errors2,"Choose a unique username");
//        }
//        $qryStr="UPDATE `login` SET `Password`='[.$newp.]' WHERE `UserName` = '[..]' ";
//        $rs=$db->query($qryStr);
//        $db->commit();
//        $db->close();
//        if($rs==1){
//            header('Location:login.html');
//        }
//        else{
//            echo "<h1>Choose another user name</h1>";
//        }
//
//
//    }catch (Exception $e){
//
//    }
//    }
?>
