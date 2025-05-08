<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/resume.css">
    <link rel="stylesheet" href="css/header.css">
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
    $resumeUser = "Robo Cop";
    $thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');
    ?>
<!--    HTML START-->
<!--
    TODO Add HTML to display the resume and its details
         Add Nav Above the h1


-->
    <h1>Viewing <?= $resumeUser?>'s Resume</h1>
    <br>
<!--    HTML END  -->
    <?php

    if (sanitizeString(INPUT_GET, 'pageType') == 'view') {


        ?>
        <!--        HTML START-->
<!--            TODO Add HTML to show the resume and its details
                     - Additionally add buttons to allow user to edit or add
                     - Open small textbox/form to fill out or edit resume details

                 TODO Maybe remove the add feature and just amke it edit.
                      One record per person. Means not add or delete would be needed-->
        <!--        HTML END  -->

        <div id="resumeInfoMain" class="resumeInfo">
            <div class="fieldDiv">
                <h2>Education</h2>
                <button><img src="garbage/pencil.png" alt="Edit"></button>
            </div>
            <label for="institution" >Institution</label>
            <input type="text" name="institution" id="institution">
            <br>
        </div>
        <?php
    } else {
        ?>
        <!--        HTML START-->

        <form action=<?=$thisPage?> method="post">

            <h2>Welcome, place username here !</h2>
            <label for="signUpUsername" id="bookArea">Username: </label>
            <input type="text" name="signUpUsername" id="signUpUsername">
            <br><br>
            <!--        TODO Place a bunch of forms here  -->
            <br><br>
            <input type="submit" value="Submit" name="signUp">

        </form>
        <!--        HTML END  -->
    <?php
    }
    ?>
</div>
</body>
</html>