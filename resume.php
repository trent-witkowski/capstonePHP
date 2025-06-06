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
        'mysql:host=sql111.infinityfree.com:3306;dbname=if0_38758969_resumatedb',
        'if0_38758969',
        'CVTCit2025'
    );
} catch (PDOException $ex) {
    $error = 'Unable to connect to the database server<br><br>' . $ex->getMessage();
    include 'error.html.php';
    throw $ex;
}

$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
	header("Location: login.php?pageType=login");
	exit();
}

if (isset($_SESSION['userType']) && $_SESSION['userType'] == 1) {
    $_SESSION['resumeView'] = 'resume';
} else {
   if (!isset($_SESSION['resumeView']) || !isset($_SESSION['prevPage']) || $_SESSION['prevPage'] != 'userBrowse' || !isset($_GET['resume'])) {
		 $_SESSION['resumeView'] = 'browse';
   }
}

if (isset($_SESSION['resumeView']) && $_SESSION['resumeView'] == 'resume') {
    if (isset($_SESSION['userType']) && $_SESSION['userType'] == 1) {
        $query = "SELECT resumeId FROM Resume WHERE userId = '$userID'";
    } else {
        
        $query = "SELECT resumeId FROM Resume WHERE userId = '" . ltrim($_GET['resume'], 'user') . "'";
    }
	$resume = callQuery($pdo, $query, "Error retrieving user's resume information. ")->fetch();
	$resumeId = $resume['resumeId'];
 
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // === Handle Education Section ===
        if (isset($_POST['educationSubmit'])) {
            $eduIds = $_POST['educationId'] ?? [];
            $institutions = $_POST['institution'] ?? [];
            $degrees = $_POST['degree'] ?? [];
            $fields = $_POST['fieldOfStudy'] ?? [];
            $starts = $_POST['startDate'] ?? [];
            $ends = $_POST['endDate'] ?? [];

            $existingEduStmt = $pdo->prepare("SELECT educationId FROM Education WHERE resumeId = ?");
            $existingEduStmt->execute([$resumeId]);
            $existingEduIds = $existingEduStmt->fetchAll(PDO::FETCH_COLUMN);

            $postedEduIds = array_filter($eduIds);
            $toDelete = array_diff($existingEduIds, $postedEduIds);

            foreach ($toDelete as $id) {
                $del = $pdo->prepare("DELETE FROM Education WHERE educationId = ?");
                $del->execute([$id]);
            }

            for ($i = 0; $i < count($institutions); $i++) {
                $id = $eduIds[$i];
                if ($id) {
                    $update = $pdo->prepare("UPDATE Education SET institutionName=?, degree=?, fieldOfStudy=?, startDate=?, endDate=? WHERE educationId=?");
                    $update->execute([$institutions[$i], $degrees[$i], $fields[$i], $starts[$i], $ends[$i], $id]);
                } else {
                    $insert = $pdo->prepare("INSERT INTO Education (resumeId, institutionName, degree, fieldOfStudy, startDate, endDate) VALUES (?, ?, ?, ?, ?, ?)");
                    $insert->execute([$resumeId, $institutions[$i], $degrees[$i], $fields[$i], $starts[$i], $ends[$i]]);
                }
            }

            header("Location: resume.php?pageType=view&resumeId=$resumeId");
            exit();
        }

        // === Handle Work Section ===
        if (isset($_POST['workSubmit'])) {
            $workIds = $_POST['workHistoryId'] ?? [];
            $jobTitles = $_POST['institution'] ?? [];
            $companies = $_POST['degree'] ?? [];
            $descs = $_POST['fieldOfStudy'] ?? [];
            $starts = $_POST['startDate'] ?? [];
            $ends = $_POST['endDate'] ?? [];

            $existingStmt = $pdo->prepare("SELECT workId FROM Work WHERE resumeId = ?");
            $existingStmt->execute([$resumeId]);
            $existingIds = $existingStmt->fetchAll(PDO::FETCH_COLUMN);

            $postedIds = array_filter($workIds);
            $toDelete = array_diff($existingIds, $postedIds);
            foreach ($toDelete as $id) {
                $del = $pdo->prepare("DELETE FROM Work WHERE workId = ?");
                $del->execute([$id]);
            }

            for ($i = 0; $i < count($jobTitles); $i++) {
                $id = $workIds[$i];
                if ($id) {
                    $update = $pdo->prepare("UPDATE Work SET jobTitle=?, companyName=?, jobDescription=?, startDate=?, endDate=? WHERE workId=?");
                    $update->execute([$jobTitles[$i], $companies[$i], $descs[$i], $starts[$i], $ends[$i], $id]);
                } else {
                    $insert = $pdo->prepare("INSERT INTO Work (resumeId, jobTitle, companyName, jobDescription, startDate, endDate) VALUES (?, ?, ?, ?, ?, ?)");
                    $insert->execute([$resumeId, $jobTitles[$i], $companies[$i], $descs[$i], $starts[$i], $ends[$i]]);
                }
            }

            header("Location: resume.php?pageType=view&resumeId=$resumeId");
            exit();
        }

        if (isset($_POST['hobbiesSubmit'])) {
            echo "hobby sumbitted";
        $hobbieIds = $_POST['hobbiesId'] ?? [];
        $descriptions = $_POST['description'] ?? [];

        $existingStmt = $pdo->prepare("SELECT hobbieId FROM Hobbies WHERE resumeId = ?");
        $existingStmt->execute([$resumeId]);
        $existingIds = $existingStmt->fetchAll(PDO::FETCH_COLUMN);

        $postedIds = array_filter($hobbieIds);
        $toDelete = array_diff($existingIds, $postedIds);
        foreach ($toDelete as $id) {
            $del = $pdo->prepare("DELETE FROM Hobbies WHERE hobbieId = ?");
            $del->execute([$id]);
        }

        for ($i = 0; $i < count($descriptions); $i++) {
            $id = $hobbieIds[$i];
            if ($id) {
                $update = $pdo->prepare("UPDATE Hobbies SET description = ? WHERE hobbieId = ?");
                $update->execute([$descriptions[$i], $id]);
            } else {
                $insert = $pdo->prepare("INSERT INTO Hobbies (resumeId, description) VALUES (?, ?)");
                $insert->execute([$resumeId, $descriptions[$i]]);
            }
        }

        header("Location: resume.php?pageType=view&resumeId=$resumeId");
        exit();
        }

        if (isset($_POST['projectsSubmit'])) {
        $projectIds = $_POST['projectsId'] ?? [];
        $descriptions = $_POST['description'] ?? [];

        $existingStmt = $pdo->prepare("SELECT projectId FROM Projects WHERE resumeId = ?");
        $existingStmt->execute([$resumeId]);
        $existingIds = $existingStmt->fetchAll(PDO::FETCH_COLUMN);

        $postedIds = array_filter($projectIds);
        $toDelete = array_diff($existingIds, $postedIds);
        foreach ($toDelete as $id) {
            $del = $pdo->prepare("DELETE FROM Projects WHERE projectId = ?");
            $del->execute([$id]);
        }

        for ($i = 0; $i < count($descriptions); $i++) {
            $id = $projectIds[$i];
            if ($id) {
                $update = $pdo->prepare("UPDATE Projects SET description = ? WHERE projectId = ?");
                $update->execute([$descriptions[$i], $id]);
            } else {
                $insert = $pdo->prepare("INSERT INTO Projects (resumeId, description) VALUES (?, ?)");
                $insert->execute([$resumeId, $descriptions[$i]]);
            }
        }

        header("Location: resume.php?pageType=view&resumeId=$resumeId");
        exit();
        }

        if (isset($_POST['skillSubmit'])) {
        $skillIds = $_POST['skillId'] ?? [];
        $skills = $_POST['skill'] ?? [];
        $proficiencies = $_POST['proficiency'] ?? [];
        $starts = $_POST['startDate'] ?? [];

        $existingStmt = $pdo->prepare("SELECT skillId FROM skill WHERE resumeId = ?");
        $existingStmt->execute([$resumeId]);
        $existingIds = $existingStmt->fetchAll(PDO::FETCH_COLUMN);

        $postedIds = array_filter($skillIds);
        $toDelete = array_diff($existingIds, $postedIds);
        foreach ($toDelete as $id) {
            $del = $pdo->prepare("DELETE FROM skill WHERE skillId = ?");
            $del->execute([$id]);
        }
        
        for ($i = 0; $i < count($skills); $i++) {
            $id = $skillIds[$i];
            if ($id) {
                $update = $pdo->prepare("UPDATE skill SET skill = ?, proficiency = ?, startDate = ? WHERE skillId = ?");
                $update->execute([$skills[$i], $proficiencies[$i], $starts[$i], $id]);
            } else {
                $insert = $pdo->prepare("INSERT INTO skill (resumeId, skill, proficiency, startDate) VALUES (?, ?, ?, ?)");
                $insert->execute([$resumeId, $skills[$i], $proficiencies[$i], $starts[$i]]);
            }
        }

        header("Location: resume.php?pageType=view&resumeId=$resumeId");
        exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume</title>
    <link rel="stylesheet" href="css/resume.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body>
    <div class="banner">
        <h1>Resumate</h1>
        <span>
            <?php if (isset($_SESSION['userID'])): ?>
                <a href="index.php?logout=true">Log Out</a>
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
                    <td class="navCell">
                        <span><a href="index.php">Home</a></span>
                    </td>
                    <td class="navCell">
                        <span><a href="help.php">Help</a></span>
                    </td>
                    <td class="navCell">
                        <span><a href="resume.php">Resume</a></span>
                    </td>
                    <td colspan="2" class="navCell">
                        <span><a href="userAccount.php">Account</a></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <?php
    if (isset($_SESSION['resumeView']) && $_SESSION['resumeView'] == 'resume') {
        
        if ($_SESSION['userType'] == 1) {
            $query = "SELECT userFirstName, userLastName FROM User WHERE userid = '$userID'";
        } else {
            $query = "SELECT userFirstName, userLastName FROM User WHERE userid = '" . ltrim($_GET['resume'], 'user') . "'";
        }
        $userStmt = callQuery($pdo, $query, "Unable to retrieve user's personal information.");
        $user = $userStmt->fetch();
        $fullName = $user ? $user['userFirstName'] . ' ' . $user['userLastName'] : "Unknown";
			
		$query = "SELECT * FROM Education WHERE resumeId =  '$resumeId'";
		$eduStmt = callQuery($pdo, $query, "Error fetching user's education information");
		
		
		$query = "SELECT * FROM Hobbies WHERE resumeId =  '$resumeId'";
		$hobbyStmt = callQuery($pdo, $query, "Error fetching user's hobby information");
		
		
		$query = "SELECT * FROM Projects WHERE resumeId =  '$resumeId'";
		$projectStmt = callQuery($pdo, $query, "Error fetching user's project information");
		
		
		$query = "SELECT * FROM skill WHERE resumeId =  '$resumeId'";
		$skillStmt = callQuery($pdo, $query, "Error fetching user's skill information");
		
		$query = "SELECT * FROM Work WHERE resumeId = '$resumeId'";
        $workStmt = callQuery($pdo, $query, "Error fetching user's work history");
    ?>

    <h1>Viewing <?= htmlspecialchars($fullName) ?>'s Resume</h1>
        <input type="hidden" name="resumeId" value="<?= htmlspecialchars($resumeId) ?>">
        
        <div id="resumeInfoMain" class="resumeInfo">
            <form method="post">
                <div id="educationSection" class="resumeInfo">
                    <div class="fieldDiv">
                        <h3>Education</h3>
                        <button type="button" id="addEducationBtn"><img src="garbage/plus.png" alt="Add"></button>
                        <button type="button" class="editEducationBtn editBtn"><img src="garbage/pencil.png" alt="Edit"></button><br>
                    </div>
					<?php
					while($row = $eduStmt->fetch()) {
					?><div class="educationBlock blockDiv">
                      <input type="hidden" name="educationId[]" placeholder="<?= htmlspecialchars($row['educationId']) ?>">
                      <label>Institution</label><br>
                      <input type="text" name="institution[]" placeholder="<?= htmlspecialchars($row['institutionName']) ?>" readonly><br>
                      <label>Degree</label><br>
                      <input type="text" name="degree[]" placeholder="<?= htmlspecialchars($row['degree']) ?>" readonly><br>
                      <label>Field of Study</label><br>
                      <input type="text" name="fieldOfStudy[]" placeholder="<?= htmlspecialchars($row['fieldOfStudy']) ?>" readonly><br>
                      <label>Start Date</label><br>
                      <input type="date" name="startDate[]" placeholder="<?= $row['startDate'] ?>" value="<?= $row['startDate'] ?>" disabled><br>
                      <label>End Date</label><br>
                      <input type="date" name="endDate[]" placeholder="<?= $row['endDate'] ?>" value="<?= $row['endDate'] ?>" disabled><br>
                      <br>
                      </div><br><br>
									<?php
                                    }
                                    ?>

                </div>
                <input type="hidden" value="educationSubmit" name="educationSubmit">
                <input id="educationSubmit" type="submit" placeholder="Submit" value="Submit" name="submit" style="display: none;">
            </form>

            <form method="post">
                <div id="hobbiesSection" class="resumeInfo">
                    <div class="fieldDiv">
                        <h3>Hobbies</h3>
                        <button type="button" id="addHobbiesBtn"><img src="garbage/plus.png" alt="Add"></button>
                        <button type="button" class="editHobbiesBtn editBtn"><img src="garbage/pencil.png" alt="Edit"></button><br>
                    </div>
									
									<?php
									while($row = $hobbyStmt->fetch()) {
										?><div class="hobbiesBlock blockDiv">
                      <input type="hidden" name="hobbieId[]" placeholder="<?= htmlspecialchars($row['hobbieId']) ?>">
                      <input type="text" name="description[]" placeholder="<?= htmlspecialchars($row['description']) ?>" readonly><br>
                      <br>
                      </div><br><br>
									<?php
                                    } ?>

                </div>
                <input type="hidden" value="hobbiesSubmit" name="hobbiesSubmit">
                <input id="hobbiesSubmit" type="submit" placeholder="Submit" value="Submit" name="submit" style="display: none;">
            </form>


            <form method="post">
                <div id="projectsSection" class="resumeInfo">
                    <div class="fieldDiv">
                        <h3>Projects</h3>
                        <button type="button" id="addProjectsBtn"><img src="garbage/plus.png" alt="Add"></button>
                        <button type="button" class="editProjectsBtn editBtn""><img src="garbage/pencil.png" alt="Edit"></button><br>
                    </div>
									
									<?php
									while($row = $projectStmt->fetch()) {
										?><div class="projectsBlock blockDiv">
                      <input type="hidden" name="projectId[]" placeholder="<?= htmlspecialchars($row['projectId']) ?>">
                      <input type="text" name="description[]" placeholder="<?= htmlspecialchars($row['description']) ?>" readonly><br>
                      <br>
                      </div><br><br>
									<?php } 
                                ?>

                </div>
                <input type="hidden" value="projectsSubmit" name="projectsSubmit">
                <input id="projectsSubmit" type="submit" placeholder="Submit" value="Submit" name="submit" style="display: none;">
            </form>


            <form method="post">
                <div id="skillSection" class="resumeInfo">
                    <div class="fieldDiv">
                        <h3>Skills</h3>
                        <button type="button" id="addSkillBtn"><img src="garbage/plus.png" alt="Add"></button>
                        <button type="button" class="editSkillsBtn editBtn""><img src="garbage/pencil.png" alt="Edit"></button><br>
                    </div>
									
									<?php
                                    $selectedProficiency = 'Beginner';
									while($row = $skillStmt->fetch()) {
										?><div class="skillBlock blockDiv">
                      <input type="hidden" name="skillId[]" placeholder="<?= htmlspecialchars($row['skillId']) ?>">
                      <label>Skill</label><br>
                      <input type="text" name="skill[]" placeholder="<?= htmlspecialchars($row['skill']) ?>" readonly><br>
                      <label>Proficiency Level</label><br>
                      <select id="proficiencySelect" name="proficiency[]" class="<?= htmlspecialchars($row['proficiency']) ?>" disabled>
                          <option value="" disabled hidden>Please Select...</option>
                          <option value="Beginner" selected>Beginner</option>
                          <option value="Intermediate">Intermediate</option>
                          <option value="Proficient">Advanced</option>
                          <option value="Expert">Expert</option>
                      </select><br>
                      <label>Start Date</label><br>
                      <input type="date" name="startDate[]" placeholder="<?= htmlspecialchars($row['startDate']) ?>" value="<?= $row['startDate'] ?>" disabled><br>
                      <br>
                      </div><br><br>
									<?php
                                    }
                                ?>

                </div>
                <input type="hidden" value="skillSubmit" name="skillSubmit">
                <input id="skillSubmit" type="submit" placeholder="Submit" value="Submit" name="submit" style="display: none;">
            </form>
            

            <form method="post">
                <div id="workSection" class="resumeInfo">
                    <div class="fieldDiv">
                        <h3>Work History</h3>
                        <button type="button" id="addWorkBtn"><img src="garbage/plus.png" alt="Add"></button>
                        <button type="button" class="editWorkBtn editBtn""><img src="garbage/pencil.png" alt="Edit"></button><br>
                    </div>
									
									<?php
									while($row = $workStmt->fetch()) {
										?><div class="workBlock blockDiv">
                      <input type="hidden" name="workId[]" placeholder="<?= htmlspecialchars($row['workId']) ?>">
                      <label>Job Title</label><br>
                      <input type="text" name="jobTitle[]" placeholder="<?= htmlspecialchars($row['jobTitle']) ?>" readonly><br>
                      <label>Company</label><br>
                      <input type="text" name="companyName[]" placeholder="<?= htmlspecialchars($row['companyName']) ?>" readonly><br>
                      <label>Job Description</label><br>
                      <input type="text" name="jobDescription[]" placeholder="<?= htmlspecialchars($row['jobDescription']) ?>" readonly><br>
                      <label>Start Date</label><br>
                      <input type="date" name="startDate[]" placeholder="<?= $row['startDate'] ?>" value="<?= $row['startDate'] ?>" disabled><br>
                      <label>End Date</label><br>
                      <input type="date" name="endDate[]" placeholder="<?= $row['endDate'] ?>" value="<?= $row['endDate'] ?>" disabled><br>
                      <br>
                      </div><br><br>
									<?php } ?>

                </div>
                <input type="hidden" value="workSubmit" name="workSubmit">
                <input id="workSubmit" type="submit" placeholder="Submit" value="Submit" name="submit" style="display: none;">
            </form>
            <div class="rightBtnDiv">
                <button type="button" class="backBtn" style="display: none;">Back</button>
            </div>
        </div>
<?php } else if (isset($_SESSION['resumeView']) && $_SESSION['resumeView'] == 'browse') {
			$query = "SELECT * FROM Resume GROUP BY resumeId";
			$resumeStmt = callQuery($pdo, $query, "Error fetching user's with resumes information");
            
      ?>
    <div class="browseDiv">
      <h2>Browsing Users</h2>
            <?php
            while($row = $resumeStmt->fetch()) {
                $query = "SELECT * FROM Education WHERE resumeId = '" . $row['resumeId'] . "'";
                $eduStmt = callQuery($pdo, $query, "Error fetching user's with resumes information");
                $eduCheck = $eduStmt->fetch();
                if ($eduCheck['institutionName'] == "" || $eduCheck['institutionName'] == "Sample University") {
                    continue;
                } else {
                    $query = "SELECT * FROM User WHERE userId = '" . $row['userId'] . "'";
                    $browseUser = callQuery($pdo, $query, "Error fetching user's information")->fetch();
                }
                
            ?><span><a id="user<?=$browseUser['userid']?>" class="browseUser" href="#" ><?php echo $browseUser['userFIrstName'] . " " . $browseUser['userLastName'] ?></a></span><br><?php
            }
            
            ?>
    </div>
	<?php
    }
    ?>
        

    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
</body>

</html>

<?php
if (isset($_SESSION['resumeView']) && $_SESSION['resumeView'] == 'resume') {
?>
    <script>
        let navLinks = document.querySelectorAll(".navTable a");
        let editBtns = document.querySelectorAll(".blockDiv > .editBtn:first-child");
        let proficiencySelect = document.querySelector("#proficiencySelect");
        let proficiencyOptions = proficiencySelect.querySelectorAll("option");
        
        navLinks.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                <?php
                if ($_SESSION['userType'] == 0) {
                    $_SESSION['prevPage'] = 'browse';
                } else {
                    $_SESSION['prevPage'] = 'resume';
                }
                ?>
                window.location.href = link.href;
            });
        });
        
        editBtns.forEach(e => {
            e.style.display = 'block';
        })
        
        proficiencyOptions.forEach(option => {
            console.log(option.value);
            console.log(proficiencySelect.className);
            if (option.value === proficiencySelect.className) {
                option.selected = true;
            } else {
                option.selected = false;
            }
        })
        
        
        
        
        
        
    <?php if (isset($_SESSION['userType']) && $_SESSION['userType'] == '1') { ?>
        let originalValues = [];

        function storeOriginalValues() {
            originalValues = [];
            document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
                originalValues.push({
                    element: input,
                    placeholder: input.placeholder
                });
            });
        }

        function restoreOriginalValues() {
            originalValues.forEach(item => {
                item.element.placeholder = item.placeholder;
            });
        }
        
        document.querySelector('.editEducationBtn').addEventListener('click', () => {
            storeOriginalValues();
            document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.value = input.placeholder;
                input.placeholder = "";
            });
            document.querySelector('.editEducationBtn').style.display = 'none';
            document.querySelector('#educationSubmit').style.display = "block";
        });

        document.querySelector('.editHobbiesBtn').addEventListener('click', () => {
            storeOriginalValues();
            document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
                input.removeAttribute('readonly');
                input.value = input.placeholder;
                input.placeholder = "";
            });
            document.querySelector('.editHobbiesBtn').style.display = 'none';
            document.querySelector('#hobbiesSubmit').style.display = "block";
        });

        document.querySelector('.editProjectsBtn').addEventListener('click', () => {
            storeOriginalValues();
            document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
                input.removeAttribute('readonly');
                input.value = input.placeholder;
                input.placeholder = "";
            });
            document.querySelector('.editProjectsBtn').style.display = 'none';
            document.querySelector('#projectsSubmit').style.display = "block";
        });

        document.querySelector('.editSkillsBtn').addEventListener('click', () => {
            storeOriginalValues();
            document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.value = input.placeholder;
                input.placeholder = "";
            });

            document.querySelector('.resumeInfo select').removeAttribute('disabled');
            document.querySelector('.editSkillsBtn').style.display = 'none';
            document.querySelector('#skillSubmit').style.display = "block";
        });

        document.querySelector('.editWorkBtn').addEventListener('click', () => {
            storeOriginalValues();
            document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.value = input.placeholder;
                input.placeholder = "";
            });
            document.querySelector('.editWorkBtn').style.display = 'none';
            document.querySelector('#workSubmit').style.display = "block";
        });
            
        // 'ADD MORE' BUTTONS
        document.querySelector('#addEducationBtn').addEventListener('click', () => {
            let container = document.querySelector('#educationSection');
            let first = container.querySelector('.educationBlock');
            let clone = first.cloneNode(true);

            clone.querySelectorAll('input').forEach(input => {
                input.placeholder = '';
                input.removeAttribute('readonly');
            });
            document.querySelector('#educationSubmit').style.display = "block";
            clone.querySelector('input[name="educationId[]"]').placeholder = ''; // clear ID
            
            container.appendChild(clone);
        });
        document.querySelector('#addHobbiesBtn').addEventListener('click', () => {
            let container = document.querySelector('#hobbiesSection');
            let first = container.querySelector('.hobbiesBlock');
            let clone = first.cloneNode(true);

            clone.querySelectorAll('input').forEach(input => {
                input.placeholder = '';
                input.removeAttribute('readonly');
            });
            document.querySelector('#hobbiesSubmit').style.display = "block";
            clone.querySelector('input[name="hobbieId[]"]').placeholder = ''; // clear ID
            container.appendChild(clone);
        });
        document.querySelector('#addProjectsBtn').addEventListener('click', () => {
            let container = document.querySelector('#projectsSection');
            let first = container.querySelector('.projectsBlock');
            let clone = first.cloneNode(true);

            clone.querySelectorAll('input').forEach(input => {
                input.placeholder = '';
                input.removeAttribute('readonly');
            });
            document.querySelector('#projectsSubmit').style.display = "block";
            clone.querySelector('input[name="projectId[]"]').placeholder = ''; // clear ID
            container.appendChild(clone);
        });
        document.querySelector('#addSkillBtn').addEventListener('click', () => {
            let container = document.querySelector('#skillSection');
            let first = container.querySelector('.skillBlock');
            let clone = first.cloneNode(true);

            clone.querySelectorAll('input').forEach(input => {
                input.placeholder = '';
                input.removeAttribute('readonly');
            });

            document.querySelector('.resumeInfo select').removeAttribute('disabled');
            document.querySelector('#skillSubmit').style.display = "block";
            clone.querySelector('input[name="skillId[]"]').placeholder = ''; // clear ID
            container.appendChild(clone);
        });

        document.querySelector('#addWorkBtn').addEventListener('click', () => {
            let container = document.querySelector('#workSection');
            let first = container.querySelector('.workBlock');
            let clone = first.cloneNode(true);

            clone.querySelectorAll('input, textarea').forEach(input => {
                input.placeholder = '';
                input.removeAttribute('readonly');
            });

            clone.querySelector('input[name="workId[]"]').placeholder = ''; // clear ID
            clone.querySelectorAll('.editBtn');
            container.appendChild(clone);
        });
        
        <?php
        } else {
        ?>
            document.querySelectorAll('#resumeInfoMain button').forEach(e => {
                e.style.display = 'none';
            });
            let backBtn = document.querySelector('.backBtn');
            backBtn.style.display = 'block';
            backBtn.addEventListener('click', e => {
                <?php
                $_SESSION['resumeView'] = 'browse';
                $_SESSION['prevPage'] = 'viewResume';
                ?>
                window.location.href = "resume.php";
            });
        <?php
        }
        ?>
    </script>
<?php
} else if (isset($_SESSION['resumeView']) && $_SESSION['resumeView'] == 'browse') {
?>

<script>
    // BROWSE VIEW Scripts
    $browseLinks = document.querySelectorAll('.browseUser');
    $browseLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            <?php
            $_SESSION['resumeView'] = 'resume';
            $_SESSION['prevPage'] = 'userBrowse';
            ?>
            window.location.href = 'resume.php?resume=' + link.id;
        });
    });
</script>
<?php
}
?>