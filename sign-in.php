<?php

  session_start();

$error = "";

if(array_key_exists("logout", $_GET)) {

  unset($_SESSION);
  setcookie("id", "", time() - 60*60);
  $_COOKIE["id"] = "";

}  elseif (array_key_exists("id", $_SESSION) OR array_key_exists("id", $_COOKIE)) {

  header("Location: loggedinpage.php");
}

if (array_key_exists('name', $_POST) OR array_key_exists('email', $_POST) OR array_key_exists('password', $_POST)) {

$link = mysqli_connect("localhost","root","","s-diary");

if (mysqli_connect_error()) {

  die ("error");
}



if (!$_POST['email']) {

  $error .= "email req'd<br>";
}


     if (!$_POST['password']) {

       $error .= "password req'd<br>";
     }

     if ($error != "") {

       $error = "<p> There are error(s)</p>".$error;

     } else {

       if($_POST['signup'] == '1') {

       $query = "SELECT `id` FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";

       $result = mysqli_query($link, $query);

         if (mysqli_num_rows($result) > 0) {
          echo "email taken";
         } else {

             $query = "INSERT INTO `users` (`name`, `email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['name'])."', '".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";

           if (!mysqli_query($link, $query)) {

          $error = "<p> Couldnt sign u up</p>";
        } else {

          $query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";

          mysqli_query($link, $query);

          $_SESSION['id'] = mysqli_insert_id($link);

          if($_POST['stayLoggedIn'] == '1') {

            setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);
          }


          header("Location: loggedinpage.php");
        }

        }

      } else {
        
      } 

    }
}




 ?>

  <html>

  <head>
    <meta charset="utf-8">
    <title>form</title>
  </head>

  <body>

    <div id="error">
      <?php echo $error; ?> </div>

    <form method="post">
      <label for="name">name</label>
      <input id="name" type="text" name="name" placeholder="name">
      <br>
      <label for="email">email</label>
      <input id="email" type="email" name="email" placeholder="email">
      <br>
      <label for="password">password</label>
      <input type="password" name="password" placeholder="password">
      <br>
      <br>
      <input type="checkbox" name="stayLoggedIn" value=1>
      <input type="hidden" name="signup" value="1">
      <input type="submit" name="submit" value="Sign Up">

    </form>

    <form method="post">
      <label for="name">name</label>
      <input id="name" type="text" name="name" placeholder="name">
      <br>
      <label for="email">email</label>
      <input id="email" type="email" name="email" placeholder="email">
      <br>
      <label for="password">password</label>
      <input type="password" name="password" placeholder="password">
      <br>
      <br>
      <input type="checkbox" name="stayLoggedIn" value=1>
      <input type="hidden" name="signup" value="0">
      <input type="submit" name="submit" value="Log In">

    </form>
  </body>

  </html>