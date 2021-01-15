<?php
require("./includes/header.php");
require("_con.php");
$con = DB::getConnection();

function display_data($data) {
	$output = '<table>';
	foreach($data as $key => $var) {
		$output .= '<tr>';
		foreach($var as $k => $v) {
			if ($key === 0) {
				$output .= '<td><strong>' . $k . '</strong></td>';
			} else {
				$output .= '<td>' . $v . '</td>';
			}
		}
		$output .= '</tr>';
	}
	$output .= '</table>';
	echo $output;
}
?>

<div class="site-content-contain">
		<div id="content" class="site-content">

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			
<article id="post-8" class="post-8 page type-page status-publish hentry">
	<header class="entry-header">
		<h1 class="entry-title">Donors and Patients</h1>			</header><!-- .entry-header -->
	<div class="entry-content">
		<?php
		if (isset($_GET['con']) && $_GET['con'] == "XKBCDZ") {
			echo "<h1>Donors</h1>";
			$mysqli = new mysqli($host, $user, $pass, $database);
			if ($mysqli->connect_errno) {
				echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}
		
			$sql = "SELECT * FROM donors ORDER BY RegistrationDate DESC";  //edit your table name here
			$res = $mysqli->query($sql);

			echo "<table>";
			$first = true;
			while ($row = $res->fetch_assoc()) {
				if ($first) {
					foreach($row as $key => $value)
						echo "<th>$key</th>";
					$first = false;
				}
				//print_r($row);
				if (ceil(abs(strtotime(date("Y-m-d")) - strtotime($row['DateOfRecovery'])) / 86400) >= 14 && $row['active'] == 1)
					echo "<tr style='background: #00ff007a;'>";
				else echo "<tr>";
				foreach ($row as $value) {
					echo "<td>$value</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}
		else if (isset($_GET['con']) && $_GET['con'] == "XKBMDZ") {
			echo "<h1>Active Patients</h1>";
			$mysqli = new mysqli($host, $user, $pass, $database);
			if ($mysqli->connect_errno) {
				echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}
		
			$sql = "SELECT * FROM patients WHERE `status` != 4 ORDER BY RegDate DESC";  //edit your table name here
			$res = $mysqli->query($sql);

			echo "<table>";
			$first = true;
			while ($row = $res->fetch_assoc()) {
				if ($first) {
					foreach($row as $key => $value)
						echo "<th>$key</th>";
					$first = false;
				}
				//print_r($row);
				if ($row['status'] == 2) {
					echo "<tr style='background: #ff000063;'>";
				}
				else echo "<tr>";
				foreach ($row as $value) {
					echo "<td>$value</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		} else if (isset($_GET['con']) && $_GET['con'] == "XVBADZ") {
			echo "<h1>Referrals</h1>";
			$mysqli = new mysqli($host, $user, $pass, $database);
			if ($mysqli->connect_errno) {
				echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}
		
			$sql = "SELECT * FROM referred ORDER BY ReferDate DESC";
			$res = $mysqli->query($sql);

			echo "<table>";
			$first = true;
			while ($row = $res->fetch_assoc()) {
				if ($first) {
					foreach($row as $key => $value)
						echo "<th>$key</th>";
					$first = false;
				}
				//print_r($row);
				if ($row['status'] == 0) {
					echo "<tr style='background: #ff000063;'>";
				}
				else echo "<tr>";
				foreach ($row as $value) {
					echo "<td>$value</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		} else {
		?>
		<h1>Unfortunately, we couldn't find what you are looking for.</h1>
		<p>If you keep seeing this error, please <a href="contact.php">contact us</a>.</p>
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