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
        <h1 class="entry-title">Volunteer</h1>
    </header><!-- .entry-header -->
	<div class="entry-content">
<?php

function whatsapp_url($no, $text) {
    $str = "https://wa.me/91" . $no . "?text=" . rawurlencode($text);
    return $str;
}

function print_patient_row($p, $v) {
    $lnk = "volunteer.php?v=" . $v['name'] . "&p=" . $v['pass'];
	echo "<tr>";

	echo "<td><a href='" . $lnk . "&p_change=" . $p['whatsapp'] . "'>" . $p['name'] . ", " . $p['bloodgroup'] . "</a></td>";

	echo "<td>" . $p['district'] . "</td>";

	//echo "<tr>Current Status: ";
	echo "<td>";
    if ($p['status'] == 0) echo "Freshly Registered";
    else if ($p['status'] == 1) echo "Contacted";
	else if ($p['status'] == 2) echo "Verified";
	else if ($p['status'] == 3) echo "Donor details sent";
	else if ($p['status'] == 4) echo "Case closed";
    echo "</td>";
	echo "<td><a target='_blank' href=\"https://wa.me/91" . $p['whatsapp'] . "?text=Salaam%2C%20I'm%20";
	echo $v['fullname'];
	echo "%20from%20PlasmaKashmir.%0A%0AYou%20requested%20" . rawurlencode($p['bloodgroup']) . "%20plasma%20for%20%22";
	echo rawurlencode($p['name']);
	echo "%22%20currently%20in%20%22";
	echo rawurlencode($p['hospital']);
	echo "%22.%0A%0APlease%20send%20us%20the%20%2Arequisition%20form%2A%20as%20proof%20that%20the%20patient%20has%20been%20prescribed%20plasma%20therapy.\">" . $p['whatsapp'] ."</a></td>";

    echo "<td><code>" . $p['calling_no']  . "</code></td>";
    
    if ($p['status'] == 0 || $p['status'] == 1)
        echo "<td><a href='" . $lnk . "&p_mark=" . $p['whatsapp'] . "&chng=" . ($p['status'] + 1) . "'>Mark</a></td>";
    if ($p['status'] < 4 && $p['status'] > 1)
	    echo "<td><a href='viewdonors.php?v=" . $v['name'] . "&p=" . $v['pass'] . "&patient=" . $p['whatsapp'] . "'>". ($p['status'] == 2 ? "View" : "Request more") ."</a></td>";
    if ($p['status'] != 4)
        echo "<td>" . $p['current_condition'] . "</td>";
    echo "<td>" . $p['comments'] . "</td>";
    echo "</tr>";
}

if (isset($_GET['v']) && isset($_GET['p'])) {
    $query = $con->prepare("SELECT * FROM `volunteers` WHERE `name` = :v");
    $query->bindValue(":v", $_GET['v']);
    $query->execute();

    if ($query->rowCount() > 0) {
        $volunteer = $query->fetch(PDO::FETCH_ASSOC);

        if ($volunteer['pass'] == $_GET['p']) {
            // Grant access
            echo "<h3>Welcome, " . $volunteer['fullname'] . "</h3>";

            if (isset($_GET['p_assign'])) {
                // Assign a patient
                $query = $con->prepare("SELECT * FROM patients WHERE `whatsapp` = :w AND `assigned_to`='none'");
                $query->bindValue(":w", $_GET['p_assign']);
                $query->execute();

                if ($query->rowCount() > 0) {
                    $query = $con->prepare("UPDATE `patients` SET `assigned_to` = :v WHERE `patients`.`whatsapp` = :w;");
                    $query->bindValue(":v", $volunteer['name']);
                    $query->bindValue(":w", $_GET['p_assign']);

                    if ($query->execute()) {
                        echo "<p><i>You have been assigned the following patient successfully:</i> <b>";

                        $query = $con->prepare("SELECT * FROM patients WHERE `whatsapp` = :w");
                        $query->bindValue(":w", $_GET['p_assign']);

                        $query->execute();

                        $p = $query->fetch(PDO::FETCH_ASSOC);

                        echo $p['name'] . ", " . $p['bloodgroup'];
                        echo "</b><br>Please contact them as soon as possible!</p>";
                    } else {
                        echo "<p>Unfortunately, the patient could not be assigned to you right now, please try again.</p>";
                    }
                } else echo "<p>Unfortunately, the patient could not be assigned to you because the link seems to be broken.";
            } else if (isset($_GET['p_change'])) {
                if (isset($_POST['status'])) {
                    $s = strval($_POST['status']);
                    if ($s == '0' || $s == '1' || $s == '2' || $s == '3' || $s == '4') {
                        $query = $con->prepare("UPDATE `patients` SET `status` = :s WHERE `patients`.`whatsapp` = :w AND `assigned_to` = :v");
                        $query->bindValue(":v", $volunteer['name']);
                        $query->bindValue(":w", $_GET['p_change']);
                        $query->bindValue(":s", $s);

                        if (!$query->execute()) echo "<p><strong>Could not change patient status. Please try again.</strong></p>";
                        else echo "<p><i>Patient status updated successfully!</i></p>";
                    } else echo "<p><strong>Please go back and select a valid patient status.</strong></p>";
                } else {
                    // Change patient status
                    $query = $con->prepare("SELECT * FROM patients WHERE `whatsapp` = :w AND `assigned_to`=:v");
                    $query->bindValue(":w", $_GET['p_change']);
                    $query->bindValue(":v", $volunteer['name']);
                    $query->execute();

                    if ($query->rowCount() > 0) {
                        $p = $query->fetch(PDO::FETCH_ASSOC);
                        echo "<p>Details of <b>\"" . $p['name'] . "\", " . $p['bloodgroup'] . "</b></p>";
                        
                        echo "<table>";
                        foreach ($p as $key => $value) {
                            echo "<tr><th>" . $key . "</th><td>" . $value . "</td></tr>";
                        }
                        echo "</table>";

                        echo "<h4>Change Status</h4>";
                        echo "<p>Please make sure you have done the following before changing the status:</p>";

                        echo "<table><tr><th>Code</th><th>Status</th><th>Guidelines</th></tr>";
                        echo "<td>1</td><td>Contacted</td><td>Make sure you have called the patient and asked them to send you the requisition form on WhatsApp.</td></tr>";
                        echo "<td>2</td><td>Verified</td><td>Make sure the patient has sent you the requisition form duly signed.</td></tr>";
                        echo "<td>3</td><td>Donor details sent</td><td>Make sure you have sent the patient details of potential donor(s) via call or WhatsApp.</td></tr>";
                        echo "<td>4</td><td>Case Closed / Inactive</td><td>Make sure the patient has successfully recieved plasma or deceased, or the patient does not need plasma anymore.</td></tr>";
                        echo "</table>";
                    ?>
<form action="" method="POST">
    <label for="status">Current status:</label>
    <select name="status" id="status">
        <option value="select">-- Select --</option>
        <option value="0">0 - Freshly Registered</option>
        <option value="1">1 - Contacted</option>
        <option value="2">2 - Verified</option>
        <option value="3">3 - Donor details sent</option>
        <option value="4">4 - Case closed / inactive</option>
    </select>
    <input type="submit" value="Change" />
</form>
<hr class='wp-block-separator is-style-wide'>
                    <?php
                    } else {
                        echo "<p><strong>Unfortunately, the link to the patient appears to be broken or the patient is not assigned to you.</strong></p>";
                    }
                }
            } else if (isset($_GET['p_mark']) && isset($_GET['chng'])) {
                if ($_GET['chng'] < 3 && $_GET['chng'] > 0) {
                    // Verify if the patient is marked as freshly registered
                    $query = $con->prepare("SELECT * FROM patients WHERE `whatsapp` = :w AND `assigned_to`=:v AND (`status`='0' OR `status`=1)");
                    $query->bindValue(":w", $_GET['p_mark']);
                    $query->bindValue(":v", $volunteer['name']);
                    $query->execute();

                    if ($query->rowCount()) {
                        $p = $query->fetch(PDO::FETCH_ASSOC);
                        $query = $con->prepare("UPDATE `patients` SET `status` = :s WHERE `patients`.`whatsapp` = :w AND `assigned_to` = :v");
                        $query->bindValue(":w", $_GET['p_mark']);
                        $query->bindValue(":v", $volunteer['name']);
                        $query->bindValue(":s", $_GET['chng']);
                        
                        if ($query->execute())
                            echo "<p><i>You changed the status of '" . $p['name'] . "' to " . ($_GET['chng'] == 1 ? "'contacted'" : "'verified'") . "</i></p>";
                        else echo "<p><strong>Couldn't change patient status, please try again.</strong></p>";
                    } else echo "<p><strong>Couldn't change patient status: Link is either broken or the patient is not assigned to you.</strong></p>";
                } else echo "<p><strong>Couldn't change patient status: Invalid status change</strong></p>";
            } else if (isset($_GET['r_mark']) && isset($_GET['chng'])) {
                if ($_GET['chng'] != 0 || $_GET['chng'] != 1) {
                    // Verify if the patient is marked as freshly registered
                    $query = $con->prepare("SELECT * FROM referred WHERE `phone` = :w AND `assigned_to`=:v");
                    $query->bindValue(":w", $_GET['r_mark']);
                    $query->bindValue(":v", $volunteer['name']);
                    $query->execute();

                    if ($query->rowCount()) {
                        $r = $query->fetch(PDO::FETCH_ASSOC);
                        $query = $con->prepare("UPDATE `referred` SET `status` = :s WHERE `referred`.`phone` = :w AND `assigned_to` = :v");
                        $query->bindValue(":w", $_GET['r_mark']);
                        $query->bindValue(":v", $volunteer['name']);
                        $query->bindValue(":s", $_GET['chng']);
                        
                        if ($query->execute())
                            echo "<p><i>You changed the status of '" . $r['name'] . "' to " . ($_GET['chng'] == 1 ? "'contacted'" : "'not contacted'") . "</i></p>";
                        else echo "<p><strong>Couldn't change patient status, please try again.</strong></p>";
                    } else echo "<p><strong>Couldn't change patient status: Link is either broken or the patient is not assigned to you.</strong></p>";
                } else echo "<p><strong>Couldn't change patient status: Invalid status change</strong></p>";
            } else if (isset($_GET['d_mark'])) {
                // Verify if the patient exists
                $query = $con->prepare("SELECT * FROM patients WHERE `whatsapp` = :w AND `assigned_to`=:v");
                $query->bindValue(":w", $_GET['d_mark']);
                $query->bindValue(":v", $volunteer['name']);
                $query->execute();

                if ($query->rowCount()) {
                    $p = $query->fetch(PDO::FETCH_ASSOC);
                    $query = $con->prepare("UPDATE `patients` SET `status` = '3' WHERE `patients`.`whatsapp` = :w AND `assigned_to` = :v");
                    $query->bindValue(":w", $_GET['d_mark']);
                    $query->bindValue(":v", $volunteer['name']);
                    
                    if ($query->execute())
                        echo "<p><i>You changed the status of '" . $p['name'] . "' to 'Donor details sent'</i></p>";
                    else echo "<p><strong>Couldn't change patient status, please try again.</strong></p>";
                } else echo "<p><strong>Couldn't change patient status: Link is either broken or the patient is not assigned to you.</strong></p>";
            } else if (isset($_POST['referred_patient'])) {
                $no = $_POST['referred_patient'];
                if (strlen($no) == 10) {
                    send_text($no, "PlasmaKashmir: Someone has submitted your number to PlasmaKashmir. If you are looking for plasma donors, please register at https://www.plasmakashmir.com/");
                    echo "<p>A text has been sent to: $no. ";
                    echo "<a target='_blank' href='";
                    echo whatsapp_url($no, "Salaam\nI'm " . $volunteer['fullname'] . " from PlasmaKashmir. If you are looking for plasma donors, please register at https://www.plasmakashmir.com/\nPlease feel free to contact me if you need any help.");
                    echo "'>Click here to WhatsApp on the given number.</a>";
                    echo "</p>";
                } else {
                    echo "<p><strong>ERROR: </strong>Invalid number entered.</p>";
                }
            }

            $v = $volunteer;
            if (isset($_GET['d'])) {
                if (isset($_POST['submit'])) {
                    if ((strval($_POST['active']) == "1" || strval($_POST['active']) == "0")
                    && (strval($_POST['contacted']) == "1" || strval($_POST['contacted']) == "0")) {
                        $query = $con->prepare("UPDATE `donors` SET `active` = :active , `contacted` = :contacted, `DateOfRecovery` = :d WHERE `WhatsApp` = :w");
                        $query->bindValue(":active", $_POST['active']);
                        $query->bindValue(":contacted", $_POST['contacted']);
                        $query->bindValue(":d", $_POST['dateofrcv']);
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
        <table>
        
        <tr>
            <th>Date of Recovery:</th>
            <td>
                <input type="date" id="dateofrcv" name="dateofrcv" value='<?php echo $d['DateOfRecovery'];?>'>
            </td>
        </tr>
        <tr>
            <th>Active:</th>
            <td><select name="active" id="active">
                <option value="<?php echo strval($d['active']) == "1" ? '1' : '0'; ?>"><?php echo strval($d['active']) == "1" ? 'Yes' : 'No'; ?></option>
                <option value="<?php echo strval($d['active']) == "1" ? '0' : '1'; ?>"><?php echo strval($d['active']) == "1" ? 'No' : 'Yes'; ?></option>
            </select></td>
        </tr>
        <tr>
            <th>Contacted:</th>
            <td><select name="contacted" id="contacted">
                <option value="<?php echo strval($d['contacted']) == "1" ? '1' : '0'; ?>"><?php echo strval($d['contacted']) == "1" ? 'Yes' : 'No'; ?></option>
                <option value="<?php echo strval($d['contacted']) == "1" ? '0' : '1'; ?>"><?php echo strval($d['contacted']) == "1" ? 'No' : 'Yes'; ?></option>
            </select></td>
        </tr>
        </table>
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
                echo "<button onclick='makevisible(document.getElementById(\"donors\"))' class='btn btn-3 aligncenter'>Assigned donors</button>";
                echo "<div class='hide' id='donors'>";
                echo "<hr class='wp-block-separator is-style-wide'>";
                echo "<p>Please call / contact the following donors and verify whether all the information they have provided is correct.</p>";
                echo "<details><summary>Guidelines for call (click to expand)</summary>";
                echo "<ul><li>Verify donor's recovery date.</li>";
                echo "<li>Tell the donor that he/she can contact you when he/she wants to temporarily or permanently remove his/her name from the list.</li>";
                echo "<li>Thank the donor for helping save lives.</li>";
                echo "<li><strong>Mark the donor ACTIVE even if he has recently recovered from COVID-19</strong></li></ul></details>";

                $query = $con->prepare("SELECT * FROM donors WHERE `assigned_to` = :v ORDER BY `RegistrationDate` ASC");
                $query->bindValue(":v", $v['name']);
                $query->execute();

                echo "<h5>Donor details</h5><table>";
                echo "<tr><th>Name</th><th>Date of recovery</th><th>Age</th><th>District</th><th>Call</th><th>Contacted</th><th>Active</th><th>Comments</th><th>Change status</th></tr>";
                while ($d = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";

                    echo "<td>" . $d['Name'] . ", " . $d['Bloodgroup'] . "</td>";
                    echo "<td>" . $d['DateOfRecovery'] . "</td>";
                    echo "<td>" . $d['Age'] . "</td>";
                    echo "<td>" . $d['District'] . "</td>";
                    echo "<td>" . $d['WhatsApp'] . "</td>";

                    echo "<td>" . (strval($d['contacted']) == "1" ? "Yes" : "No") . "</td>";
                    echo "<td>" . (strval($d['active']) == "1" ? "Yes" : "No") . "</td>";
                    echo "<td>" . $d['comments'] . "</td>";
                    echo "<td><a href='volunteer.php?v=" . $v['name'] . "&p=" . $v['pass'] . "&d=" . $d['WhatsApp'] . "'>Click</a></td>";

                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
            }


            $query = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = '" . $volunteer['name'] . "'");
            $query->execute();

            echo "<button onclick='makevisible(document.getElementById(\"patients\"))' class='btn btn-4 aligncenter'>Assigned patients</button>";
            echo "<div class='hide' id='patients'>";
            echo "<hr class='wp-block-separator is-style-wide'>";
            echo "<p>You are responsible to engage with the following patients (click on the status of the patient to see details or change status):</p>";

            $query = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = '" . $volunteer['name'] . "' AND `status` = 0");
            $query->execute();

            if ($query->rowCount() > 0) {
                echo "<h4>Freshly Registered</h4>";
                echo "<p>Please contact the following patients as soon as possible.</p>";
                echo "<table>";
                echo "<tr><th>Name</th><th>District</th><th>Status</th><th>WhatsApp</th><th>Call</th><th>Mark as contacted</th><th>Condition</th><th>Comments</th></tr>";

                while ($p = $query->fetch(PDO::FETCH_ASSOC)) {
                    print_patient_row($p, $volunteer);
                }
                echo "</table>";
                echo "<hr class='wp-block-separator is-style-wide'>";
            }

            $query = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = '" . $volunteer['name'] . "' AND `status` = 1");
            $query->execute();

            if ($query->rowCount() > 0) {
                echo "<h4>Contacted</h4>";
                echo "<p>Please verify the details and requisition form of the following patients as soon as possible.</p>";
                echo "<table>";
                echo "<tr><th>Name</th><th>District</th><th>Status</th><th>WhatsApp</th><th>Call</th><th>Mark as verified</th><th>Condition</th><th>Comments</th></tr>";

                while ($p = $query->fetch(PDO::FETCH_ASSOC)) {
                    print_patient_row($p, $volunteer);
                }
                echo "</table>";
                echo "<hr class='wp-block-separator is-style-wide'>";
            }
            
            $query = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = '" . $volunteer['name'] . "' AND `status` = 2");
            $query->execute();

            if ($query->rowCount() > 0) {
                echo "<h4>Verified</h4>";
                echo "<p>Please connect the following patients with donors as soon as possible.</p>";
                echo "<table>";
                echo "<tr><th>Name</th><th>District</th><th>Status</th><th>WhatsApp</th><th>Call</th><th>Donors</th><th>Condition</th><th>Comments</th></tr>";

                while ($p = $query->fetch(PDO::FETCH_ASSOC))
                    print_patient_row($p, $volunteer);
                
                echo "</table>";
                echo "<hr class='wp-block-separator is-style-wide'>";
            }

            $query = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = '" . $volunteer['name'] . "' AND `status` = 3");
            $query->execute();

            if ($query->rowCount() > 0) {
                echo "<h4>Donors details sent</h4>";
                echo "<p>You have sent donor details to the following patients. Please stay in touch with them until the case is fully closed.</p>";
                echo "<table>";
                echo "<tr><th>Name</th><th>District</th><th>Status</th><th>WhatsApp</th><th>Call</th><th>Donors</th><th>Condition</th><th>Comments</th></tr>";

                while ($p = $query->fetch(PDO::FETCH_ASSOC))
                    print_patient_row($p, $volunteer);
                
                echo "</table>";
                echo "<hr class='wp-block-separator is-style-wide'>";
            }

            $query = $con->prepare("SELECT * FROM patients WHERE `assigned_to` = '" . $volunteer['name'] . "' AND `status` = 4");
            $query->execute();

            if ($query->rowCount() > 0) {
                echo "<h4>Case closed/inactive</h4>";
                echo "<details><summary>(Click to view) You have marked the following cases as closed/inactive.</summary>";
                echo "<table>";
                echo "<tr><th>Name</th><th>District</th><th>Status</th><th>WhatsApp</th><th>Call</th></tr>";

                while ($p = $query->fetch(PDO::FETCH_ASSOC))
                    print_patient_row($p, $volunteer);
                
                echo "</table></details>";
                echo "<hr class='wp-block-separator is-style-wide'>";
            }
            echo "</div>";

            echo "<button onclick='makevisible(document.getElementById(\"referrals\"))' class='btn btn-5 aligncenter'>Assigned referrals</button>";
            echo "<div class='hide' id='referrals'>";
            echo "<hr class='wp-block-separator is-style-wide'>";
            echo "<details><summary>(Click to view) Guidelines for calling</summary>";
            echo "<p><ul><li>Introduce PlasmaKashmir";
            echo "<li>Tell them there is a shortage of plasma available, and people are dying because of that, and that they can save human lives";
            echo "<li>Ask them if they know anyone else who has recovered from COVID-19, and collect their contact number";
            echo "</ul></p></details>";

            $query = $con->prepare("SELECT * FROM referred WHERE `assigned_to` = '" . $volunteer['name'] . "' AND `status` = 0");
            $query->execute();

            if ($query->rowCount() > 0) {
                echo "<h4>Pending referrals</h4>";
                echo "<table>";
                echo "<tr><th>Name</th><th>District</th><th>Phone</th><th>Submitted by</th><th>Submitter phone</th><th>Comments</th><th>Mark as contacted</th></tr>";

                while ($r = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";

                    echo "<td>" . $r['name'] . ", <b>" . (strval($r['bloodgroup']) == "Unknown" ? "" : $r['bloodgroup']) . "</b></td>";
                    echo "<td>" . $r['district'] . "</td>";
                    echo "<td>" . $r['phone'] . "</td>";
                    echo "<td>" . $r['submitted_by'] . "</td>";
                    echo "<td>" . $r['submitter_phone'] . "</td>";
                    echo "<td>" . $r['comments'] . "</td>";
                    echo "<td><a href='volunteer.php?v=" . $volunteer['name'] . "&p=" . $volunteer['pass'] . "&r_mark=" . $r['phone'] . "&chng=1'>Mark</a></td>";

                    echo "</tr>";
                    
                }
                echo "</table>";
            } else echo "<p>No referrals have been assigned to you so far...</p>";

            $query = $con->prepare("SELECT * FROM referred WHERE `assigned_to` = '" . $volunteer['name'] . "' AND `status` = 1");
            $query->execute();

            if ($query->rowCount() > 0) {
                echo "<details><summary>Contacted referrals (click to expand)</summary>";
                echo "<h4>Contacted</h4>";
                echo "<table>";
                echo "<tr><th>Name</th><th>District</th><th>Phone</th><th>Submitted by</th><th>Submitter phone</th><th>Comments</th><th>Mark as not contacted</th></tr>";

                while ($r = $query->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";

                    echo "<td>" . $r['name'] . "</td>";
                    echo "<td>" . $r['district'] . "</td>";
                    echo "<td>" . $r['phone'] . "</td>";
                    echo "<td>" . $r['submitted_by'] . "</td>";
                    echo "<td>" . $r['submitter_phone'] . "</td>";
                    echo "<td>" . $r['comments'] . "</td>";
                    echo "<td><a href='volunteer.php?v=" . $volunteer['name'] . "&p=" . $volunteer['pass'] . "&r_mark=" . $r['phone'] . "&chng=0'>Mark</a></td>";

                    echo "</tr>";   
                }
                echo "</table>";
                echo "</details>";
            }

            echo "</div>";

            $query = $con->prepare("SELECT * FROM patients WHERE `assigned_to`=:v");
            $query->bindValue(":v", $volunteer['name']);
            $query->execute();
            $p_helped = $query->rowCount();
            $query = $con->prepare("SELECT * FROM donors WHERE `assigned_to`=:v");
            $query->bindValue(":v", $volunteer['name']);
            $query->execute();
            $d_helped = $query->rowCount();

            echo "<h2>Refer a patient</h2>";
            echo "<form method='post'>";
            echo "<table style='display: table;'><tr><td><b>Contact no:</b></td><td><input type='number' name='referred_patient'/></td></tr></table>";
            echo "<div style='text-align: right;'><input type='submit' value='Refer'></div>";
            echo "</form>";

            echo "<h2>Your statistics</h2>";
            echo "<p>You are currently <b>" . ($volunteer['active'] ? "active" : "inactive") . "</b> and have helped " . $p_helped . " patients and " . $d_helped . " donors.</p>";
            if ($volunteer['active'] == 1)
                echo "<a href='https://wa.me/918494023439?text=Please%20mark%20me%20as%20inactive%20because...' class='btn btn-2 aligncenter'>Mark me as inactive</a>";
            else echo "<a href='https://wa.me/918494023439?text=Please%20mark%20me%20as%20active.' class='btn btn-1 aligncenter'>Mark me as active</a>";
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
<script type="text/javascript">
function makevisible(content) {
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
}

</script>
		</div><!-- .entry-content -->
</article><!-- #post-8 -->

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->
</div> <!-- site-content -->

<?php
require("./includes/footer.php");
?>