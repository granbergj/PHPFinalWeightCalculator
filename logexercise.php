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
    <title class="center">Exercise Log - Logging Exercise</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <h1 class="center">Exercise Log - Log New Exercise!</h1>
    
    <hr />
    
        <p class="nav"> <a href="index.php">Home</a> &#8226; <a href="logexercise.php">Log Exercise</a> &#8226;
                    <a href="viewprofile.php">View Profile</a> &#8226; <a href="editprofile.php">Edit Profile</a>
                    &#8226; <a href="logout.php">Log Out(<?=$_SESSION['username']?>)</a></p>
            
    <hr />
    
<?php
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
  

    if (isset($_POST['submit'])) 
    {
        // Grab the profile data from the POST
        $exercise = mysqli_real_escape_string($dbc, trim($_POST['exercise']));
        $date = mysqli_real_escape_string($dbc, trim($_POST['date']));
        $time_in_minutes = mysqli_real_escape_string($dbc, trim($_POST['time_in_minutes']));
        $heartrate = mysqli_real_escape_string($dbc, trim($_POST['heartrate']));

        if (!empty($_SESSION['user_id']) && !empty($_SESSION['username'])) 
        {
            // Look up the gender, birthdate and weight in the database
            $query = "SELECT gender, birthdate, weight FROM exercise_user WHERE user_id = '" . $_SESSION['user_id'] . "'";
            $data = mysqli_query($dbc, $query)
                    or die('Error querying the database');

            if (mysqli_num_rows($data) == 1) 
            {
                // Get the data from the database and set gender, birthdate and weight to variables
                $row = mysqli_fetch_array($data);
                $gender = $row['gender'];
                $age = date_diff(date_create($row['birthdate']), date_create('now'))->y;
                $weight = $row['weight'];
            }
        }
        
        // the different calories burned for the different genders
        if ($gender == "M")
        {
            $calories_burned = ((-55.0969 + (0.6309 * $heartrate) + (0.090174 * $weight) + (0.2017 * $age)) / 4.184) * $time_in_minutes;
        }
        elseif ($gender == "F")
        {
            $calories_burned = ((-20.4022 + (0.4472 * $heartrate) - (0.057288 * $weight) + (0.074 * $age)) / 4.184) * $time_in_minutes;
        }
        
        // inserting the form data into the database
        if (!empty($exercise) && !empty($date) && !empty($time_in_minutes) && !empty($heartrate) && !empty($calories_burned))
        {
            $query = "INSERT INTO exercise_log (user_id, date, type_of_exercise, time_in_minutes, heartrate, calories_burned)" .
                    "VALUES('" . $_SESSION['user_id'] . "', '$date', '$exercise', '$time_in_minutes', '$heartrate', '$calories_burned')";
            $data2 = mysqli_query($dbc, $query)
                    or die('Error querying the database');
        }
    }
    
    // Closing the database
    mysqli_close($dbc);
?>

    <form class="form-horizontal" role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <legend>Log a New Exercise.</legend>
        <div class="form-group">   
            <label class="control-label col-sm-2" for="exercise" >Type:</label>
            <div class="col-sm-2">
                <select name="exercise">
                    <option class="text-left">Choose an Exercise</option>
                    <option <?php if($exercise =="running") echo 'selected="selected"'; ?> value="running">Running</option>
                    <option <?php if($exercise == "walking") echo 'selected="selected"'; ?> value="walking">Walking</option>
                    <option <?php if($exercise == "swimming") echo 'selected="selected"'; ?> value="swimming">Swimming</option>
                    <option <?php if($exercise == "weightlifting") echo 'selected="selected"'; ?> value="weightlifting">Weightlifting</option>
                    <option <?php if($exercise == "yoga") echo 'selected="selected"'; ?> value="yoga">Yoga</option>
                    <option <?php if($exercise == "basketball") echo 'selected="selected"'; ?> value="basketball">Basketball</option>
                </select><br />
            </div>
        </div>
        <div class="form-group">   
            <label class="control-label col-sm-2"for="date">Date:</label>
            <div class="col-sm-2">
                <input class="form-control" type="text" id="date" name="date" value="<?php if (!empty($date)) echo $date; else echo 'YYYY-MM-DD'; ?>" /><br />
            </div>
        </div>
        <div class="form-group">   
            <label class="control-label col-sm-2" for="time_in_minutes">Time(in minutes):</label>
            <div class="col-sm-2">
                <input type="text" id="time_in_minutes" name="time_in_minutes" value="<?php if (!empty($time_in_minutes)) echo $time_in_minutes; ?>" /><br />
            </div>
        </div>
        <div class="form-group">   
            <label class="control-label col-sm-2" for="heartrate">Average Heart Rate</label>
            <div class="col-sm-2">
                <input type="text" id="heartrate" name="heartrate" value="<?php if (!empty($heartrate)) echo $heartrate; ?>" /><br />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
            <input class="btn btn-default" type="submit" value="Log Exercise" name="submit" />
            </div>
        </div>
    </form>
    
<?php
    if (isset($_POST['submit'])) 
    {
        echo '<p>Your work out of ' . $exercise . ' for ' . $time_in_minutes . ' minutes with a heart rate of ' .  $heartrate . 
                ' beats per minute burned ' . round($calories_burned, 2) . ' calories!' . '</p>';
    }
?>
</body> 
</html>
