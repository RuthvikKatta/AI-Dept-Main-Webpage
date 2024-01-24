<?php
include '../Connection/Connection.php';

$staff_id = $_GET['id'];
$tablename = 'staff_test';

$staff_query = "SELECT * FROM $tablename WHERE staff_id = '$staff_id'";
$result = $conn->query($staff_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <link rel="stylesheet" href="/AI-Main-Page/style.css">
    <link rel="stylesheet" href="./staff.style.css">
    <link rel="shortcut icon" href="/AI-Main-Page/assets/Images/favicon-icon.png" type="image/x-icon">

    <script src="/AI-Main-Page/CustomElements/AppHeaderElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <?php
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        echo $row['first_name'];
        echo $row['middle_name'];
        echo $row['last_name'];
        echo $row['age'];
        echo $row['gender'];
        echo $row['salutation'];
        echo $row['qualification'];
    }

    mysqli_close($conn);
    ?>
</body>

</html>