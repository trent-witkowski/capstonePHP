<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resumate</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <div class="banner">
        <h1>Resumate</h1>
        <span><a href="login.php?pageType=login">Login/Sign up</a></span>
    </div>
    <div class="navDiv">
        <table class="navTable">
            <tbody>
<!--            This is the nav, painted by using a table-->
            <tr>
                <td></td>
                <td class="navCell">
                    <span><a href="index.php">Home</a></span>
                </td>
                <td class="navCell">
                    <span><a href="help.php">Help</a></span>
                </td>
                <td class="navCell">
                    <span><a href="resume.php?pageType=view">Resume</a></span>
                </td>
                <td colspan="2" class="navCell">
                    <span><a href="userAccount.php?pageType=view">Account</a></span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

  <div class="main"><?php  // TODO - Remove the following two lines of code from
  // our production code.
  ini_set('display_errors', '1');
  error_reporting(-1);  // level value of -1 says to display all PHP errors

  // Include the sanitizeString function (sanitize.php)
  //
  // include vs require
  //
  // include imports code from a file giving a warning message
  // if the file cannot be opened for any reason.
  //
  // require also imports code from a file, but gives a fatal
  // program-ending error if the file cannot be opened.
  //
  require 'sanitize.php';

  // Define callQuery() helper function to run a passed-in
  // query string.
  require 'callQuery.php';

  try {

    // Create an instance of the PDO class
    // $pdo = new PDO('connectionString', 'userName', 'password');
    $pdo = new PDO('mysql:host=sql111.infinityfree.com:3306;dbname=if0_38758969_resumatedb', 'if0_38758969', 'CVTCit2025');
  
  } catch(PDOException $ex) {
  
    // Note: remove $ex->getMessage() from production code so we don't
    // reveal too much detailed information.  TODO for production mode
    $error = 'Unable to connect to the database server<br><br>' . $ex->getMessage();
  
    include 'error.html.php';
    throw $ex;
    //exit();
  
  }
  //
  // Include the code to connect to our DB and login to it
  //
  // require 'dbConnect.php';

  $query = "SELECT *
            FROM User;";
// had to quote $newBookTitle as it contains a string value

  $errorMsg = 'Error fetching User information';

  $thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');
  $user = callQuery($pdo, $query, $errorMsg);

//  while ($row = $user->fetch()) {
//    echo $row['userLastName'];
//  }
    

    ?>
<!--            HTML START-->
        <div class="missionDiv">
            <p id="missionStatement">At Resumate, our mission is to empower individuals to take control of their professional journey by
                providing a dynamic platform that connects talent with opportunity. We strive to simplify career growth
                through intelligent networking, streamlined resume building, and meaningful connections that drive
                success in the modern workforce.
            </p>
            <span>- Previously Employed Resumate HR Coordinator 2025</span>
        </div>
          <div class="btnDiv">
              <form action="#" method="post">
                  <button class="button-19" type="submit" >Begin Viewing Candidates</button>
              </form>
              <form action="login.php?pageType=signUp" method="post">
                  <button class="button-19" type="submit" >Begin Creating Resume</button>
              </form>
          </div>
<!--            HTML END  -->
    <?php
    ?>
<!--            HTML START-->
<!--            HTML END  -->
    <?php
    // TODO
//      check to see if user is signed in when clicking the "Get STarted!" btn.
//      If yes they either go to user account or resume browsing depending on type of account.
//      If the user isn't signed in, bring them to login page'.

//      Maybe can put the check on the user account/browse pages and if you are not signed in then you have to sign in.
//      This would be a security check and prevent people from accessing random pages using url.


    ?>
  </div>
  <div class="footerDiv">
    <p>Copyright 2025<span>&copy;</span>Resumate</p>
    <span><a href="#">totalyReal@resumate.com</a></span>
  </div>
</body>
</html>