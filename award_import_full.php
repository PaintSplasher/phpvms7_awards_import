<?php
  /*
   * Award Import v1.2
   * Copyright 2024 Sass-Projects (https://www.sass-projects.info)
   * Licensed under GNU GENERAL PUBLIC LICENSE v3.0
   * (https://github.com/PaintSplasher/phpvms7_awards_import/blob/main/README.md)
  */
  
  $upOne = dirname(__DIR__, 1);
  $dotenvPath = $upOne . '/.env';
  
  if (file_exists($dotenvPath)) {
    require_once $upOne . "/vendor/autoload.php";
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($dotenvPath));
    $dotenv->load();
  } else {
      die('.env Datei nicht gefunden.');
  }
  
  $dbHost     = $_ENV['DB_HOST'];
  $dbDatabase = $_ENV['DB_DATABASE'];
  $dbUsername = $_ENV['DB_USERNAME'];
  $dbPassword = $_ENV['DB_PASSWORD'];
  
  $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
  
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  
    step1();
    step2();
    step3();
    step4();
    step5();
    die("<b>All done.</b>");
  
    function step1() {
      global $conn;
  
      $sql_trunca = "TRUNCATE TABLE phpvms7_awards";
      $conn->query($sql_trunca);
  
      $sql_select = "SELECT awardid, name, descrip, image FROM phpvms_awards";
      $sql_insert = "INSERT INTO phpvms7_awards (id, name, description, image_url, active, created_at) VALUES (?, ?, ?, ?, 1, NOW())";
      $stmt = $conn->prepare($sql_insert);
  
      if (!$stmt) {
        die("Prepare failed: " . $conn->error);
      }
  
      $result = $conn->query($sql_select);
  
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $stmt->bind_param("isss", $row['awardid'], $row['name'], $row['descrip'], $row['image']);
          $stmt->execute();
        }
          echo "<b>Step1</b>: Data from table phpvms_awards successfully imported into table phpvms7_awards.<br><hr>";
        } else {
          echo "<b>Step1</b>: No data found in table phpvms_awards.<br><hr>";
        }
  
    }
  
    function step2() {
      global $conn;
  
      $sql_select = "SELECT awardid, achieve FROM phpvms_awards_auto";
      $sql_update = "UPDATE phpvms7_awards AS p7a
                     JOIN phpvms_awards_auto AS paa ON p7a.id = paa.awardid
                     SET p7a.ref_model_params = paa.achieve";
  
      if ($conn->query($sql_update) === TRUE) {
        echo "<b>Step2</b>: Data from table phpvms_awards_auto successfully imported into table phpvms7_awards.<br><hr>";
      } else {
        echo "<b>Step2</b>: Error importing data: " . $conn->error . "<br><hr>";
      }
  
    }
  
    function step3() {
      global $conn;
  
      $sql_trunca = "TRUNCATE TABLE phpvms7_user_awards";
      $conn->query($sql_trunca);
  
      $sql_select = "SELECT awardid, pilotid, dateissued FROM phpvms_awardsgranted";
      $sql_insert = "INSERT INTO phpvms7_user_awards (award_id, user_id, created_at) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql_insert);
  
      if (!$stmt) {
          die("Prepare failed: " . $conn->error);
      }
  
      $result = $conn->query($sql_select);
  
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $dateissued = $row['dateissued'];
  
              $dateTime = DateTime::createFromFormat('d/m/y', $dateissued);
              if ($dateTime === false) {
                  $dateTime = DateTime::createFromFormat('Y-m-d', $dateissued);
              }
              if ($dateTime === false) {
                  die("Date format conversion failed for dateissued: " . $dateissued);
              }
              $created_at = $dateTime->format('Y-m-d H:i:s');
  
              $stmt->bind_param("iis", $row['awardid'], $row['pilotid'], $created_at);
              $stmt->execute();
          }
          echo "<b>Step3</b>: Data from table phpvms_awardsgranted successfully imported into table phpvms7_user_awards.<br><hr>";
      } else {
          echo "<b>Step3</b>: No data found in table phpvms_awardsgranted.<br><hr>";
      }
  
      $stmt->close();
  } 
  
  function step4() {
    global $conn;
  
    $sql_select_user_awards = "SELECT user_id FROM phpvms7_user_awards";
    $result_user_awards = $conn->query($sql_select_user_awards);
  
    if ($result_user_awards->num_rows > 0) {
        while($row = $result_user_awards->fetch_assoc()) {
            $user_id = $row['user_id'];
  
            $sql_check_user = "SELECT pilot_id FROM phpvms7_users WHERE pilot_id = $user_id";
            $result_check_user = $conn->query($sql_check_user);
  
            if ($result_check_user->num_rows == 0) {
                $sql_delete = "DELETE FROM phpvms7_user_awards WHERE user_id = $user_id";
                if ($conn->query($sql_delete) === TRUE) {
                    echo "<b>Step4</b>: User ID $user_id not found in phpvms7_users. Deleted from phpvms7_user_awards.<br><hr>";
                } else {
                    echo "<b>Step4</b>: Error deleting user ID $user_id: " . $conn->error . "<br><hr>";
                }
            }
        }
    } else {
        echo "<b>Step4</b>: No user IDs found in phpvms7_user_awards.<br><hr>";
    }
  }
  
  function step5() {
    global $conn;
    
    $sql_update = "UPDATE phpvms7_user_awards ua
                   JOIN phpvms7_users u
                   ON ua.user_id = u.pilot_id
                   SET ua.user_id = u.id";
                   
    if ($conn->query($sql_update) === TRUE) {
      echo "<b>Step5</b>: User IDs in phpvms7_user_awards successfully updated.<br><hr>";
    } else {
      echo "<b>Step5</b>: Error updating User IDs in phpvms7_user_awards: " . $conn->error . "<br><hr>";
    }
    
    $conn->close();
  }
  
  ?>
