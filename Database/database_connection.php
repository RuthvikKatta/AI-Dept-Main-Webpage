<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "college_website_test_db";
    $tablename = "projects_test_table";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>