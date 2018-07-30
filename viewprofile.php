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
    <title class="center">Exercise Log - View Profile</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <h1 class="center">Exercise Log - View Profile</h1>
    
    <hr />
    
        <p class="nav"> <a href="index.php">Home</a> &#8226; <a href="logexercise.php">Log Exercise</a> &#8226;
                    <a href="viewprofile.php">View Profile</a> &#8226; <a href="editprofile.php">Edit Profile</a>
                    &#8226; <a href="logout.php">Log Out(<?=$_SESSION['username']?>)</a></p>
            
    <hr />

<?php
    require_once('appvars.php');
    require_once('connectvars.php');

    // Make sure the user is logged in before going any further.
    if (!isset($_SESSION['user_id'])) 
    {
        echo '<p>Please <a href="login.php">log in</a> to access this page.<p>';
        exit();
    }
    else 
    {
        echo('<p>You are logged in as ' . $_SESSION['username'] . '. <a href="logout.php">Log out</a>.</p>');
    }

    // Connect to the database
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
            or die('Error connecting to database');

    // Grab the profile data from the database
    if (!isset($_GET['user_id'])) 
    {
        $query = "SELECT username, first_name, last_name, gender, birthdate, weight, picture FROM exercise_user WHERE user_id = '" . 
                $_SESSION['user_id'] . "'";
    
    }
    else 
    {
        $query = "SELECT username, first_name, last_name, gender, birthdate, weight, picture FROM exercise_user WHERE user_id = '" . 
                $_GET['user_id'] . "'";
    }
    $data = mysqli_query($dbc, $query)
            or die('Error querying the database');

    if (mysqli_num_rows($data) == 1) 
    {
        // The user row was found so display the user data
        $row = mysqli_fetch_array($data);
        echo '<div class="container"><table class="table table-condensed">';
        if (!empty($row['username'])) 
        {
            echo '<div class="row"><tr><td class="col-md-1">Username:</td><td>' . $row['username'] . '</td></tr></div>';
        }
        if (!empty($row['first_name'])) 
        {
            echo '<div class="row"><tr><td class="col-md-1">First name:</td><td>' . $row['first_name'] . '</td></tr></div>';
        }
        if (!empty($row['last_name'])) 
        {
            echo '<div class="row"><tr><td class="col-md-1">Last name:</td><td>' . $row['last_name'] . '</td></tr></div>';
        }
        if (!empty($row['gender'])) 
        {
            echo '<div class="row"><tr><td class="col-md-1">Gender:</td><td>';
        if ($row['gender'] == 'M') 
        {
            echo 'Male';
        }
        else if ($row['gender'] == 'F') 
        {
            echo 'Female';
        }
        else 
        {
            echo '?';
        }
        echo '</td></tr></div>';
        }
        if (!empty($row['birthdate'])) 
        {
            if (!isset($_GET['user_id']) || ($_SESSION['user_id'] == $_GET['user_id'])) 
            {
                // Show the user their own birthdate
                echo '<div class="row"><tr><td class="col-md-1">Birthdate:</td><td>' . $row['birthdate'] . '</td></tr></div>';
            }
            else 
            {
                // Show only the birth year for everyone else
                list($year, $month, $day) = explode('-', $row['birthdate']);
                echo '<div class="row"><tr><td class="col-md-1">Year born:</td><td>' . $year . '</td></tr></div>';
            }
        }
        if (!empty($row['weight'])) 
        {
            echo '<div class="row"><tr><td class="col-md-1">Weight:</td><td>' . $row['weight'] . '</td></tr><div>';
        }
        if (!empty($row['picture'])) 
        {
            echo '<div class="row"><tr><td class="col-md-1">Picture:</td><td><img src="' . MM_UPLOADPATH . $row['picture'] .
                    '" alt="Profile Picture" /></td></tr></div>';
        }
        echo '</table></div><br />';
        
        // Getting data from database to display there workouts
        $query2 = "SELECT exercise_id, date, type_of_exercise, time_in_minutes, heartrate , calories_burned FROM exercise_log WHERE user_id ='" .
                $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 15";
                
        $data2 = mysqli_query($dbc, $query2)
                or die('Error querying the database');
                
        echo '<div class="container "><table class="table table-condensed">';
    
        $i = 0;
    
        // Looping through array to output complete storys
        while ($row = mysqli_fetch_array($data2))
        {
            if ($i == 0)
            {
                echo '<div class="row"><tr><th> Date </th><th> Type </th><th> Time in Minutes </th><th> Heart Rate </th><th> Calories Burned </th></tr></div>';
            }
        
            echo '<div class="row"><tr><td> ' . $row['date'] . ' </td><td> ' . $row['type_of_exercise'] . ' </td><td> ' . $row['time_in_minutes'] . ' </td><td> ' .
                    $row['heartrate'] . ' </td><td> ' . $row['calories_burned'] . ' </td><td><a href="removeexercise.php?exercise_id=' .
                    $row['exercise_id'] . '&amp;date=' . $row['date'] . '&amp;type_of_exercise=' . $row['type_of_exercise'] . '">Remove</a></td></tr></div>';
        
            $i++;
        }
    
        echo '</table></div>';
        
                
        if (!isset($_GET['user_id']) || ($_SESSION['user_id'] == $_GET['user_id'])) 
        {
            echo '<p>Would you like to <a href="editprofile.php">edit your profile</a>?<br />';
            echo '<p>Would you like to go back to the <a href="index.php">Home Page</a>?</p>';
        }
    } // End of check for a single row of user results
    else 
    {
        echo '<p class="error">There was a problem accessing your profile.</p>';
    }

    // Closing the database
    mysqli_close($dbc);
?>
</body> 
</html>
