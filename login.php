<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/login.css">
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

    $errorMsg = 'Error fetching User information';

    $out = "Added random book. L";
    $thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');
    
    if (sanitizeString(INPUT_GET, 'pageType') == 'login') {


        ?>
<!--        HTML START-->
        <div class="loginDiv">
            <form action=<?=$thisPage?> method="post">
                <h2>Login</h2>
                <label for="loginUsername" class="inputLbl">Username: </label>
                <input type="text" name="loginUsername" id="loginUsername">
                <br><br>
                <label for="loginPassword" class="inputLbl">Password: </label>
                <input type="text" name="loginPassword" id="loginPassword">
                <br><br>

                <input type="submit" value="Login" name="login">
            </form>
            <br>
            <span><a href="login.php?pageType=signUp">Don't have an Account?</a></span>
        </div>

<!--        HTML END  -->

    <?php
    }
    else { // Sign Up
        ?>
<!--        HTML START-->
        <div class="loginDiv">
            <form action=<?=$thisPage?> method="post">

                <h2>Sign Up</h2>
                <label for="signUpUsername" class="inputLbl">Username: </label>
                <input type="text" name="signUpUsername" id="signUpUsername">
                <br><br>

                <label for="signUpPassword" class="inputLbl">Password: </label>
                
                <input type="text" name="signUpPassword" id="signUpPassword">

                <br><br>
                <div>
                    <input type="submit" value="Sign Up" name="signUp">
                </div>
            </form>
            <br>
            <span><a href="login.php?pageType=login">Already have an Account?</a></span>
        </div>
<!--        HTML END  -->

    <?php
    }
//    if (sanitizeString(INPUT_POST, 'loginUsername')) {
//        echo "<p>" . sanitizeString(INPUT_POST, 'loginUsername') . "</p>";
//        echo "<p>" . sanitizeString(INPUT_POST, 'loginPassword') . "</p>";
//    }

    ?>

</div>
</body>
</html>