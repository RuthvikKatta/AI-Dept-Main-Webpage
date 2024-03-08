<?php
include '../users/models/Staff.php';

$staff_id = $_GET['id'];

$staff = new Staff();
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
    $row = $staff->getStaffDetails($staff_id);
    if (count($row) > 0) {

        $profileImagePath = '../Database/Staff/' . $row['staff_id'] . '.jpeg';
        
        if (!file_exists($profileImagePath)) {
            $profileImagePath = '../Database/Staff/' . $row['staff_id'] . '.jpg';

            if (!file_exists($profileImagePath)) {
                $profileImagePath = '../assets/Icons/' . ($row['gender'] == 'Male' ? 'Male.png' : 'Female.png');
            }
        }

        $designation_id = $row['designation_id'];
        $designation = $staff->getDesignation($row['designation_id']);

        ?>
        <div class="profile-container">
            <div class="profile-image">
                <img src="<?php echo $profileImagePath ?>" alt="Profile Image">
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
                        <?php echo $designation['title']; ?>
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
        echo "<h2>No Staff found.</h2>";
    }
    ?>
</body>

</html>