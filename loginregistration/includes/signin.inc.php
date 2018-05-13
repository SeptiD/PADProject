<?php

session_start();

if(isset($_POST['signin'])){

  include 'dbh.inc.php';

  $uid = mysqli_real_escape_string($conn, $_POST['uid']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  //error handlers
  //check if inputs are EmptyIterator
  if (empty($uid)||empty($password)) {
    header("Location: ../index2.php?signin=empty");
    exit();
  } else {
    $sql = "SELECT * FROM users WHERE user_uid='$uid'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck < 1) {
      header("Location: ../index2.php?signin=nonexistinguser");
      exit();
    }else {
      if($row = mysqli_fetch_assoc($result)){
        //de-hashing the password
        $hashedPwdCheck = password_verify($password,$row['user_pwd']);
        if ($hashedPwdCheck == false) {
          header("Location: ../index2.php?signin=error");
          exit();
        }elseif($hashedPwdCheck == true) {
          //log in the user here
          $_SESSION['u_id'] = $row['user_id'];
          $_SESSION['u_first'] = $row['user_first'];
          $_SESSION['u_last'] = $row['user_last'];
          $_SESSION['u_email'] = $row['user_email'];
          $_SESSION['u_uid'] = $row['user_uid'];
          header("Location: ../charts.php?signin=success");
          exit();
        }else {
          header("Location: ../index2.php?signin=error");
          exit();
        }
      }else {
        header("Location: ../index2.php?signin=error");
        exit();
      }
    }
  }
}else {
  header("Location: ../index2.php?signin=error");
  exit();
}
