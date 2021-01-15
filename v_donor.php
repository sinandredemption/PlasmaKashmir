<?php
require("./includes/header.php");
require("_con.php");
$con = DB::getConnection();
?>

<div class="site-content-contain">
		<div id="content" class="site-content">

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			
<article id="post-8" class="post-8 page type-page status-publish hentry">
<?php
if (isset($_GET['v']) && isset($_GET['p'])) {
?>
<header class="entry-header">
	<h1 class="entry-title">View donors</h1>			</header><!-- .entry-header -->
<div class="entry-content">
<?php
	// Check if username and password correct
	$query = $con->prepare("SELECT * FROM `volunteers` WHERE `name` = :v AND `pass` = :p");
    $query->bindValue(":v", $_GET['v']);
	$query->bindValue(":p", $_GET['p']);
	$query->execute();

	if ($query->rowCount() > 0) {
        $v = $query->fetch(PDO::FETCH_ASSOC);
        if (isset($_GET['d'])) {
            if (isset($_POST['submit'])) {
                if ((strval($_POST['active']) == "1" || strval($_POST['active']) == "0")
                && (strval($_POST['contacted']) == "1" || strval($_POST['contacted']) == "0")) {
                    $query = $con->prepare("UPDATE `donors` SET `active` = :active , `contacted` = :contacted WHERE `WhatsApp` = :w");
                    $query->bindValue(":active", $_POST['active']);
                    $query->bindValue(":contacted", $_POST['contacted']);
                    $query->bindValue(":w", $_GET['d']);

                    if ($query->execute())
                        echo "<p><i>Donor status for updated successfully.</i></p>";
                    else
                        echo "<p><strong>Unsuccessful: Please try again later.</p>";
                } else
                    echo "<p>Incorrect options choosen. Please verify and try again.</p>";
            } else {
                $query = $con->prepare("SELECT * FROM `donors` WHERE `WhatsApp` = :w AND `assigned_to` = :v");
                $query->bindValue(":w", $_GET['d']);
                $query->bindValue(":v", $_GET['v']);
                $query->execute();
                
                if ($query->rowCount() > 0) {
                    $d = $query->fetch(PDO::FETCH_ASSOC);
                    echo "<h4>Changing status for '" . $d['Name'] . "', " . $d['Bloodgroup'] . "</h4>";
                    echo "<p>Currently active: " . (strval($d['active']) == "1" ? "Yes" : "No") . "<br>";
                    echo "Currently contacted: " . (strval($d['contacted']) == "1" ? "Yes" : "No") . "</p>";
                
            
?>
<form action="" method="POST">
    <label for="active">Active: </label>
    <select name="active" id="active">
        <option value="select">-- Select --</option>
        <option value="1">Yes</option>
        <option value="0">No</option>
    </select><br/><br/>
    <label for="contacted">Contacted: </label>
    <select name="contacted" id="contacted">
        <option value="select">-- Select --</option>
        <option value="1">Yes</option>
        <option value="0">No</option>
    </select><br/><br/>
    <input type="submit" value="Change" name="submit" />
</form>
<?php
                }   else {
                    echo "<p><strong>The link appears to be broken because the donor either does not exist or the donor isn't assigned to you.</strong></p>";
                }
            }
        }
        $query = $con->prepare("SELECT * FROM donors WHERE `assigned_to` = :v ORDER BY `RegistrationDate` ASC");
        $query->bindValue(":v", $v['name']);
        $query->execute();

        if ($query->rowCount() > 0) {
            echo "<h4>Assigned donors</h4>";
            echo "<p>Please call / contact the following donors and verify whether all the information they have provided is correct.</p>";
            echo "<h5>Guidelines for call</h5>";
            echo "<ul><li>Verify donor's recovery date.</li>";
            echo "<li>Tell the donor that he/she can contact you when he/she wants to temporarily or permanently remove his/her name from the list.</li>";
            echo "<li>Thank the donor for helping save lives.</li></ul>";
            echo "<p><strong>Mark the donor ACTIVE even if he has recently recovered from COVID-19</strong></p>";

            $query = $con->prepare("SELECT * FROM donors WHERE `assigned_to` = :v ORDER BY `RegistrationDate` ASC");
            $query->bindValue(":v", $v['name']);
            $query->execute();

            echo "<table>";
            echo "<tr><th>Name</th><th>Date of recovery</th><th>Age</th><th>District</th><th>Call</th><th>Contacted</th><th>Active</th><th>Change status</th></tr>";
            while ($d = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";

                echo "<td>" . $d['Name'] . ", " . $d['Bloodgroup'] . "</td>";
                echo "<td>" . $d['DateOfRecovery'] . "</td>";
                echo "<td>" . $d['Age'] . "</td>";
                echo "<td>" . $d['District'] . "</td>";
                echo "<td>" . $d['WhatsApp'] . "</td>";

                echo "<td>" . (strval($d['contacted']) == "1" ? "Yes" : "No") . "</td>";
                echo "<td>" . (strval($d['active']) == "1" ? "Yes" : "No") . "</td>";
                echo "<td><a href='v_donor.php?v=" . $v['name'] . "&p=" . $v['pass'] . "&d=" . $d['WhatsApp'] . "'>Click</a></td>";

                echo "</tr>";
            }
            echo "</table>";
        } else echo "<p>No donors are assigned to you so far</p>";
    } else {
		echo "<h1>Error: Authorization failed</h1>";
		echo "<p>Incorrect username or password.</p>";
	}
?>
</div>
<?php
}
else {
?>
	<header class="entry-header">
		<h1 class="entry-title">Volunteer Donor management</h1>			</header><!-- .entry-header -->
	<div class="entry-content">
<form method="GET">
<label for="name">Username:</label>
<input id="name" name="v" type="text"><br>
<label for="pass">Password:</label>
<input id="pass" name="p" type="password"><br>
<input type="submit" />
</form>
	</div><!-- .entry-content -->
<?php
}
?>
</article><!-- #post-8 -->

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->
</div> <!-- site-content -->

<?php
require("./includes/footer.php");
?>
            