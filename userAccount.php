<?php
//session_start();
//ini_set('display_errors', '1');
//error_reporting(-1);
//
//require 'sanitize.php';
//require 'callQuery.php';
//
//try {
//    $pdo = new PDO(
//        'mysql:host=sql111.infinityfree.com;dbname=if0_38758969_resumatedb;port=3306',
//        'if0_38758969',
//        'CVTCit2025'
//    );
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//} catch (PDOException $ex) {
//    $error = 'Unable to connect to the database server<br><br>' . $ex->getMessage();
//    include 'error.html.php';
//    throw $ex;
//}
//
//$thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');
//$userID = $_SESSION['user_id'] ?? null;
//
//// if (!$userId) {
////     header('Location: login.php');
////     exit();
//// }
//
//$stmt = $pdo->prepare("SELECT * FROM User WHERE userId = ?");
//$stmt->execute([$userID]);
//$user = $stmt->fetch(PDO::FETCH_ASSOC);
//
//if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
//    $updateStmt = $pdo->prepare("UPDATE users SET userFirstName=?, userLastName=?, age=?, email=?, phoneNumber=?, street=?, country=?, state=?, zip=? WHERE userId=?");
//    $updateStmt->execute([
//        $_POST['firstName'], $_POST['lastName'], $_POST['age'], $_POST['email'],
//        $_POST['phoneNumber'], $_POST['street'], $_POST['country'], $_POST['state'],
//        $_POST['zip'], $userId
//    ]);
//    header("Location: userAccount.php?pageType=view");
//    exit();
//}
//?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/user.css">
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
                        } catch (PDOException $ex) {

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
                        $thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');

                        if (sanitizeString(INPUT_GET, 'pageType') == 'new') {


                        ?>
            <!--        HTML START-->
            <!--        TODO Place HTML here that displays the users info to themselves
                 - There should also be a button next to the label that allows an additional thing
                   of that field to be added to the account.
                 - This should open a small text box/form the user fills in and submits
                 - Maybe change Address to area so actual adresses are not out there
                 - Add image to input if possible
-->


            <form action="<?php echo $thisPage ?>?pageType=view" method="post">
                <div class="userInfo">

                    <h2>Edit Account Information</h2>
                    <label for="editFirstName" id="editFirstNameLbl">First Name</label>
                    <input type="text" name="firstName" id="editFirstName">
                    <br>
                    <label for="editLastName" id="editLastNameLbl">Last Name</label>
                    <input type="text" name="lastName" id="editLastName">
                    <br>
                    <label for="editAge" id="editAgeLbl">Age</label>
                    <input type="text" name="age" id="editAge">
                    <br>
                    <label for="editEmail" id="editEmailLbl">Email</label>
                    <input type="text" name="email" id="editEmail">
                    <br>
                    <label for="editPhoneNumber" id="editPhoneNumberLbl">Phone Number</label>
                    <input type="text" name="phoneNumber" id="editPhoneNumber">
                    <br>
                    <label for="editStreet" id="editStreetLbl">Street</label>
                    <input type="text" name="street" id="editStreet">
                    <br>
                    <label for="editCountry" id="editCountryLbl">Country</label>
                    <input type="text" name="country" id="editCountry">
                    <br>
                    <label for="editState" id="editStateLbl">State</label>
                    <input type="text" name="state" id="editState">
                    <br>
                    <label for="editZip" id="editZipLbl">Zip Code</label>
                    <input type="text" name="zip" id="editZip">
                    <br><br>

                    <input type="submit" value="Submit" name="submit">
                    <input type="submit" value="Cancel" name="cancel">

                </div>
            </form>
            <br>
            <span><a href="resume.php?pageType=view">Don't have a Resume yet?</a></span>
            <!--        HTML END  -->

        <?php
                        } else if (sanitizeString(INPUT_GET, 'pageType') == 'view') { // New
        ?>
            <!--        HTML START-->

            <form action="<?php echo $thisPage ?>?pageType=view" method="post">
                <div class="userInfo">
                    <div class="infoTitle">
                        <h2>Account Information</h2>
                        <button><img src="garbage/pencil.png" alt="Edit"></button>
                    </div>
                    <label for="editFirstName" id="editFirstNameLbl">First Name</label>
                    <input type="text" name="firstName" id="editFirstName">
                    <br>
                    <label for="editLastName" id="editLastNameLbl">Last Name</label>
                    <input type="text" name="lastName" id="editLastName">
                    <br>
                    <label for="editAge" id="editAgeLbl">Age</label>
                    <input type="text" name="age" id="editAge">
                    <br>
                    <label for="editEmail" id="editEmailLbl">Email</label>
                    <input type="text" name="email" id="editEmail">
                    <br>
                    <label for="editPhoneNumber" id="editPhoneNumberLbl">Phone Number</label>
                    <input type="text" name="phoneNumber" id="editPhoneNumber">
                    <br>
                    <label for="editStreet" id="editStreetLbl">Street</label>
                    <input type="text" name="street" id="editStreet">
                    <br>
                    <label for="editCountry" id="editCountryLbl">Country</label>
                    <input type="text" name="country" id="editCountry">
                    <br>
                    <label for="editState" id="editStateLbl">State</label>
                    <input type="text" name="state" id="editState">
                    <br>
                    <label for="editZip" id="editZipLbl">Zip Code</label>
                    <input type="text" name="zip" id="editZip">
                    <br><br>
                    <div class="btnDiv">
                        <input type="submit" value="Submit" name="submit">
                        <input type="submit" value="Cancel" name="cancel">
                    </div>

                </div>
            </form>
            <span><a href="resume.php?pageType=view">Don't have a Resume yet?</a></span>
            <!--        HTML END  -->

        <?php
                        }

        ?>

    </div>
    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
</body>

</html>