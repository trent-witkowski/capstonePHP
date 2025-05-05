<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resumate</title>

</head>
<body>
    <div class="banner">
        <h1>Resumate</h1>
        <span><a href="login.php?pageType=login">Login/Sign up</a></span>
    </div>
    <div class="navDiv">
        <table>
            <tbody>
<!--            This is the nav, painted by using a table-->
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2">
                    <span><a href="index.php">Home</a></span>
                </td>
                <td colspan="2">
                    <span><a href="#">Help</a></span>
                </td>
<!--                TODO This may need to be hidden/changed for the business/viewing account type-->
                <td colspan="2">
                    <span><a href="#">Resume</a></span>
                </td>
                <td colspan="2">
                    <span><a href="userAccount.php?pageType=edit">Account</a></span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            </tbody>
        </table>
    </div>

  <h2>works</h2>
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

  $out = "Added random book. L";
  $thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');
  $user = callQuery($pdo, $query, $errorMsg);

      while ($row = $user->fetch()) {
        echo $row['userLastName'];
      }

    if (sanitizeString(INPUT_GET, 'out')) {

    ?>
<!--            HTML START-->
      <span> <?php echo sanitizeString(INPUT_GET, 'out'); ?></span>
<!--        HTML END-->
    <?php
    }
    else {
    ?>
<!--            HTML START-->
      <a href="<?= $thisPage ?>?out= <?= $out ?>">Add new book title!</a>
<!--        HTML END-->
    <?php }
    // TODO
//      check to see if user is signed in when clicking the "Get STarted!" btn.
//      If yes they either go to user account or resume browsing depending on type of account.
//      If the user isn't signed in, bring them to login page'.

//      Maybe can put the check on the user account/browse pages and if you are not signed in then you have to sign in.
//      This would be a security check and prevent people from accessing random pages using url.


    ?>

  </div>
</body>
</html>