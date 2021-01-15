<?php
require("./includes/header.php");
require_once("./_con.php");
$con = DB::getConnection();

?>

<div class="site-content-contain">
		<div id="content" class="site-content">

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
        <article id="post-54" class="post-54 page type-page status-publish hentry">
	<header class="entry-header">
		<h1 class="entry-title">Patient Registration</h1>			</header><!-- .entry-header -->
	<div class="entry-content" id="entry_content">
  <script type="text/javascript">
<?php
if (!isset($_POST['patient_submit'])) {
  ?>
  //checking browser support
  if (typeof(Storage) !== "undefined") {
    //check for existing patients
    if(localStorage.getItem('patient_record')!=null) {
      // existing patient found
      // Parse previously stored  array and push new entries into the array
      var patient_info=localStorage.getItem('patient_record');
      heading = document.createElement("h1");
      heading.innerHTML = "You've already registered for \"" + patient_info + "\".<br>Do you wish to register for another patient?";
      document.getElementById("entry_content").appendChild(heading);
    }
  }
  <?php
}
?>

</script>
			</div><!-- .entry-content -->
</article><!-- #post-54 -->
			<?php
      
      $display_form = true;
      $isok_name = false; $isok_covid = false; $isok_age = false; $isok_whatsapp = false; $isok_calling = false; $isok_gender = false;
      $isok_district = false; $isok_bloodgroup = false; $isok_hospital = false; $isok_condition = false;
      $form_ok = true;
			if (isset($_POST['patient_submit'])) {
        // Check the form validity

        // Name
        if (isset($_POST['patient_name']) && strlen($_POST['patient_name']) > 0)
          $isok_name = true;
        
        // Hospital
        if (isset($_POST['patient_hospital']) && strlen($_POST['patient_hospital']) > 0)
          $isok_hospital = true;
        
        // Covid status
        if (isset($_POST['patient_covid'])) {
          if (strval($_POST['patient_covid']) == "yes")
            $isok_covid = true;
        }

        // Calling number
        if (isset($_POST['patient_calling_no'])) {
          if (strlen($_POST['patient_calling_no']) == 10) {
            $q = $con->prepare("SELECT * FROM patients WHERE calling_no = :number");
            $q->bindValue(":number", strval($_POST['patient_calling_no']));
            $q->execute();
            if ($q->rowCount() == 0)
              $isok_calling = true;
          }
          
      }

        // WhatsApp number
        if (isset($_POST['patient_whatsapp_no'])) {
            if (strlen($_POST['patient_whatsapp_no']) == 10) {
              $q = $con->prepare("SELECT * FROM patients WHERE whatsapp = :number");
              $q->bindValue(":number", strval($_POST['patient_whatsapp_no']));
              $q->execute();
              if ($q->rowCount() == 0)
                $isok_whatsapp = true;
            }
        }

        // Gender
        if (isset($_POST['patient_gender'])) {
          $isok_gender = true;
        }

        // District
        if (isset($_POST['patient_district'])) {
          $isok_district = true;
        }

        // Bloodgroup
        if (isset($_POST['patient_bloodgroup'])) {
          $isok_bloodgroup = true;
        }

        if (strval($_POST['patient_condition']) != "") {
          $isok_condition = true;
        }

       // Check if everything is okay
        if ($isok_name && $isok_hospital && $isok_covid && $isok_calling && $isok_whatsapp && $isok_gender && $isok_district && $isok_condition) {
          $query = $con->prepare("SELECT * FROM `volunteers` WHERE `active` = 1 ORDER BY RAND() LIMIT 1");
          $query->execute();
          $v = $query->fetch(PDO::FETCH_ASSOC);

          //bind the posted values to local variables;
          $name = $_POST["patient_name"];
          $hospital = $_POST["patient_hospital"];
          $calling = $_POST["patient_calling_no"];
          $whatsapp = $_POST["patient_whatsapp_no"];
          $gender = $_POST["patient_gender"];
          $district = $_POST["patient_district"];
          $bloodGroup = $_POST["patient_bloodgroup"];
          $cond = $_POST['patient_condition'];
          $comments = $_POST['patient_comments'];

          //At this point, we assume data is valid and we add it to the database
          //prepare the query
          $query = $con->prepare("INSERT INTO `patients` (`name`, `RegDate`, `hospital`, `calling_no`, `whatsapp`, `gender`, `district`, `bloodgroup`, `current_condition`, `status`, `assigned_to`, `comments`) VALUES (:name, :regdate, :hospital, :calling, :whatsapp, :gender, :district, :bloodgroup, :cond, 0, :v, :c)");
          $query->bindValue(":name", $name);
          $query->bindValue(":regdate", date("Y-m-d H:i:s"));
          $query->bindValue(":hospital", $hospital);
          $query->bindValue(":calling", $calling);
          $query->bindValue(":whatsapp", $whatsapp);
          $query->bindValue(":gender", $gender);
          $query->bindValue(":district", $district);
          $query->bindValue(":bloodgroup", $bloodGroup);
          $query->bindValue(":cond", $cond);
          $query->bindValue(":v", $v['name']);
          $query->bindValue(":c", $comments);

          if (!$query->execute())
            {
              // TODO Add option to whatsapp details instead
                echo "<p>Unfortunately, we are experiencing problems with our database. Please try again.</p>";
                $display_form = true;
                //echo $query->errorInfo() . "\n";
            }
            else
            {
              // Thank you submitting your form
              echo "<h1>Thank you, " . $_POST['patient_name'] . "</h1>";
              echo "<p>Your details have been saved. We will contact you on your phone number: <b>+91 " . $calling . "</b> and WhatsApp number: <b>+91 " . $_POST['patient_whatsapp_no'] . "</b>";
              echo " for verification.</p>";
              echo "<p><strong>Please keep the requisition form given by your doctor ready!</strong></p>";
              echo "<p>You have been assigned the following volunteer: <strong>" . $v['fullname'] . ", " . $v['whatsapp'] . "</strong>. <i>Please call them as soon as possible to notify them.</i></p>";
              echo "<h2>We care about you.</h2>";

              // Message the volunteer and patient
              send_text($v['whatsapp'], "PlasmaKashmir: A new patient has been assigned to you. Please check https://www.plasmakashmir.com/volunteer.php");
              send_text($calling, "PlasmaKashmir: Your assigned volunteer is: " . $v['fullname'] . ", " . $v['whatsapp'] . ". You can call / whatsapp them anytime you want.");
              ?>
              <script type="text/javascript">
              localStorage.setItem('patient_record', "<?php echo $_POST['patient_name']; ?>");
              </script>
              <?php
              $display_form = false;
            }
        }
        else {
          // display form again
          echo "<p style='color:red'>Error: Please verify the details and refill the form</p>";
          $form_ok = false;
          $display_form = true;
        }
			}
			else {
        $display_form = true;
      }
      if ($display_form) {
				?>
        <div class="entry-content">
		
    <?php
    if ($form_ok == true) {
    ?>

    <p>To register as a patient, fill in the form below. This will only take you two minutes.</p>
    <?php }
    ?>
    <h1 class="has-text-align-center">COVID-19 Patient registration form</h1>
    <hr class="wp-block-separator is-style-wide">
    
		<form action="" method="post">
  <label for="patient_name">Patient name:</label>
  <input type="text" id="patient_name" name="patient_name"
  <?php if(isset($_POST['patient_name'])) echo "value='" . $_POST['patient_name'] . "'";?>
  ><br>
  <?php
  if ($form_ok == false && $isok_name == false) echo "<p style='color:red'><i>Please enter a name.</i></p>";
  ?>

  <label for="patient_covid">Is the patient currently COVID-19 positive?</label>
  <input type="radio" id="patient_covid_yes" name="patient_covid" value="yes">
  <label for="patient_covid_yes">Yes</label>
  <input type="radio" id="patient_covid_no" name="patient_covid" value="no">
  <label for="patient_covid_no">No</label>
  <?php
  if ($form_ok == false && $isok_covid == false) echo "<p style='color:red'><i>You must be COVID-19 positive to register as a patient.</i></p>";
  ?>
  <br>

  <label for="patient_hospital">Full hospital address:</label>
  <input type="text" id="patient_hospital" name="patient_hospital"
  <?php if(isset($_POST['patient_hospital'])) echo "value='" . $_POST['patient_hospital'] . "'";?>
  ><br>
  <?php
  if ($form_ok == false && $isok_hospital == false) echo "<p style='color:red'><i>Please enter the complete hospital address.</i></p>";
  ?>

  <label for="patient_calling_no">Enter your phone number (10-digits):</label>
  <input type="number" id="patient_calling_no" name="patient_calling_no"
  <?php if(isset($_POST['patient_calling_no'])) echo "value='" . $_POST['patient_calling_no'] . "'";?>
  ><br>

  <?php
  if ($form_ok == false && $isok_calling == false) {
    if (isset($_POST['patient_calling_no'])) {
      $q = $con->prepare("SELECT * FROM patients WHERE calling_no = :number");
      $q->bindValue(":number", strval($_POST['patient_calling_no']));
      $q->execute();
      if ($q->rowCount() > 0)
        echo "<p style='color:red'><i>The number is already registered.</i></p>";
      else
        echo "<p style='color:red'><i>The number is incorrectly entered.</i></p>";
    }
  }
  ?>

  <label for="patient_whatsapp_no">Enter your WhatsApp number (10-digits, can be same as above):</label>
  <input type="number" id="patient_whatsapp_no" name="patient_whatsapp_no"
  <?php if(isset($_POST['patient_whatsapp_no'])) echo "value='" . $_POST['patient_whatsapp_no'] . "'";?>
  ><br>

  <?php
  if ($form_ok == false && $isok_whatsapp == false) {
    if (isset($_POST['patient_whatsapp_no'])) {
      $q = $con->prepare("SELECT * FROM patients WHERE whatsapp = :number");
      $q->bindValue(":number", strval($_POST['patient_whatsapp_no']));
      $q->execute();
      if ($q->rowCount() > 0)
        echo "<p style='color:red'><i>The number is already registered.</i></p>";
      else
        echo "<p style='color:red'><i>The number is incorrectly entered.</i></p>";
    }
  }
  ?><br>

  <label for="patient_gender">Patient's biological gender:</label>
  <input type="radio" id="patient_male" name="patient_gender" value="male">
  <label for="patient_male">Male</label>
  <input type="radio" id="patient_female" name="patient_gender" value="female">
  <label for="patient_female">Female</label>
  <?php
  if ($form_ok == false && $isok_gender == false) echo "<p style='color:red'><i>Please choose a gender.</i></p>";
  ?><br>

  <label for="patient_district">Select your district:</label>
  <select id="patient_district" name="patient_district">
    <option value="Anantnag">Anantnag</option>
    <option value="Bandipora">Bandipora</option>
    <option value="Baramulla">Baramulla</option>
    <option value="Budgam">Budgam</option>
    <option value="Doda">Doda</option>
    <option value="Ganderbal">Ganderbal</option>
    <option value="Jammu">Jammu</option>
    <option value="Kathua">Kathua</option>
    <option value="Kishtwar">Kishtwar</option>
    <option value="Kulgam">Kulgam</option>
    <option value="Kupwara">Kupwara</option>
    <option value="Poonch">Poonch</option>
    <option value="Pulwama">Pulwama</option>
    <option value="Rajouri">Rajouri</option>
    <option value="Ramban">Ramban</option>
    <option value="Reasi">Reasi</option>
    <option value="Samba">Samba</option>
    <option value="Shopian">Shopian</option>
    <option value="Srinagar">Srinagar</option>
    <option value="Udhampur">Udhampur</option>
  </select><br><br>

  <label for="patient_bloodgroup">Select patient's bloodgroup:</label>
  <select id="patient_bloodgroup" name="patient_bloodgroup">
    <option value="A+">A+</option>
    <option value="A-">A-</option>
    <option value="B+">B+</option>
    <option value="B-">B-</option>
    <option value="O+">O+</option>
    <option value="O-">O-</option>
    <option value="AB+">AB+</option>
    <option value="AB-">AB-</option>
  </select><br><br>

  <label for="patient_condition">Present condition of the patient:</label>
  <select id="patient_condition" name="patient_condition">
    <option value="">--Select--</option>
    <option value="Stable without Oxygen">Stable without Oxygen</option>
    <option value="Stable on Oxygen">Stable on Oxygen</option>
    <option value="Unstable on Non Invasive Ventilation">Unstable on Non Invasive Ventilation</option>
    <option value="Unstable on Ventilator">Unstable on Ventilator</option>
  </select><br>
  <?php
  if ($form_ok == false && $isok_condition == false) echo "<p style='color:red'><i>Please select the present condition of the patient.</i></p>";
  ?><br>

  <label for="patient_comments">Additional comments:</label>
  <textarea name="patient_comments"></textarea><br>
  <hr class="wp-block-separator is-style-wide">

  <p><i>Please verify the information before submitting.</i></p>

  <input type="submit" value="Submit" name="patient_submit">
</form>
      </div>
<?php
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->
        </div> <!-- site-content -->

<?php
require("./includes/footer.php");
?>