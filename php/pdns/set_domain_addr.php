<?php
session_start();

//Constants
$TABLE_NAME		= "pdns.records";
$DOMAIN_SUFFIX	= ".avrahamshalev.com";

// initializing variables
$sdomain		= "";
$newIpAddr		= "";
$errors   = array(); 

// connect to the database
///////////////////////host///////user///pwd////table///
$db = mysqli_connect("localhost","pdns","pdns","pdns");

// REGISTER USER

///////////////Add a button which its input name (for example, the button name) is "set_sdomain_addr"
//			<button type="submit" class="btn" name="set_sdomain_addr">Set Subdomain IP Address</button>
//////////////////////////////////////////////////////////////////////////////////////////////////////

if (isset($_POST['set_sdomain_addr'])) {
	// receive all input values from the form
	$sdomain	=	mysqli_real_escape_string($db, $_POST['sdomain']);
	$newIpAddr	=	mysqli_real_escape_string($db, $_POST['newIpAddr']);

	// form validation: ensure that the form is correctly filled ...
	// by adding (array_push()) corresponding error unto $errors array
	if (empty($sdomain))	{ array_push($errors, "sdomain is required"); }
	if (empty($newIpAddr))	{ array_push($errors, "newIpAddr is required"); }

	if (count($errors) > 0) { return; }

//TODO:filter input fields!!! to ignore SQL Injection \ similiar attacks

	//By default, we should insert new sdomain.
	$fullDomainAsStoredInDb	= $sdomain . $DOMAIN_SUFFIX;
	$setDomainAddrCommand	= "INSERT INTO $TABLE_NAME (domain_id,name,content,type,ttl,prio) VALUES (2,'$fullDomainAsStoredInDb','$newIpAddr','A',300,NULL);";

	//Check whether sdimain already exists, and if so, change statement from INSERT to UPDATE
	$sdomain_check_query	= "SELECT * FROM $TABLE_NAME WHERE name='$fullDomainAsStoredInDb' LIMIT 1;";
	$result_sdomain			= mysqli_query($db, $sdomain_check_query);
	$existing_sdomain		= mysqli_fetch_assoc($result_sdomain);
	if ($existing_sdomain) { // if sdomain exists we need to update instaed of insert
		//echo "EXISTING DOMAIN.Domain ID:";
		$sdomainId				= $existing_sdomain['id'];
		//echo $domainId;
		$setDomainAddrCommand	= "UPDATE $TABLE_NAME SET content='$newIpAddr' WHERE id=$sdomainId;";
		//echo "NEW STATEMENT:" . $setDomainAddrCommand;
	}

	mysqli_query($db, $setDomainAddrCommand);
	
} elseif (isset($_POST['delete_sdomain'])) {
	// receive all input values from the form
	$sdomain	=	mysqli_real_escape_string($db, $_POST['sdomain']);
	$fullDomainAsStoredInDb	= $sdomain . $DOMAIN_SUFFIX;
	$sdomain_delete_query	= "DELETE FROM $TABLE_NAME WHERE name='$fullDomainAsStoredInDb';";
	mysqli_query($db, $sdomain_delete_query);
	
} elseif (isset($_POST['delete_all_sdomains'])) {
	$deleteAllSdomainsCommand	= "DELETE FROM $TABLE_NAME WHERE id>20;";
	mysqli_query($db, $deleteAllSdomainsCommand);
}
//////////////END OF PHP CODE
?>



<!DOCTYPE html>
<html>
<head>
  <title>Set Subdomain IP Address</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
  	<h2>Register</h2>
  </div>
	
  <form method="post">
  	<div class="input-group">
  	  <label>subdomain</label>
  	  <input type="text" name="sdomain" value="<?php echo $sdomain; ?>">
  	</div>
  	<div class="input-group">
  	  <label>newIpAddr</label>
  	  <input type="text" name="newIpAddr" value="<?php echo $newIpAddr; ?>">
  	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="set_sdomain_addr">Set!</button>
  	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="delete_sdomain">Delete subdomain!</button>
  	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="delete_all_sdomains">Delete All subdomains!</button>
  	</div>
  </form>
</body>
</html>