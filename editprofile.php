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
    <title>Exercise Log - Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <h1 class="center">Exercise Log - Edit Profile</h1>
    
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
        echo '<p>Please <a href="login.php">log in</a> to access this page.</p>';
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
        $first_name = mysqli_real_escape_string($dbc, trim($_POST['firstname']));
        $last_name = mysqli_real_escape_string($dbc, trim($_POST['lastname']));
        $gender = mysqli_real_escape_string($dbc, trim($_POST['gender']));
        $birthdate = mysqli_real_escape_string($dbc, trim($_POST['birthdate']));
        $weight = mysqli_real_escape_string($dbc, trim($_POST['weight']));
        $old_picture = mysqli_real_escape_string($dbc, trim($_POST['old_picture']));
        $new_picture = mysqli_real_escape_string($dbc, trim($_FILES['new_picture']['name']));
        $new_picture_type = $_FILES['new_picture']['type'];
        $new_picture_size = $_FILES['new_picture']['size']; 
        //list($new_picture_width, $new_picture_height) = getimagesize($_FILES['new_picture']['tmp_name']);
        $error = false;
    
        // Validate and move the uploaded picture file, if necessary
        if (!empty($new_picture)) 
        {
            if ((($new_picture_type == 'image/gif') || ($new_picture_type == 'image/jpeg') || ($new_picture_type == 'image/pjpeg') ||
                ($new_picture_type == 'image/png')) && ($new_picture_size > 0) && ($new_picture_size <= MM_MAXFILESIZE) &&
                ($new_picture_width <= MM_MAXIMGWIDTH) && ($new_picture_height <= MM_MAXIMGHEIGHT)) 
            {
                list($new_picture_width, $new_picture_height) = getimagesize($_FILES['new_picture']['tmp_name']);

                if ($_FILES['file']['error'] == 0) 
                {
                    // Move the file to the target upload folder
                    $target = MM_UPLOADPATH . basename($new_picture);
                    if (move_uploaded_file($_FILES['new_picture']['tmp_name'], $target)) 
                    {
                        // The new picture file move was successful, now make sure any old picture is deleted
                        if (!empty($old_picture) && ($old_picture != $new_picture)) 
                        {
                            @unlink(MM_UPLOADPATH . $old_picture);
                        }
                    }
                    else 
                    {
                        // The new picture file move failed, so delete the temporary file and set the error flag
                        @unlink($_FILES['new_picture']['tmp_name']);
                        $error = true;
                        echo '<p class="error">Sorry, there was a problem uploading your picture.</p>';
                    }
                }
            }
            else 
            {
                // The new picture file is not valid, so delete the temporary file and set the error flag
                @unlink($_FILES['new_picture']['tmp_name']);
                $error = true;
                echo '<p class="error">Your picture must be a GIF, JPEG, or PNG image file no greater than ' . (MM_MAXFILESIZE / 1024) .
                        ' KB and ' . MM_MAXIMGWIDTH . 'x' . MM_MAXIMGHEIGHT . ' pixels in size.</p>';
            }
        }

            // Update the profile data in the database
            if (!$error) 
            {
                if (!empty($first_name) && !empty($last_name) && !empty($gender) && !empty($birthdate) && !empty($weight)) 
                {
                    // Only set the picture column if there is a new picture
                    if (!empty($new_picture)) 
                    {
                    $query = "UPDATE exercise_user SET first_name = '$first_name', last_name = '$last_name', gender = '$gender', " .
                            " birthdate = '$birthdate', weight = '$weight', picture = '$new_picture' WHERE user_id = '" . 
                            $_SESSION['user_id'] . "'";
                    }
                    else 
                    {
                        $query = "UPDATE exercise_user SET first_name = '$first_name', last_name = '$last_name', gender = '$gender', " .
                                " birthdate = '$birthdate', weight = '$weight' WHERE user_id = '" . $_SESSION['user_id'] . "'";
                    }
                    mysqli_query($dbc, $query)
                            or die('Error querying the database 1');

                    // Confirm success with the user
                    echo '<p>Your profile has been successfully updated. Would you like to <a href="viewprofile.php">view your profile</a>?</p>';

                    mysqli_close    ($dbc);
                    exit();
                }
                else 
                {
                    echo '<p class="error">You must enter all of the profile data (the picture is optional).</p>';
                }
            }
    } // End of check for form submission
    else 
    {
        // Grab the profile data from the database
        $query = "SELECT first_name, last_name, gender, birthdate, weight, picture FROM exercise_user WHERE user_id = '" . $_SESSION['user_id'] . "'";
        $data = mysqli_query($dbc, $query)
                or die ('Error querying the database 2');
        $row = mysqli_fetch_array($data);

        if ($row != NULL) 
        {
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $gender = $row['gender'];
            $birthdate = $row['birthdate'];
            $weight = $row['weight'];
            $old_picture = $row['picture'];
        }
        else 
        {
        echo '<p class="error">There was a problem accessing your profile.</p>';
        }
    }

    mysqli_close($dbc);
?>

    <form class="form-horizontal" role="form" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Personal Information</legend>
            <div class="form-group">   
                <label class="control-label col-sm-2" for="firstname">First name:</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" id="firstname" name="firstname" value="<?php if (!empty($first_name)) echo $first_name; ?>" /><br />
                </div>
            </div>
            <div class="form-group">   
                <label class="control-label col-sm-2" for="lastname">Last name:</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" id="lastname" name="lastname" value="<?php if (!empty($last_name)) echo $last_name; ?>" /><br />
                </div>
            </div>
            <div class="form-group">   
                <label class="control-label col-sm-2" for="gender">Gender:</label>
                <div class="col-sm-2">
                    <select id="gender" name="gender">
                        <option value="M" <?php if (!empty($gender) && $gender == 'M') echo 'selected = "selected"'; ?>>Male</option>
                        <option value="F" <?php if (!empty($gender) && $gender == 'F') echo 'selected = "selected"'; ?>>Female</option>
                    </select><br />
                </div>
            </div>
            <div class="form-group">   
                <label class="control-label col-sm-2" for="birthdate">Birthdate:</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" id="birthdate" name="birthdate" value="<?php if (!empty($birthdate)) echo $birthdate; else echo 'YYYY-MM-DD'; ?>" /><br />
                </div>
            </div>
            <div class="form-group">   
                <label class="control-label col-sm-2" for="weight">Weight:</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" id="weight" name="weight" value="<?php if (!empty($weight)) echo $weight; ?>" /><br />
                </div>
            </div>
            <input type="hidden" name="old_picture" value="<?php if (!empty($old_picture)) echo $old_picture; ?>" />
            <div class="form-group">   
                <label class="control-label col-sm-2" for="new_picture">Picture:</label>
                    <span class="btn btn-file">
                        <input type="file" id="new_picture" name="new_picture" /><br />
                        <?php 
                            if (!empty($old_picture)) 
                            {
                                echo '<img class="profile" src="' . MM_UPLOADPATH . $old_picture . '" alt="Profile Picture" />';
                            }
                        ?>
                        </span>
            </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input class="btn btn-default" type="submit" value="Save Profile" name="submit" />
            </div>
        </div>
    </form>
</body> 
</html>
