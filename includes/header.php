<?php
function get_siteurl(){
    return sprintf(
      "%s://%s%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['SERVER_NAME'],
      $_SERVER['REQUEST_URI']
    );
  }
?>

<!DOCTYPE html>
<html lang="en-US" class="js svg background-fixed">
<head>
  <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<title>Plasma Kashmir</title>
<link rel="dns-prefetch" href="http://fonts.googleapis.com/">
<link rel="dns-prefetch" href="http://s.w.org/">
<link href="https://fonts.gstatic.com/" crossorigin="" rel="preconnect">
		<script src="./files/wp-emoji-release.min.js.download" type="text/javascript" defer=""></script>
		<style>
img.wp-smiley,
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 .07em !important;
	vertical-align: -0.1em !important;
	background: none !important;
	padding: 0 !important;
}
</style>
	<link rel="stylesheet" id="wp-block-library-css" href="./files/style.min.css" media="all">
<link rel="stylesheet" id="wp-block-library-theme-css" href="./files/theme.min.css" media="all">
<link rel="stylesheet" id="twentyseventeen-fonts-css" href="./files/css" media="all">
<link rel="stylesheet" id="twentyseventeen-style-css" href="./files/style.css" media="all">
<link rel="stylesheet" id="twentyseventeen-block-style-css" href="./files/blocks.css" media="all">
<script src="./files/jquery.js.download"></script>
<script src="./files/jquery-migrate.min.js.download"></script>

<style id="wp-custom-css">
			input[type="radio"] {
	float:left;
}

label {
	/*border-bottom: 1px solid;*/
}


/* General button style */
.btn {
    width: 100%;
    text-align: center;
    border: none;
    font-size: inherit;
    color: inherit;
    background: none;
    padding: 1em 3em;
    display: inline-block;
    margin: 0.1em 0.25em;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 100;
    outline: none;
    position: relative;
    -webkit-transition: all 0.3s  !important;
    -moz-transition: all 0.3s  !important;
    transition: all 0.3s !important;
    box-shadow: none !important;
}

/* Button 1 */
.btn-1 {
    background: #43A047;
    color: #fff;
}
.btn-2 {
    background: #E53935;
    color: #fff;
}

.btn-1:hover {
    background: #66BB6A;
    color:#fff !important;
    box-shadow: none !important;
}

.btn-1:active {
    background: #66BB6A;
    top: 2px;
}
.btn-2:hover {
    background: #EF5350;
    color:#fff !important;
    box-shadow: none !important;
}

.btn-2:active {
    background: #EF5350;
    top: 2px;
}

.btn-3 {
	background: darkcyan;
	color: #fff;
}

.btn-3:hover {
    background: #10bebe;
    color:#fff !important;
    box-shadow: none !important;
}

.btn-3:active {
    background: #10bebe;
    top: 2px;
}

.btn-4 {
	background: crimson;
	color: #fff;
}

.btn-4:hover {
    background: #e86b83;
    color:#fff !important;
    box-shadow: none !important;
}

.btn-4:active {
    background: #e86b83;
    top: 2px;
}

.btn-5 {
	background: chocolate;
	color: #fff;
}

.btn-5:hover {
    background: ##ca8f65;
    color:#fff !important;
    box-shadow: none !important;
}

.btn-5:active {
    background: ##ca8f65;
    top: 2px;
}

table {
    display: inline-block;
	overflow-x: auto;
}

.hide {
    display: none;
}
</style>
</head>

<body class="page-template page-template-patient page-template-patient-php page page-id-136 wp-embed-responsive has-header-image page-one-column colors-light">
<div id="page" class="site">

	<header id="masthead" class="site-header" role="banner">

		<div class="custom-header" style="margin-bottom: 71px;">

		<div class="custom-header-media">
			<div id="wp-custom-header" class="wp-custom-header"><img src="./files/header.jpg" width="2000" height="1200" alt="Plasma Kashmir"></div>		</div>

	<div class="site-branding">
	<div class="wrap">
		<div class="site-branding-text">
							<p class="site-title"><a href="index.php" rel="home">Plasma Kashmir</a></p>
			
							<p class="site-description">Donate Plasma. Save Lives.</p>
					</div><!-- .site-branding-text -->

		
	</div><!-- .wrap -->
</div><!-- .site-branding -->

</div><!-- .custom-header -->

<div class="navigation-top" style="text-align-last: center;">
				<div class="wrap">
					<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Top Menu">
	<button class="menu-toggle" aria-controls="top-menu" aria-expanded="false">Menu</button>

	<div class="menu-top-menu-container">
<ul id="top-menu" class="menu">
<li id="menu-item-22" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home menu-item-22"><a href="index.php">Home</a></li>
<li id="menu-item-134" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-134"><a href="donor.php">Donor Registration</a></li>
<li id="menu-item-138" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-138"><a href="patient.php" aria-current="page">Patient registration</a></li>
<li id="menu-item-23" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-23"><a href="refer.php">Refer a potential donor</a></li>
<li id="menu-item-23" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-23"><a href="about.php">About</a>
<ul class="sub-menu">
	<li id="menu-item-25" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-25"><a href="contribute.php">Contribute</a></li>
	<li id="menu-item-25" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-25"><a href="contact.php">Contact</a></li>
</ul>
</li>
</ul></div>
	</nav><!-- #site-navigation -->
				</div><!-- .wrap -->
			</div><!-- .navigation-top -->
		
	</header><!-- #masthead -->
