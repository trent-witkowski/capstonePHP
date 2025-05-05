<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/user.css">
</head>
<body>
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
    $thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');

    if (sanitizeString(INPUT_GET, 'pageType') == 'view') {


        ?>
        <!--        HTML START-->
<!--        TODO Place HTML here that displays the users info to themselves
                 - There should also be a button next to the label that allows an additional thing
                   of that field to be added to the account.
                 - This should open a small text box/form the user fills in and submits
                 - Maybe change Address to area so actual adresses are not out there
                 - Add image to input if possible
-->
        <form>
            <div id="userInfoMain">
                <h2>Account Information</h2>
                <input type="submit" name="pageType" value="edit" >
                <div class="userInfo">
                    <p>Robo Cop</p>
                    <p><span>Age: </span>31</p>
                    <p>fakeemaillol</p>
                    <p><span>Phone:</span> 111-111-1111</p>
                    <p><span>Address:</span> 123 State St</p>
                    <p><span>Account Created On:</span> 5/5/2025</p>
                </div>
            </div>
        </form>
        <!--        HTML END  -->

        <?php
    }
    else if (sanitizeString(INPUT_GET, 'pageType') == 'edit') { // New
        ?>
        <!--        HTML START-->

        <form action=<?=$thisPage?> method="post">
            <div class="userInfoEdit">

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

                <input type="submit" value="Submit" name="signUp">
                <button>Cancel</button>
            </div>
        </form>
        <!--        HTML END  -->

        <?php
    }

    ?>

</div>
</body>
</html>