<?php

if (isset($_POST['submit'])) {

  include_once 'dbh.inc.php';

  $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
  $uid = mysqli_real_escape_string($conn, $_POST['uid']);
  $emailaddress = mysqli_real_escape_string($conn, $_POST['emailaddress']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  //Error handlers
  //Check for empty fields
  if(empty($firstname)||empty($lastname)||empty($uid)||empty($emailaddress)||empty($password)) {
    header("Location: ../index2.php?signup=empty");
    exit();
  } else {
    //Check if input characters are valid
    if(!preg_match("/^[a-zA-Z]*$/", $firstname) || !preg_match("/^[a-zA-Z]*$/", $lastname)){
      header("Location: ../index2.php?signup=invalid");
      exit();
    }else{
      //check if email is valid
      if (!filter_var($emailaddress, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../index2.php?signup=email");
        exit();
      }else {
        //check if the username is taken
        $sql = "SELECT * FROM users WHERE user_uid='$uid'";
        $result = mysqli_query($conn,$sql);
        $resultCheck = mysqli_num_rows($result);

        if ($resultCheck > 0) {
          header("Location: ../index2.php?signup=usertaken");
          exit();
        }else {
          //hashing the password
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          //insert the user into the database
          $sql = "INSERT INTO users (user_uid,user_first, user_last, user_email,  user_pwd) VALUES ('$uid','$firstname','$lastname','$emailaddress','$hashedPassword');";
          mysqli_query($conn,$sql);
          header("Location: ../index2.php?signup=success");
          exit();
        }
      }
    }
  }


}else{
  header("Location: ../tickets.php");
  exit();
}
