<?php
    require_once('connectvars.php');

    // Start the session
    session_start();

    // Clear the error message
    $error_msg = "";

    // If the user isn't logged in, try to log them in
    if (!isset($_SESSION['user_id'])) 
    {
        if (isset($_POST['submit'])) 
        {
            // Connect to the database
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the database');

            // Grab the user-entered log-in data
            $user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
            $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));

                if (!empty($user_username) && !empty($user_password)) 
                {
                    // Look up the username and password in the database
                    $query = "SELECT user_id, username FROM exercise_user WHERE username = '$user_username' AND password = SHA('$user_password')";
                    $data = mysqli_query($dbc, $query)
                            or die('Error querying the database');

                    if (mysqli_num_rows($data) == 1) 
                    {
                        // The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
                        $row = mysqli_fetch_array($data);
                        $_SESSION['user_id'] = $row['user_id'];
                        $_SESSION['username'] = $row['username'];
                        setcookie('user_id', $row['user_id'], time() + (60 * 60 * 24 * 30));    // expires in 30 days
                        setcookie('username', $row['username'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
                        $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
                        header('Location: ' . $home_url);
                    }
                    else 
                    {
                        // The username/password are incorrect so set an error message
                        $error_msg = 'Sorry, you must enter a valid username and password to log in.';
                    }
                }
                else 
                {
                    // The username/password weren't entered so set an error message
                    $error_msg = 'Sorry, you must enter your username and password to log in.';
                }
                
            // Closing the database
            mysqli_close($dbc);
        }
    }
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Exercise Log - Log In</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <h1 class="center">Exercise Log - Log In</h1>
    
    <hr />
    
        <p class="nav"> <a href="index.php">Home</a> &#8226; <a href="logexercise.php">Log Exercise</a> &#8226;
                    <a href="viewprofile.php">View Profile</a> &#8226; <a href="editprofile.php">Edit Profile</a>
                    &#8226; <a href="logout.php">Log Out(<?=$_SESSION['username']?>)</a></p>
            
    <hr />

<?php
    // If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
    if (empty($_SESSION['user_id'])) 
    {
        echo '<p class="error">' . $error_msg . '</p>';
?>

    <form class="form-horizontal" role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <legend>Log In</legend>
        <div class="form-group">   
            <label class="control-label col-sm-2" for="username">Username:</label>
            <div class="col-sm-2">
                <input type="text" name="username" value="<?php if (!empty($user_username)) echo $user_username; ?>" /><br />
            </div>
        </div>
        <div class="form-group">   
            <label class="control-label col-sm-2" for="password">Password:</label>
            <div class="col-sm-2">
                <input type="password" name="password" /><br />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input class="btn btn-default" type="submit" value="Log In" name="submit" />
            </div>
        </div>
    </form>

<?php
    }
    else 
    {
        // Confirm the successful log-in
        echo('<p class="login">You are logged in as ' . $_SESSION['username'] . '.</p>');
    }
?>

</body>
</html>
