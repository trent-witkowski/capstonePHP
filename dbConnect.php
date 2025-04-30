<?php
/*
1. Connect to the DB Server
2. Select a DB
3. Provide account information for logging in to the DB Server
4. Check for exceptions
*/
try {

  // Create an instance of the PDO class
  // $pdo = new PDO('connectionString', 'userName', 'password');
  $pdo = new PDO('mysql:host=sql111.infinityfree.com:3306;dbname=if0_38758969_XXX', 'if0_38758969', 'CVTCit2025');

} catch(PDOException $ex) {

  // Note: remove $ex->getMessage() from production code so we don't
  // reveal too much detailed information.  TODO for production mode
  $error = 'Unable to connect to the database server<br><br>' . $ex->getMessage();

  if ($closeSelect) {
    echo "";
    $closeSelect = false;
  }

  include 'error.html.php';
  throw $ex;
  //exit();

}