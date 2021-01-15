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
	<header class="entry-header">
        <h1 class="entry-title">Volunteer Performance</h1>
    </header><!-- .entry-header -->
	<div class="entry-content">
<?php
if (isset($_GET['v']) && isset($_GET['p'])) {
    $query = $con->prepare("SELECT * FROM `volunteers` WHERE `name` = :v");
    $query->bindValue(":v", $_GET['v']);
    $query->execute();

    if ($query->rowCount() > 0) {
        $volunteer = $query->fetch(PDO::FETCH_ASSOC);

        if ($volunteer['pass'] == $_GET['p']) {
            // Grant access
			echo "<h3>Welcome, " . $volunteer['fullname'] . "</h3>";

			$query = $con->prepare("SELECT * FROM `volunteers` WHERE `active` = 1");
			$query->execute();
			
			$all_volunteers = array();
			$i = 0;
			while ($v = $query->fetch(PDO::FETCH_ASSOC)) {
				$i++;
				$latency = 0;
				// Fullname, patients helped, donors helped, pending donors, pending patients
				$q = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = :v AND `status` != 0");
				$q->bindValue(":v", $v['name']);
				$q->execute();
				$p_help = $q->rowCount();

				$q = $con->prepare("SELECT * FROM donors WHERE `assigned_to` = :v AND `contacted` != 0");
				$q->bindValue(":v", $v['name']);
				$q->execute();
				$d_help = $q->rowCount();

				$q = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = :v AND `status` = 0");
				$q->bindValue(":v", $v['name']);
				$q->execute();
				$p_pending = $q->rowCount();

				while ($p = $q->fetch(PDO::FETCH_ASSOC))
					$latency += abs(strtotime(date("Y-m-d h:i:sa")) - strtotime($p['RegDate']));

				$q = $con->prepare("SELECT * FROM donors WHERE `assigned_to` = :v AND `contacted` = 0");
				$q->bindValue(":v", $v['name']);
				$q->execute();
				$d_pending = $q->rowCount();
				while ($d = $q->fetch(PDO::FETCH_ASSOC))
					$latency += abs(strtotime(date("Y-m-d h:i:sa")) - strtotime($d['RegistrationDate']));

				$latency = ceil($latency / 864.);
				array_push($all_volunteers, array("Fullname" => $v['fullname'],
				"Patients helped" => $p_help, "Donors helped" => $d_help,
				"Patients pending" => $p_pending, "Donors Pending" => $d_pending, "Latency" => $latency));
			}

			usort($all_volunteers, function ($v1, $v2) {
				if ($v1['Latency'] == $v2['Latency']) {
					return $v1['Patients helped'] + $v1['Donors helped'] < $v2['Patients helped'] + $v2['Donors helped'] ? 1 : -1;
				}
				return $v1['Latency'] < $v2['Latency'] ? -1 : 1;
			});

			echo "<table>";
			$first = true;
			echo "<tr><th>Rank</th>";
			foreach ($all_volunteers[0] as $key => $value)
				echo "<th>" . $key . "</th>";
			echo "</tr>";
			$i = 1;
			foreach ($all_volunteers as $v) {
				echo "<tr><td>$i</td>";
				$i++;
				foreach($v as $value)
					echo "<td>" . $value . "</td>";
				echo "</tr>";
			}
			echo "</table>";
        }
        else {
            echo "<h1>Error Code: 4005 (password incorrect)</h1>";
        }
    }
} else {
    ?>
<form method="GET">
<label for="name">Username:</label>
<input id="name" name="v" type="text"><br>
<label for="pass">Password:</label>
<input id="pass" name="p" type="password"><br>
<input type="submit" />
</form>
    <?php
}
?>
		</div><!-- .entry-content -->
</article><!-- #post-8 -->

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->
</div> <!-- site-content -->

<?php
require("./includes/footer.php");
?>