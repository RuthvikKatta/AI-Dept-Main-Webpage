<?php
include '../users/models/Staff.php';

$staff = new Staff();

$role = isset($_GET['role']) ? $_GET['role'] : 'Teaching';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Staff</title>

    <link rel="stylesheet" href="/AI-Main-Page/style.css">
    <link rel="stylesheet" href="./staff.style.css">
    <link rel="shortcut icon" href="/AI-Main-Page/assets/Images/favicon-icon.png" type="image/x-icon">

    <script src="/AI-Main-Page/CustomElements/AppHeaderElement.js" defer></script>
</head>

<body>
    <app-header></app-header>
    <section class="profiles-container">
        <h1 class="title">
            <?php echo $role . ' Staff' ?>
        </h1>
        <div class="staff-profiles">
            <?php
            $data = $staff->getStaffByRole($role);
            foreach ($data as $row) {

                $designation = $staff->getDesignation($row['designation_id']);

                $profileImagePath = '../Database/Staff/' . $row['staff_id'] . '.jpeg';

                if (!file_exists($profileImagePath)) {
                    $profileImagePath = '../Database/Staff/' . $row['staff_id'] . '.jpg';

                    if (!file_exists($profileImagePath)) {
                        $profileImagePath = '../assets/Icons/' . ($row['gender'] == 'Male' ? 'Male.png' : 'Female.png');
                    }
                }

                echo '<div class="profile">
                            <div class="profile-image">
                                <img src="' . $profileImagePath . '" alt="Profile" width="150">
                            </div>
                            <div class="profile-details">
                                <h2>
                                    <a href="../Profile/index.php?id=' . $row['staff_id'] . '">
                                    ' . $row['salutation'] . ' ' . $row['first_name'] . ' ' . $row['last_name'] . '
                                    </a>
                                </h2>
                                <h3>' . $designation['title'] . '</h3>
                                <p>' . $row['qualification'] . '</p>
                            </div>
                          </div>';
            }
            ?>
        </div>
    </section>
</body>

</html>