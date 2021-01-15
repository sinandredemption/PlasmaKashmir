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
		<h1 class="entry-title">Donor Registration</h1>			</header><!-- .entry-header -->
	<div class="entry-content">
  </div><!-- .entry-content -->
</article><!-- #post-54 -->
<?php
      $display_form = true;
      $isok_name = false; $isok_covid = false; $isok_age = false; $isok_whatsapp = false; $isok_gender = false; $isok_district = false;
      $isok_bloodgroup = false; $isok_hbp_diabetes = false; $isok_kidney = false; $isok_lung = false; $isok_liver = false; $isok_date = false;
      $isok_cancer = false; $isok_hypertension = false; $isok_whatsapp = false; $isok_weight = false;
      $form_ok = true;
			if (isset($_POST['donor_submit'])) {
        // Check the form validity

        // Name
        if (isset($_POST['donor_name']) && strlen($_POST['donor_name']) > 0)
          $isok_name = true;
        
        // Covid status
        if (isset($_POST['donor_covid'])) {
          if (strval($_POST['donor_covid']) == "yes")
            $isok_covid = true;
        }

        // Age
        if (isset($_POST['donor_age'])) {
          if ((int)$_POST['donor_age'] >= 18 && (int)$_POST['donor_age'] <= 65)
            $isok_age = true;
          //else echo "<p>ERROR: donor_age: '" . (int)$_POST['donor_age'] . "'</p>";
        }

        // WhatsApp number
        if (isset($_POST['donor_whatsapp_no']) && isset($_POST['donor_whatsapp_no2'])) {
          if ($_POST['donor_whatsapp_no'] == $_POST['donor_whatsapp_no2']) {
            if (strlen($_POST['donor_whatsapp_no']) == 10) {
              $q = $con->prepare("SELECT * FROM donors WHERE WhatsApp = :number");
              $q->bindValue(":number", strval($_POST['donor_whatsapp_no']));
              $q->execute();
              if ($q->rowCount() == 0)
                $isok_whatsapp = true;
            }
          }
        }

        // Gender
        if (isset($_POST['donor_gender'])) {
          $isok_gender = true;
        }

        // District
        if (isset($_POST['donor_district'])) {
          $isok_district = true;
        }

        // Bloodgroup
        if (isset($_POST['donor_bloodgroup'])) {
          $isok_bloodgroup = true;
        }

        // High blood pressure & diabetes
        if (isset($_POST['donor_hbp_diabetes'])) {
          if (strval($_POST['donor_hbp_diabetes']) == "no")
            $isok_hbp_diabetes = true;
        }

        if (isset($_POST['donor_kidney_disease'])) {
          if (strval($_POST['donor_kidney_disease']) == "no")
            $isok_kidney = true;
        }

        if (isset($_POST['donor_lung_disease'])) {
          if (strval($_POST['donor_lung_disease']) == "no")
            $isok_lung = true;
        }

        if (isset($_POST['donor_liver_disease'])) {
          if (strval($_POST['donor_liver_disease']) == "no")
            $isok_liver = true;
        }

        if (isset($_POST['donor_underweight'])) {
          if (strval($_POST['donor_underweight']) == "no")
            $isok_weight = true;
        }

        if (isset($_POST['donor_cancer'])) {
          if (strval($_POST['donor_cancer']) == "no")
            $isok_cancer = true;
        }

        if (isset($_POST['donor_hypertension'])) {
          if (strval($_POST['donor_hypertension']) == "no")
            $isok_hypertension = true;
        }

        if (isset($_POST['donor_dateofrcv'])) {
          if (strtotime(strval($_POST['donor_dateofrcv'])) <= strtotime(date("Y-m-d")))
            $isok_date = true;
        }

        // Check if everything is okay
        if ($isok_name && $isok_covid && $isok_age && $isok_whatsapp && $isok_gender && $isok_district && $isok_bloodgroup
            && $isok_hbp_diabetes && $isok_kidney && $isok_liver && $isok_lung && $isok_date && $isok_weight && $isok_cancer && $isok_hypertension) {

              $query = $con->prepare("SELECT * FROM `volunteers` WHERE `active` = 1 ORDER BY RAND() ASC LIMIT 1");
            $query->execute();
            $v = $query->fetch(PDO::FETCH_ASSOC);

              //bind the posted values to local variables;
              $name = $_POST["donor_name"];
              $covid = $_POST["donor_covid"];
              $whatsapp = $_POST["donor_whatsapp_no"];
              $whatsapp2 = $_POST["donor_whatsapp_no2"];
              $gender = $_POST["donor_gender"];
              $district = $_POST["donor_district"];
              $bloodGroup = $_POST["donor_bloodgroup"];
              $hbp_disease = $_POST["donor_hbp_diabetes"];
              $recover_date = $_POST["donor_dateofrcv"];
              $age = $_POST["donor_age"];
              $comments = $_POST["donor_comments"];
            //At this point, we assume data is valid and we add it to the database
            //prepare the query
            $query = $con->prepare("INSERT INTO donors (Name, RegistrationDate, DateOfRecovery, WhatsApp, Age, Gender, District, Bloodgroup, contacted, active, assigned_to, comments) VALUES (:name, :regdate, :date, :whatsapp, :age, :gender, :district, :blood, 0, 1, :v, :c)");
            $query->bindValue(":name", $name);
            $query->bindValue(":date", $recover_date);
            $query->bindValue(":regdate", date("Y-m-d H:i:s"));
            $query->bindValue(":whatsapp", $whatsapp);
            $query->bindValue(":age", $age);
            $query->bindValue(":gender", $gender);
            $query->bindValue(":district", $district);
            $query->bindValue(":blood", $bloodGroup);
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
              echo "<h1>Thank you, " . $_POST['donor_name'] . "</h1>";
              echo "<p>Your details have been saved. We will contact you on your WhatsApp number: <b>+91 " . $_POST['donor_whatsapp_no'] . "</b>";
              echo " and ask you to send us your COVID test reports.</p>";
              echo "<p>You have been assigned the following volunteer: <strong>" . $v['fullname'] . ", " . $v['whatsapp'] . "</strong>. You can call them anytime you want. You can also ask them to remove your name from website (temporarily or permanently).</p>";

              echo "<h2>You are a lifesaver and you should be proud of it!</h2>";

              // Message the volunteer and patient
              send_text($v['whatsapp'], "PlasmaKashmir: A new donor has been assigned to you. Please check https://www.plasmakashmir.com/volunteer.php");
              send_text($whatsapp, "PlasmaKashmir: Your assigned volunteer is: " . $v['fullname'] . ", " . $v['whatsapp'] . ". You can call them if you want to remove your name from the website.");
              
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

    <p>Thank you for potentially saving someone’s life! To register as a donor, fill in the form below. This will only take you two minutes. But before that, please verify the following information:</p>
    
    <ul>
    <li>I have tested positive for COVID-19 and recovered</li>
    <li>I am between 18 to 60 years old.</li>
    <li>I weight over 50 Kg</li>
    <li>I am <b>not</b> and was never pregnant (women only)</li>
    <li>I do <b>not</b> have diabetes</li>
    <li>I do <b>not</b> have high blood pressure</li>
    <li>I am <b>not</b> suffering from uncontrolled hypertension</li>
    <li>I am <b>not</b> a cancer survivor/patient.</li>
    <li>I do <b>not</b> have any kidney, heart, lung, liver disease or any other immune compromised state.</li>
    </ul>
    <?php }
    ?>
    <h1 class="has-text-align-center">Covid-19 Plasma donor registration form</h1>
    <hr class="wp-block-separator is-style-wide">
    
		<form action="" method="post">
  <label for="donor_name">Your full name:</label>
  <input type="text" id="donor_name" name="donor_name"
  <?php if(isset($_POST['donor_name'])) echo "value='" . $_POST['donor_name'] . "'";?>
  ><br>
  <?php
  if ($form_ok == false && $isok_name == false) echo "<p style='color:red'><i>Please enter a valid name.</i></p>";
  ?>

  <label for="donor_covid">Did you ever test positive for COVID-19?:</label>
  <input type="radio" id="donor_covid_yes" name="donor_covid" value="yes">
  <label for="donor_covid_yes">Yes</label>
  <input type="radio" id="donor_covid_no" name="donor_covid" value="no">
  <label for="donor_covid_no">No</label>
  <?php
  if ($form_ok == false && $isok_covid == false) echo "<p style='color:red'><i>You must have been COVID-19 positive in the past to donate.</i></p>";
  ?>
  <br>

  <label for="donor_dateofrcv">What was your date of recovery? (leave blank if you're still positive)</label>
  <input type="date" id="donor_dateofrcv" name="donor_dateofrcv" 
  <?php if(isset($_POST['donor_dateofrcv'])) echo "value='" . $_POST['donor_dateofrcv'] . "'";?>
  >
  <?php
  if ($form_ok == false && $isok_date == false) echo "<p style='color:red'><i>Please choose a valid date.</i></p>";
  ?><br>

  <label for="donor_whatsapp_no">Enter your phone number (10-digits):</label>
  <input type="number" id="donor_whatsapp_no" name="donor_whatsapp_no"
  <?php if(isset($_POST['donor_whatsapp_no'])) echo "value='" . $_POST['donor_whatsapp_no'] . "'";?>
  ><br>

  <label for="donor_whatsapp_no2">Enter your phone number again (same as above):</label>
  <input type="number" id="donor_whatsapp_no2" name="donor_whatsapp_no2"
  <?php if(isset($_POST['donor_whatsapp_no2'])) echo "value='" . $_POST['donor_whatsapp_no2'] . "'";?>
  >
  <?php
  if ($form_ok == false && $isok_whatsapp == false) {
    if (isset($_POST['donor_whatsapp_no'])) {
      $q = $con->prepare("SELECT * FROM donors WHERE WhatsApp = :number");
      $q->bindValue(":number", strval($_POST['donor_whatsapp_no']));
      $q->execute();
      if ($q->rowCount() > 0)
        echo "<p style='color:red'><i>The number is already registered.</i></p>";
      else echo "<p style='color:red'><i>The numbers are incorrectly entered. Please make sure the numbers match.</i></p>";
    }
  }
  ?><br>

  <label for="donor_age">Enter your age (in years):</label>
  <input type="number" id="donor_age" name="donor_age" <?php if(isset($_POST['donor_age'])) echo "value='" . $_POST['donor_age'] . "'";?> >
  <?php
  if ($form_ok == false && $isok_age == false) echo "<p style='color:red'><i>Only people of ages 18-65 are eligible to donate.</i></p>";
  ?> <br>

  <label for="donor_gender">What is your biological gender?</label>
  <input type="radio" id="donor_male" name="donor_gender" value="male">
  <label for="donor_male">Male</label>
  <input type="radio" id="donor_female" name="donor_gender" value="female">
  <label for="donor_female">Female</label>
  <?php
  if ($form_ok == false && $isok_gender == false) echo "<p style='color:red'><i>Please choose a gender.</i></p>";
  ?><br>

  <label for="donor_district">Select the district you are currently in:</label>
  <select id="donor_district" name="donor_district">
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

  <label for="donor_bloodgroup">Select your Bloodgroup:</label>
  <select id="donor_bloodgroup" name="donor_bloodgroup">
    <option value="A+">A+</option>
    <option value="A-">A-</option>
    <option value="B+">B+</option>
    <option value="B-">B-</option>
    <option value="O+">O+</option>
    <option value="O-">O-</option>
    <option value="AB+">AB+</option>
    <option value="AB-">AB-</option>
  </select><br><br>

  <label for="donor_hbp_diabetes">Do you have high blood pressure or diabetes?</label>
  <input type="radio" id="donor_hbp_diabetes_yes" name="donor_hbp_diabetes" value="yes">
  <label for="donor_hbp_diabetes_yes">Yes</label>
  <input type="radio" id="donor_hbp_diabetes_no" name="donor_hbp_diabetes" value="no">
  <label for="donor_hbp_diabetes_no">No</label>
  <?php
  if ($form_ok == false && $isok_hbp_diabetes == false) echo "<p style='color:red'><i>You must not have high blood pressure or diabetes to be eligible.</i></p>";
  ?><br>
  
  <label for="donor_kidney_disease">Do you have a kidney disease?</label>
  <input type="radio" id="donor_kidney_disease_yes" name="donor_kidney_disease" value="yes">
  <label for="donor_kidney_disease_yes">Yes</label>
  <input type="radio" id="donor_kidney_disease_no" name="donor_kidney_disease" value="no">
  <label for="donor_kidney_disease_no">No</label>
  <?php
  if ($form_ok == false && $isok_kidney == false) echo "<p style='color:red'><i>You must not have any kidney disease to be eligible.</i></p>";
  ?><br>
  
  <label for="donor_lung_disease">Do you have a lung disease?</label>
  <input type="radio" id="donor_lung_disease_yes" name="donor_lung_disease" value="yes">
  <label for="donor_lung_disease_yes">Yes</label>
  <input type="radio" id="donor_lung_disease_no" name="donor_lung_disease" value="no">
  <label for="donor_lung_disease_no">No</label>
  <?php
  if ($form_ok == false && $isok_lung == false) echo "<p style='color:red'><i>You must not have any lung disease to be eligible.</i></p>";
  ?><br>

  <label for="donor_liver_disease">Do you have a liver disease?</label>
  <input type="radio" id="donor_liver_disease_yes" name="donor_liver_disease" value="yes">
  <label for="donor_liver_disease_yes">Yes</label>
  <input type="radio" id="donor_liver_disease_no" name="donor_liver_disease" value="no">
  <label for="donor_liver_disease_no">No</label>
  <?php
  if ($form_ok == false && $isok_liver == false) echo "<p style='color:red'><i>You must not have any liver disease to be eligible.</i></p>";
  ?><br>

  <label for="donor_underweight">Do you weight less than 50 kgs?</label>
  <input type="radio" id="donor_underweight_yes" name="donor_underweight" value="yes">
  <label for="donor_underweight_yes">Yes</label>
  <input type="radio" id="donor_underweight_no" name="donor_underweight" value="no">
  <label for="donor_underweight_no">No</label>
  <?php
  if ($form_ok == false && $isok_weight == false) echo "<p style='color:red'><i>You must weight more than 50kgs to be eligible.</i></p>";
  ?><br>

  <label for="donor_cancer">Have you ever had cancer?</label>
  <input type="radio" id="donor_cancer_yes" name="donor_cancer" value="yes">
  <label for="donor_cancer_yes">Yes</label>
  <input type="radio" id="donor_cancer_no" name="donor_cancer" value="no">
  <label for="donor_cancer_no">No</label>
  <?php
  if ($form_ok == false && $isok_cancer == false) echo "<p style='color:red'><i>You must have never had cancer to be eligible.</i></p>";
  ?><br>

<label for="donor_hypertension">Do you suffer from uncontrolled hypertension?</label>
  <input type="radio" id="donor_hypertension_yes" name="donor_hypertension" value="yes">
  <label for="donor_hypertension_yes">Yes</label>
  <input type="radio" id="donor_hypertension_no" name="donor_hypertension" value="no">
  <label for="donor_hypertension_no">No</label>
  <?php
  if ($form_ok == false && $isok_hypertension == false) echo "<p style='color:red'><i>You must not suffer from uncontrolled hypertension to be eligible.</i></p>";
  ?><br>
  <label for="donor_comments">Additional comments:</label>
  <textarea name="donor_comments"></textarea><br>
  <hr class="wp-block-separator is-style-wide">

  <p><i>Please verify the information before submitting.</i></p>

  <input type="submit" value="Submit" name="donor_submit">
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