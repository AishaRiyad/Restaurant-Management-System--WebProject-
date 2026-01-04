<?php
include ('adminproduct.php');
if (isset($_GET['id'])&& isset($_GET['image_name'])){
$id=$_GET['id'];
$image_name=$_GET['image_name'];

if ($image_name!=""){
    $path="../images/productimg/".$image_name;
    $remove=unlink($path);
    if ($remove==false){
        echo "false";
    }
    $db = new mysqli('localhost', 'root', '', 'restaurant');
    $sql="DELETE FROM `products` WHERE `id`='$id'";
    $res=mysqli_query($db,$sql);
    if ($res==true){
        echo "true";
    }
}
}
?>