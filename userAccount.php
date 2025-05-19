<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
	session_unset();
	session_destroy();
	header("Location: index.php");
	exit();
}

ini_set('display_errors', '1');
error_reporting(-1);

require 'sanitize.php';
require 'callQuery.php';

try {
    $pdo = new PDO(
        'mysql:host=sql111.infinityfree.com;dbname=if0_38758969_resumatedb;port=3306',
        'if0_38758969',
        'CVTCit2025'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    $error = 'Unable to connect to the database server<br><br>' . $ex->getMessage();
    include 'error.html.php';
    throw $ex;
}

$thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');
$userID = $_SESSION['userID'] ?? null;

if (!$userID) {
    header('Location: login.php?pageType=login');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM User WHERE userid = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    $updateStmt = $pdo->prepare("UPDATE User SET userFirstName=?, userLastName=?, age=?, email=?, phoneNumber=?, street=?, country=?, state=?, zip=? WHERE userid=?");
    $updateStmt->execute([
        $_POST['firstName'], $_POST['lastName'], $_POST['age'], $_POST['email'],
        $_POST['phoneNumber'], $_POST['street'], $_POST['country'], $_POST['state'],
        $_POST['zip'], $userID
    ]);
    header("Location: userAccount.php?pageType=view");
    exit();
}

?>


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
        <span>
            <?php if (isset($_SESSION['userID'])): ?>
                <a href="login.php?logout=true">Log Out</a>
			<?php else: ?>
                <a href="login.php?pageType=login">Login/Sign up</a>
			<?php endif; ?>
        </span>
    </div>
    <div class="navDiv">
        <table class="navTable">
            <tbody>
                <tr>
                    <td></td>
                    <td class="navCell"><span><a href="index.php">Home</a></span></td>
                    <td class="navCell"><span><a href="help.php">Help</a></span></td>
                    <td class="navCell"><span><a href="resume.php">Resume</a></span></td>
                    <td colspan="2" class="navCell"><span><a href="userAccount.php">Account</a></span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="main">
        <form action="<?= $thisPage ?>?pageType=view" method="post">
            <div class="userInfo">
                <div class="infoTitle">
                    <h2>Account Information</h2>
                    <button type="button" class="editBtn"><img src="garbage/pencil.png" alt="Edit"></button>
                </div>
                <label for="editFirstName" id="editFirstNameLbl">First Name</label>
                <input type="text" name="firstName" id="editFirstName" value="<?= htmlspecialchars($user['userFIrstName']) ?>" readonly>
                <br>
                <label for="editLastName" id="editLastNameLbl" class="userOnly">Last Name</label>
                <input type="text" name="lastName" id="editLastName" class="userOnly" value="<?= htmlspecialchars($user['userLastName']) ?>" readonly>
                <br class="userOnly">
                <label for="editAge" id="editAgeLbl" class="userOnly" >Age</label>
                <input type="text" name="age" id="editAge" class="userOnly" value="<?= htmlspecialchars($user['age']) ?>" readonly>
                <br class="userOnly">
                <label for="editEmail" id="editEmailLbl">Email</label>
                <input type="text" name="email" id="editEmail" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                <br>
                <label for="editPhoneNumber" id="editPhoneNumberLbl">Phone Number</label>
                <input type="text" name="phoneNumber" id="editPhoneNumber" value="<?= htmlspecialchars($user['phoneNumber']) ?>" readonly>
                <br>
                <label for="editStreet" id="editStreetLbl">Street</label>
                <input type="text" name="street" id="editStreet" value="<?= htmlspecialchars($user['street']) ?>" readonly>
                <br>
                <label for="editCountry" id="editCountryLbl">Country</label>
                <input type="text" name="country" id="editCountry" value="<?= htmlspecialchars($user['country']) ?>" readonly>
                <br>
                <label for="editState" id="editStateLbl">State</label>
                <input type="text" name="state" id="editState" value="<?= htmlspecialchars($user['state']) ?>" readonly>
                <br>
                <label for="editZip" id="editZipLbl">Zip Code</label>
                <input type="text" name="zip" id="editZip" value="<?= htmlspecialchars($user['zip']) ?>" readonly>
                <br><br>
                <div class="btnDiv" style="display: none;">
                    <input type="submit" value="Submit" name="submit">
                    <input type="button" value="Cancel" class="cancelBtn">
                </div>
            </div>
        </form>
        <span>
        <?php
            if (isset($_SESSION['userType']) != 0) {
                $stmt = $pdo->prepare("SELECT * FROM Resume WHERE userid = ?");
                $stmt->execute([$userID]);
                $resume = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$resume) {
                    ?><a href="resume.php?pageType=view">Don't have a Resume yet?</a><?php
                }
            }
        ?>
        </span>
        <!--        HTML END  -->

        <script>
            let editBtn = document.querySelector(".editBtn");
            let cancelBtn = document.querySelector(".cancelBtn");
            let btnDiv = document.querySelector(".btnDiv");
            let inputs = document.querySelectorAll("input[type='text']");
            let navLinks = document.querySelectorAll(".navTable a");
            let originalValues = {};

            editBtn.addEventListener("click", () => {
                inputs.forEach(input => {
                    originalValues[input.name] = input.value;
                    input.removeAttribute("readonly");
                });
                btnDiv.style.display = "block";
                editBtn.style.display = "none";
            });

            cancelBtn.addEventListener("click", () => {
                inputs.forEach(input => {
                    input.value = originalValues[input.name];
                    input.setAttribute("readonly", true);
                });
                btnDiv.style.display = "none";
                editBtn.style.display = "inline-block";
            });
            
            navLinks.forEach(link => {
               link.addEventListener('click', e => {
                   e.preventDefault();
                    <?php
                    $_SESSION['prevPage'] = 'userAccount';
                    ?>
                   window.location.href = link.href;
               });
            });
            
            
            
            <?php
            if (isset($_SESSION['prevPage']) && $_SESSION['prevPage'] == "signUp") {
            ?>
                editBtn.click();
                document.querySelector('#editAge').value = null;
                cancelBtn.style.display = "none";
            <?php
            }
            if (isset($_SESSION['userType']) && $_SESSION['userType'] == 0) {
                ?>
                document.querySelectorAll('.userOnly').forEach(e => {
                    e.style.display = "none";
                });
                document.querySelector('#editFirstNameLbl').innerHTML = "Company Name";
                document.querySelector('#editAge').value = null;
                
                <?php
            }
            ?>
        </script>
    </div>
    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
</body>
</html>