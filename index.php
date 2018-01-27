<?php

  session_start();

$error = "";

if (array_key_exists("logout", $_GET)) {
    unset($_SESSION);
    setcookie("id", "", time() - 60*60);
    $_COOKIE["id"] = "";

}

  elseif ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
    header("Location: loggedinpage.php");
}

if (array_key_exists("submit", $_POST) ) {

    include("connection.php");



    if (!$_POST['email']) {
        $error .= "email req'd<br>";
    }


    if (!$_POST['password']) {
        $error .= "password req'd<br>";
    }

    if ($error != "") {
        $error = "<p> There are error(s)</p>".$error;
    } else {
        if ($_POST['signup'] == '1') {
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

                    if ($_POST['stayLoggedIn'] == '1') {
                        setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);
                    }


                    header("Location: loggedinpage.php");
                }
            }

          } else {

            $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";

            $result = mysqli_query($link, $query);

            $row = mysqli_fetch_array($result);

            if (isset($row)) {

              $hashedPassword = md5(md5($row['id']).$_POST['password']);

                if ($hashedPassword == $row['password']) {

                  $_SESSION['id'] = $row['id'];

                    if ($_POST['stayLoggedIn'] == '1') {
                        setcookie("id", $row['id'], time() + 60*60*24*365);
                  }


                        header("Location: loggedinpage.php");
                } else {

                  $error = "Wrong password";
                }

            }
              else {
                $error = "That email/password combination cannot be found!";
              }
          }
    }
}
?>


<?php include("header.php"); ?>

    <div class="container " id="homePageContainer">

      <h1>Secret Diary</h1>

      <p><strong>Store your toughts securly</strong></p>

    <div id="error"> <?php echo $error; ?> </div>

    <form method="post" id="signUpForm">
      <p id="hh"> Interested? Sign Up now!</p>
      <div class="form-group">
      <input id="name" class="form-control" type="text" name="name" placeholder="name">

    </div>
<div class="form-group">
      <input id="email" class="form-control"  name="email" placeholder="email">

    </div>
<div class="form-group">

      <input type="password" class="form-control" name="password" placeholder="password">

    </div>
<div class="form-check">
      <input type="checkbox" class="form-check-input" name="stayLoggedIn" value=1>
      <label class="form-check-label" for="password">Stay Logged In</label>
    </div>
<div class="form-group">
      <input type="hidden" name="signup" value="1">
      <input type="submit" class="btn btn-outline-info" name="submit" value="Sign Up">
    </div>

    <p>
      <button type="button" class="btn btn-primary toggleForms" data-toggle="button" aria-pressed="false" autocomplete="off" >Log In</button>
    </p>

    </form>

    <form method="post" id="logInForm">
      <p id="hh">Log In using tour username and password.</p>

      <div class="form-group">

      <input id="email" class="form-control" type="email" name="email" placeholder="email">
    </div>
<div class="form-group">

      <input type="password" class="form-control" name="password" placeholder="password">

    </div>
    <div class="form-check">
          <input type="checkbox" class="form-check-input" name="stayLoggedIn" value=1>
          <label class="form-check-label" for="password">Stay Logged In</label>
        </div>
<div class="form-group">
      <input type="hidden" name="signup" value="0">
      <input type="submit" class="btn btn-outline-info" name="submit" value="Log In">
    </div>

    <p>
      <button type="button" class="btn btn-primary toggleForms" data-toggle="button" aria-pressed="false" autocomplete="off" >Sign Up</button>
    </p>

    </form>

        </div>

  <?php include("footer.php") ?>
