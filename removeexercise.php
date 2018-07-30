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
    <title>Exercise Log - Remove an Exercise</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <h1 class="center">Exercise Log - Remove an Exercise</h1>
  
<?php
    // linking to shared constants
    require_once('connectvars.php');
    
    if (isset($_GET['exercise_id'])) 
    {
        // Grab the exercise_id data from the GET
        $id = $_GET['exercise_id'];
        $date = $_GET['date'];
        $type_of_exercise = $_GET['type_of_exercise'];
    }
    elseif (isset($_POST['exercise_id'])) 
    {
        // Grab the exercise_id from the POST
        $id = $_POST['exercise_id'];
        $date = $_POST['date'];
        $type_of_exercise = $_POST['type_of_exercise'];
    }
    else 
    {
        echo '<p class="error">Sorry, no score was specified for removal.</p>';
    }
    
    if (isset($_POST['submit'])) 
    {
        // Confirm, ok to delete
        if ($_POST['confirm'] == 'Yes') 
        {
            // Connect to the database
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to database');

            // Delete the score data from the database
            $query = "DELETE FROM exercise_log WHERE exercise_id = $id";

            mysqli_query($dbc, $query)
                    or die('Error deleting data from database');
                    
            mysqli_close($dbc);
            
            // Confirm success with the user
            echo '<p class="error">The exercise has been removed.</p>';
        }
        else 
        {
            echo '<p class"error"> the exercise was not removed';
        }
    }
    elseif (isset($id) && isset($type_of_exercise) && isset($date)) {
        echo '<p>Are you sure you want to delete the following exercise?<p>';
        echo '<p><strong>Type:' . $type_of_exercise . '<br /><strong>From the Date of:' . $date . '<p>';
        echo '<form method="post" action="removeexercise.php">';
        echo '<input type="radio" name="confirm" value="Yes" />Yes';
        echo '<input type="radio" name="confirm" value="No" checked="checked" />No<br />';
        echo '<input type="submit" value="Submit" name="submit" />';
        echo '<input type="hidden" name="exercise_id" value="' . $id . '" />';
        echo '<input type="hidden" name="date" value="' . $date . '" />';
        echo '<input type="hidden" name="type_of_exercise" value="' . $type_of_exercise . '" />';
        echo '</form>';
    }
    
    echo '<p><a href="viewprofile.php">&lt;&lt; Back to View Profile page</a></p>';
    
?>
    
</body>
</html>
    