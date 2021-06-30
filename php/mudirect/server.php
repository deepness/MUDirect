<?php
session_start();

include('utils.php');

// initializing variables
$username = "";
$email    = "";
$errors   = array(); 
$phone    = "";
$udid     = "";
$mac      = "";
$ipLocal  = "";

// connect to the database
$db = mysqli_connect("localhost","iotic","iot","iotica");



// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  
  $phone = "123456789";
  $udid = getGUID();
  $mac = "11:22:33:44:55:66";
  $ipLocal = "192.168.1.103";
  
  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

if (count($errors) > 0) { return; }

//TODO:filter input fields!!! to ignore SQL Injection \ similiar attacks

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $udid_check_query = "SELECT * FROM endpoints WHERE udid='$udid' LIMIT 1";
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  
  $result_udid = mysqli_query($db, $udid_check_query);
  $existingUdid = mysqli_fetch_assoc($result_udid);
  $result_user = mysqli_query($db, $user_check_query);
  $existingUser = mysqli_fetch_assoc($result_user);
  
  if ($existingUdid) { // if udid exists
      array_push($errors, "endpoint is already registered to an existing user");
    }
  if ($existingUser) { // if user exists
    if ($existingUser['username'] === $username) {
      array_push($errors, "Username \ email already exists");
    }

    if ($existingUser['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
    
    //create needed fields to add new row in table 'subdomains'
    $t = time();
    $subdomain = rand(1,99999) . $t;
    $query_subdomain_insert = "INSERT INTO subdomains (subdomain, timeRegistered) VALUES('$subdomain', '$t')";
  	$query_subdomain_get_id = "SELECT id FROM subdomains WHERE subdomain='$subdomain'";
  	
    mysqli_query($db, $query_subdomain_insert);
    $result_subdomain = mysqli_query($db, $query_subdomain_get_id);
    $existingSubdomain = mysqli_fetch_assoc($result_subdomain);
    $subdomainId = $existingSubdomain['id'];
    
    if(!$existingSubdomain) {
    array_push($errors, "query_subdomain_insert = " . $query_subdomain_insert);
    array_push($errors, "query_subdomain_get_id = " . $query_subdomain_get_id);
    return;
    }
    
    
    
    //create needed fields to add new row in table 'users'
  	$password = md5($password_1);//encrypt the password before saving in the database
    $query_user_insert = "INSERT INTO users (username,password,email,phone,subdomainID) VALUES('$username','$password','$email','$phone','$subdomainId')";
  	$query_user_get_id = "SELECT id FROM users WHERE username='$username'";
    
    mysqli_query($db, $query_user_insert);
    $result_user = mysqli_query($db, $query_user_get_id);
    $existingUser = mysqli_fetch_assoc($result_user);
    $userId = $existingUser['id'];
    
    if(!$existingUser) {
    array_push($errors, "query_user_insert = " . $query_user_insert);
    array_push($errors, "query_user_get_id = " . $query_user_get_id);
    return;
    }
    
    
    
    //create needed fields to add new row in table 'endpoints'
    $timeLastPing = $t;
    $ipExternal = "93.55.81.208";
    $usernameId = $userId;
    $query_endpoint_insert = "INSERT INTO endpoints (udid,timeLastPing,mac,ipLocal,ipExternal,usernameID) VALUES('$udid','$timeLastPing','$mac','$ipLocal','$ipExternal', '$usernameId')";
  	
  	mysqli_query($db, $query_endpoint_insert);
   
   
   
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	//header('location: index.php');
  }
}



// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}

?>