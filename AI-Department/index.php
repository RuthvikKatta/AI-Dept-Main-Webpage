<?php

include '../users/models/Media.php';

$media = new Media();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Artificial Intelligence - AI</title>
  <link rel="shortcut icon" href="../assets/Images/favicon-icon.png" type="image/x-icon" />

  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="./ai.style.css" />
</head>

<body>
  <app-header></app-header>
  <div class="container" id="Home">
    <vision-mission></vision-mission>
    <div class="carousal-container">
      <h1>Gallery of AI</h1>
      <div class="carousal">
        <img class="carousal-controller move-left" src="../assets/Icons/chevron-left-solid.svg"
          onclick="showAdjacentSlides(true)" width="35" alt="left-move-icon" />

        <?php
        $carousalImages = $media->getCarousalImages();


        if (count($carousalImages) > 0) {
          foreach ($carousalImages as $row) {
            echo '<div class="mySlides fade">';
            echo '<img src="../Database/Carousal Images/' . $row['file_name'] . '" style="width: 100%" />';
            echo '</div>';
          }
        }
        ?>

        <img class="carousal-controller move-right" src="../assets/Icons/chevron-right-solid.svg"
          onclick="showAdjacentSlides(false)" width="35" alt="right-move-icon" />
      </div>
    </div>
    <div class="about">
      <div>
        <h1>Head of the Department</h1>
      </div>
      <div>
        <div class="image-container">
          <img src="../assets/Images/HOD.jpeg" alt="Image" width="200" />
          <p>Dr. A. Obulesh</p>
          <p>M.Tech, Ph.D</p>
        </div>
        <p class="about-hod">
          <span>Dr. A. Obulesh</span> working as Assoc. Prof. at Vidya Jyothi
          Institute of Technology, in the Dept. of Artificial Intelligence has
          an incredible experience of 14 years in academics under various
          capacities. He received his Ph.D. in Computer Science & Engineering
          from Jawaharlal Nehru Technological University, Kakinada (JNTUK),
          India. The M.Tech. in CSE from Rajeev Gandhi Memorial College of
          Engineering and Technology(RGMCET), Nandyal which is affiliated to
          JNT University, Hyderabad, in 2006. He has published 32 research
          papers in various National, International Scopus/ SCI journals and
          conferences proceedings. He worked as Convener for Artificial
          Intelligence and Machine learning Research Wing and he established
          Machine Learning and Artificial Intelligence (MALAI) Club in
          association with Wave Labs in Anurag University (AU), Hyderabad
          within the Department.
        </p>
      </div>
    </div>
  </div>
</body>
<script src="../script.js"></script>
<script src="../CustomElements/AppHeaderElement.js"></script>
<script src="../CustomElements/VisionMissionElement.js"></script>

</html>