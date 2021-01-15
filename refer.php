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
		<h1 class="entry-title">Refer a potential donor</h1>			</header><!-- .entry-header -->
	<div class="entry-content" id="entry_content">
			</div><!-- .entry-content -->
</article><!-- #post-54 -->
			<?php
      
      $isok_submitter = false; $isok_submitter_phone = false;
      $isok_name = false; $isok_calling = false; $isok_district = false;
      $form_ok = true;
			if (isset($_POST['refer_submit'])) {
        // Check the form validity
        if (isset($_POST['your_name']) && strlen($_POST['your_name']) > 0)
          $isok_submitter = true;
        
        if (isset($_POST['your_phone']) && strlen($_POST['your_phone']) == 10)
          $isok_submitter_phone = true;

        // Name
        if (isset($_POST['refer_name']) && strlen($_POST['refer_name']) > 0)
          $isok_name = true;
      
        // Calling number
        if (isset($_POST['refer_phone'])) {
          if (strlen($_POST['refer_phone']) == 10) {
            $isok_calling = true;
          }
        }

        // District
        if (isset($_POST['refer_district'])) {
          $isok_district = true;
        }

       // Check if everything is okay
        if ($isok_name && $isok_calling && $isok_district && $isok_submitter && $isok_submitter_phone) {
          $q = $con->prepare("SELECT * FROM referred WHERE phone = :number");
          $q->bindValue(":number", strval($_POST['refer_phone']));
          $q->execute();
          if ($q->rowCount() == 0) {
            $query = $con->prepare("SELECT * FROM `volunteers` WHERE `active` = 1 ORDER BY RAND() LIMIT 1");
            $query->execute();
            $v = $query->fetch(PDO::FETCH_ASSOC);

            //bind the posted values to local variables;
            $submitter = $_POST['your_name'];
            $submitter_ph = $_POST['your_phone'];
            $name = $_POST["refer_name"];
            $calling = $_POST["refer_phone"];
            $district = $_POST["refer_district"];
            $bloodgroup = $_POST["refer_bloodgroup"];
            $comments = strval($_POST["refer_comments"]);

            //At this point, we assume data is valid and we add it to the database
            //prepare the query
            $query = $con->prepare("INSERT INTO `referred` (`name`, `ReferDate`, `phone`, `district`, `bloodgroup`, `submitted_by`, `submitter_phone`, `assigned_to`, `comments`) VALUES (:name, :regdate, :calling, :district, :bg, :submitter, :submitterph, :v, :comments)");
            $query->bindValue(":name", $name);
            $query->bindValue(":regdate", date("Y-m-d H:i:s"));
            $query->bindValue(":calling", $calling);
            $query->bindValue(":district", $district);
            $query->bindValue(":bg", $bloodgroup);
            $query->bindValue(":submitter", $submitter);
            $query->bindValue(":submitterph", $submitter_ph);
            $query->bindValue(":v", $v['name']);
            $query->bindValue(":comments", $comments);

            if (!$query->execute())
            {
                // TODO Add option to whatsapp details instead
                  echo "<p>Unfortunately, we are experiencing problems with our database. Please try again, and contact +91 8494023439 for help.</p>";
            } else {
                // Thank you submitting your form
                echo "<h1>Thank you</h1>";
                echo "<p>You referred: '$name' from $district</p>";
                echo "<p><strong>Thank you for helping save human lives!</strong></p>";
                echo "<h2>You can refer more potential donors below.</h2>";

                // Message the volunteer and refer
                //send_text($v['whatsapp'], "PlasmaKashmir: A new refer has been assigned to you. Please check https://www.plasmakashmir.com/volunteer.php");
                //send_text($calling, "PlasmaKashmir: Thank you for helping save human lives! Please refer more potential donors if you can.");
            }
          } else {
            echo "<p><strong>The given number appears to be already registered on our database.</strong></p>";
          }
        }
        else {
          // display form again
          echo "<p style='color:red'>Error: Please verify the details and refill the form</p>";
          $form_ok = false;
        }
      }
				?>
<div class="entry-content">
<p class="has-drop-cap">Do you know someone who has recovered from COVID-19? Then please provide us with their contact details and we will try to persuade them
  to come forward and donate plasma. Please know that PlasmaKashmir will never unnecessarily call the referred contacts, neither will
  share any information with any third party, and try to maintain high standards of confidentiality and ethics when dealing with the given contacts.</p>
<p><strong>To refer a potential donor, fill in the form below.</strong></p>
<h1 class="has-text-align-center">Refer Potential Donors</h1>
<hr class="wp-block-separator is-style-wide">
<form action="" method="post">
  <label>Your name: <input type="text" name="your_name" value="<?php if (isset($_POST['your_name'])) echo $_POST['your_name']; ?>"></label><br>
  <label>Your phone number: <input type="number" name="your_phone" value="<?php if (isset($_POST['your_phone'])) echo $_POST['your_phone']; ?>"></label><br>

  <p>Enter the details of the recovered COVID-19 patient below:</p>
  <label for="refer_name">Name: </label><input type="text" name="refer_name"><br>
  <label for="refer_phone">Number: </label><input type="number" name="refer_phone"><br>
  <table style="display: table;"><tr><td>
  <label for="refer_district">District:&nbsp;&nbsp;
    <select name="refer_district">
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
    </select></label>
    </td><td>
    <label for="refer_bloodgroup">Bloodgroup:&nbsp;&nbsp;
  <select id="refer_bloodgroup" name="refer_bloodgroup">
    <option value="Unknown">Unknown</option>
    <option value="A+">A+</option>
    <option value="A-">A-</option>
    <option value="B+">B+</option>
    <option value="B-">B-</option>
    <option value="O+">O+</option>
    <option value="O-">O-</option>
    <option value="AB+">AB+</option>
    <option value="AB-">AB-</option>
  </select> </label>
  </td></tr></table>
  
  <label for="refer_comments">Comments: </label><textarea name="refer_comments"></textarea><br>
  <hr class="wp-block-separator is-style-wide">

<input type="submit" class="btn btn-1 aligncenter" value="Submit / Refer more" name="refer_submit">



</form>

</div>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->
        </div> <!-- site-content -->

<?php
require("./includes/footer.php");
?>