<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Lister - PDO (PHP Data Object)</title>

  <link rel="stylesheet" href="css/bookLister.css">
</head>
<body>
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

  $errorMsg = 'Error fetching User infomrmation';

  $user = callQuery($pdo, $query, $errorMsg)->fetchColumn();

  echo $user;
    ?>    


  </div>
</body>
</html>