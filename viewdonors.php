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
if (isset($_GET['v']) && isset($_GET['p']) && isset($_GET['patient'])) {
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
		// Check if patient exists
		$query = $con->prepare("SELECT * FROM `patients` WHERE `whatsapp` = :w AND `assigned_to` = :v");
		$query->bindValue(":w", $_GET['patient']);
		$query->bindValue(":v", $_GET['v']);
		$query->execute();

		if ($query->rowCount() > 0) {
			$p = $query->fetch(PDO::FETCH_ASSOC);
			// List donors
			echo "<h1>Matched donors for '" . $p['name'] . "' (" . $p['bloodgroup'] . "): " . $p['whatsapp'] . "</h1>";
			echo "<p>Please don't send details of more than ONE donor at once to the patient.</p>";

			$arr_plasma_match = array("A+" => "'A+', 'A-', 'AB+', 'AB-'", "A-" => "'A+', 'A-', 'AB+', 'AB-'", 
										  "B+" => "'B+', 'B-', 'AB+', 'AB-'", "B-" => "'B+', 'B-', 'AB+', 'AB-'",
										  "O+" => "'A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'", "O-" => "'A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'",
										  "AB+" => "'AB+', 'AB-'", "AB-" => "'AB+', 'AB-'");
			
			//$query = $con->prepare("SELECT * FROM ( SELECT * FROM `donors` WHERE `bloodgroup` = :b AND `active`=1 ORDER BY RAND() )\
			//UNION SELECT * FROM ( SELECT * FROM `donors` WHERE `bloodgroup` IN ( " . $arr_plasma_match[$p['bloodgroup']] . " ) AND `active`=1 ORDER BY RAND() ) LIMIT 3");
			//$query = $con->prepare("SELECT * FROM `donors` WHERE `bloodgroup` = :b AND `active`=1 ORDER BY RAND()");
			//$query->bindValue(":b", $p['bloodgroup']);
			//$query->execute();
			//$arr_donors = $query->fetchAll(PDO::FETCH_ASSOC);

			$query = $con->prepare("SELECT * FROM `donors` WHERE `bloodgroup` IN (" . $arr_plasma_match[$p['bloodgroup']] . ") AND `active`=1 ORDER BY RAND()");
			//$query->bindValue(":b", $arr_plasma_match[$p['bloodgroup']]);
			$query->execute();
			$arr_donors = $query->fetchAll(PDO::FETCH_ASSOC);
			usort($arr_donors, function ($v1, $v2) use(&$p) {
				if ($v1['Bloodgroup'] == $p['bloodgroup']) return -1;
				if ($v2['Bloodgroup'] == $p['bloodgroup']) return 1;
				else return 0;
			});
			array_splice($arr_donors, 3);
			//echo "<p>arr_donors: ";print_r($arr_donors); echo "</p>";
			//echo "<p>arr: ";print_r($arr);echo "</p>";
			//array_push($arr_donors, $arr);

			$n_donors = count($arr_donors); $n = 0;
			if ($n_donors > 0) {
				foreach ($arr_donors as $d) {
					if (ceil(abs(strtotime(date("Y-m-d")) - strtotime($d['DateOfRecovery'])) / 86400) < 14)
						continue;
					
					echo "<h2>" . $d['Name'] . ", " . $d['Bloodgroup'] . "</h2><p>";
					echo "<table>";
					echo "<tr><th>Age</th><td>" . $d['Age'] . "</td></tr>";
					echo "<tr><th>Gender</th><td>" . $d['Gender'] . "</td></tr>";
					echo "<tr><th>District</th><td>" . $d['District'] . "</td></tr>";
					echo "<tr><th>Recovery Date</th><td>" . $d['DateOfRecovery'] . "</td></tr>";
					echo "<tr><th>Phone</th><td>" . $d['WhatsApp'] . "</td></tr>";

					$q2 = $con->prepare("SELECT * FROM `volunteers` WHERE `name` = :n");
					$q2->bindValue(":n", $d['assigned_to']);
					$q2->execute();

					if ($q2->rowCount()) {
						$assigned_to = $q2->fetch(PDO::FETCH_ASSOC);
						echo "<tr><th>Assigned Volunteer</th><td>" . $assigned_to['fullname'] . "</td></tr>";
						echo "<tr><th>Volunteer contact</th><td>" . $assigned_to['whatsapp'] . "</td></tr>";
					}
					echo "</table>";

					$text = $d['Bloodgroup'] . " donor matched " .
					"\nName: " . $d['Name'] .
					"\nRecovery Date: " . $d['DateOfRecovery'] .
					"\nWhatsApp: " . $d['WhatsApp'] .
					"\nAge: " . $d['Age'] . " " . $d['Gender'] .
					"\nDistrict: " . $d['District'];

					$lnk = "https://wa.me/91" . $p['whatsapp'] . "?text=" . rawurlencode($text);
					
					echo "</p>";
					echo "<p><a href=\"" . $lnk . "\" target='_blank' class='btn btn-1 aligncenter'>Click to WhatsApp these details to Patient</a></p>";
					echo "<hr class='wp-block-separator is-style-wide'>";
					$n++;
				}
				if ($n != 0)
					echo "<a href='volunteer.php?v=" . $_GET['v'] . "&p=" . $_GET['p'] . "&d_mark=" . $_GET['patient'] . "' class='btn btn-2 aligncenter'>Click here to change status to 'donor details sent'</a>";
			}
			
			if ($n == 0) {
				echo "<p><strong>No donor is currently available at the moment. Please try again later.</strong></p>";
			}

			if ($n_donors > 3) {
				echo "<a href='viewdonors.php?v=" . $_GET['v'] . "&p=" . $_GET['p'] . "&patient=" . $_GET['patient'] . "' class='btn btn-3 aligncenter'>";
				echo "Request more donors" . "</a>";
			}
		} else {
			echo "<h1>Error: Patient not found</h1>";
			echo "<p>Please try again later.</p>";
		}
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
		<h1 class="entry-title">Error 404</h1>			</header><!-- .entry-header -->
	<div class="entry-content">
		<h1>Unfortunately, we couldn't find what you are looking for.</h1>
		<p>If you keep seeing this error, please <a href="contact.php">contact us</a>.</p>
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