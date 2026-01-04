<?php
session_start();

$db = new mysqli('localhost', 'root', '', 'restaurant');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$stmt = $db->prepare("SELECT * FROM `aboutus` ");

$stmt->execute();
$result = $stmt->get_result();
$about = $result->fetch_assoc();
$stmt->close();
if ($about) {
    $head = $about['head'];
    $description = $about['para'];

} else {
    $_SESSION['edit_error'] = " not found.";
    header("Location: adminabout.php");
    exit;
}
if (isset($_POST['updatebtn'])) {
    $head = $_POST['head'];
    $description = $_POST['para'];
    $stmt = $db->prepare("UPDATE `aboutus` SET `head`=?, `para`=?");
    $stmt->bind_param("ss", $head, $description);
    $res2 = $stmt->execute();
    $stmt->close();


    if ($res2) {

        echo "<script>alert('About updated successfully');</script>";
        echo "<script>window.location = 'adminabout.php';</script>";
    } else {
        echo "<script>alert('Error updating About');</script>";
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
            if (confirm("Are you sure you want to update this product?")) {
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
    <a href="adminabout.php" style="color: red;font-size:27px;">Back</a>

</header>
<div class="container">
    <div class="form-container">
        <h1>Edit About</h1>


        <?php if (isset($_SESSION['edit_error'])): ?>
            <div class="error"><?php echo $_SESSION['edit_error']; ?></div>
            <?php unset($_SESSION['edit_error']); ?>
        <?php endif; ?>

        <form id="editForm" action="editabout.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Header:</label>
                <input type="text" id="title" name="head" placeholder="Head" value="<?php echo htmlspecialchars($head); ?>" >

            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" id="description" name="para" placeholder="Description" value="<?php echo htmlspecialchars($description); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="updatebtn" class="btn" onclick="confirmUpdate(event)">Edit About</button>
            </div>

        </form>
    </div>
</div>
</body>
</html>
