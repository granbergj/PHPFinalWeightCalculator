<?php
    session_start();

    // If the session vars aren't set, try to set them with a cookie
    if (!isset($_SESSION['user_id'])) 
    {
        if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) 
        {
            $_SESSION['user_id'] = $_COOKIE['user_id'];
            $_SESSION['username'] = $_COOKIE['username'];
        }
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Your Personal Exercise Log</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <h1 class="center">Your Personal Exercise Log</h1>
    
    <hr />
    
        <p class="nav"> <a href="index.php">Home</a> &#8226; <a href="logexercise.php">Log Exercise</a> &#8226;
                    <a href="viewprofile.php">View Profile</a> &#8226; <a href="editprofile.php">Edit Profile</a>
                    &#8226; <a href="logout.php">Log Out(<?=$_SESSION['username']?>)</a></p>
            
    <hr />
    
<?php
    require_once('connectvars.php');

    // Generate the navigation menu
    if (isset($_SESSION['username'])) 
    {
?>
        &#8226;<a href="index.php">Home</a><br /> &#8226; <a href="logexercise.php">Log Exercise</a><br /> &#8226;
                <a href="viewprofile.php">View Profile</a><br /> &#8226; <a href="editprofile.php">Edit Profile</a>
                <br />&#8226; <a href="logout.php">Log Out(<?=$_SESSION['username']?>)</a>
<?php
    }
    else 
    {
?>
        &#8226; <a href="login.php">Log In</a><br />&#8226; <a href="signup.php">Sign Up</a>
<?php
    }

?>

</body> 
</html>
