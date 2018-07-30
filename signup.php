<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Exercise Log - Sign Up</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <h1 class="center">Exercise Log - Sign Up</h1>
    
    <hr />
    
        <p class="nav"> <a href="index.php">Home</a> &#8226; <a href="logexercise.php">Log Exercise</a> &#8226;
                    <a href="viewprofile.php">View Profile</a> &#8226; <a href="editprofile.php">Edit Profile</a>
                    &#8226; <a href="logout.php">Log Out(<?=$_SESSION['username']?>)</a></p>
            
    <hr /><br /><br />

<?php
    require_once('connectvars.php');

    // Connect to the database
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
            or die('Error connecting to database');
  

    if (isset($_POST['submit'])) 
    {
        // Grab the profile data from the POST
        $username = mysqli_real_escape_string($dbc, trim($_POST['username']));
        $password1 = mysqli_real_escape_string($dbc, trim($_POST['password1']));
        $password2 = mysqli_real_escape_string($dbc, trim($_POST['password2']));
    
        if (!empty($username) && !empty($password1) && !empty($password2) && ($password1 == $password2)) 
        {
            // Make sure someone isn't already registered using this username
            $query = "SELECT * FROM exercise_user WHERE username = '$username'";
            $data = mysqli_query($dbc, $query)
                    or die('Error querying the database');
            if (mysqli_num_rows($data) == 0) 
            {
                // The username is unique, so insert the data into the database
                $query = "INSERT INTO exercise_user (username, password, join_date) VALUES ('$username', SHA('$password1'), NOW())";
                mysqli_query($dbc, $query)
                        or die('error querying the database');

                // Confirm success with the user
                echo '<p>Your new account has been successfully created. You\'re now ready to <a href="login.php">log in</a>.</p>';

                mysqli_close($dbc);
                exit();
            }
            else 
            {
                // An account already exists for this username, so display an error message
                echo '<p class="error">An account already exists for this username. Please use a different address.</p>';
                $username = "";
            }
        }
        else 
        {
            echo '<p class="error">You must enter all of the sign-up data, including the desired password twice.</p>';
        }
    }

?>

    <p>Please enter your username and desired password to sign up to Exercise Log.</p>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Registration Info</legend>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php if (!empty($username)) echo $username; ?>" /><br />
            <label for="password1">Password:</label>
            <input type="password" id="password1" name="password1" /><br />
            <label for="password2">Confirm Password:</label>
            <input type="password" id="password2" name="password2" /><br />
        </fieldset>
    <input type="submit" value="Sign Up" name="submit" />
    </form>
</body> 
</html>
