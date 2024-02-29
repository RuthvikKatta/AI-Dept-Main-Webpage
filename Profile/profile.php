<?php
include '../Connection/Connection.php';

$staff_id = $_GET['id'];
$tablename = 'staff';

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
    <link rel="stylesheet" href="./profile.style.css">
    <link rel="shortcut icon" href="/AI-Main-Page/assets/Images/favicon-icon.png" type="image/x-icon">

    <script src="/AI-Main-Page/CustomElements/AppHeaderElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <?php
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $profile_image = $row['profile_image_link'] == '' ?
            '../assets/Icons/' . ($row['gender'] == 'Male' ? 'Male.png' : 'Female.png') :
            '../' . $row['profile_image_link'];

        $designation_id = $row['designation_id'];

        $designation_query = "SELECT title FROM Designation WHERE designation_id = '$designation_id'";
        $designation_result = $conn->query($designation_query);

        $designation_row = $designation_result->fetch_assoc();
        $designation_title = $designation_row['title'];

        ?>
        <div class="profile-container">
            <div class="profile-image">
                <img src="<?php echo $profile_image ?>" alt="Profile Image">
            </div>
            <div class="profile-info">
                <h2 class="profile-name">
                    <?php echo $row['salutation'] . ' ' . $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?>
                </h2>
                <p>
                    <?php echo $row['qualification']; ?>
                </p>
                <p>
                    <strong>
                        <?php echo $designation_title; ?>
                    </strong>
                </p>
                <p>
                    <?php echo 'Experience: ' . $row['experience_years'] . ' years'; ?>
                </p>
            </div>
            <div class="contact-details">
                <p>
                    <?php echo 'Mobile Number: ' . $row['mobile_number']; ?>
                </p>
                <p>
                    <?php echo 'Email: ' . $row['email']; ?>
                </p>
            </div>
        </div>
        <?php
    } else {
        echo "No records found.";
    }
    mysqli_close($conn);
    ?>
</body>

</html>