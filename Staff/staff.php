<?php
include '../Connection/Connection.php';

$role = $_GET['role'];
$tablename = 'staff_test';

$query = "SELECT * FROM $tablename where role = '$role' ORDER BY designation_id DESC";
$result = $conn->query($query);
$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
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
            <?php echo $role .' Staff'?>
        </h1>
        <div class="staff-profiles">
            <?php
            foreach ($data as $row) {
                $designation_table = 'designation_test';
                $designation_query = "SELECT title FROM $designation_table WHERE designation_id = ?";

                $stmt = $conn->prepare($designation_query);
                $stmt->bind_param("i", $row['designation_id']);
                $stmt->execute();

                $designation_result = $stmt->get_result();
                $designation = $designation_result->fetch_assoc()['title'];

                $profile_image = $row['profile_image_link'] == '' ? 
                                    '../assets/Icons/' . ($row['gender'] == 'Male' ? 'Male.png' : 'Female.png') :
                                    '../' .$row['profile_image_link'];

                echo '<div class="profile">
                            <div class="profile-image">
                                <img src="'. $profile_image .'" alt="Profile">
                            </div>
                            <div class="profile-details">
                                <h2>
                                    <a href="../Profile/profile.php?id='. $row['staff_id'] .'">
                                    '. $row['salutation'] .' ' . $row['first_name'] . ' ' . $row['last_name'] . '
                                    </a>
                                </h2>
                                <h3>' . $designation . '</h3>
                                <p>' . $row['qualification'] . '</p>
                            </div>
                          </div>';

                $stmt->close();
            }

            mysqli_close($conn);
            ?>
        </div>
    </section>
</body>

</html>