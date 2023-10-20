<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id']))
{
    header("Location: ../index.php");
    exit();
}
?>

<?php
require_once('../mysqli_connect.php');

// Initialize variables
$breed_id = null;
$breed_name = '';

// Check if 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    $breed_id = $_GET['id'];

    // Retrieve the breed's information from the database
    $sql = "SELECT * FROM apple_breeds WHERE id = ?";
    $stmt = mysqli_prepare($dbcon, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $breed_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if breed with the specified id exists
    if ($row = mysqli_fetch_assoc($result)) {
        $breed_name = $row['breed_name'];
    } 
    else
    {
        // breed does not exist, display an alert and exit
        echo "<script>alert('Breed with ID $breed_id does not exist.')</script>";
        exit; // Exit the script
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit Apple Breed</title>
    <link rel="icon" href="../images/apple.jpg" type="image/jpg">
    <?php
    $path = __DIR__;
    require_once("../includes/external_file_links.php");
    ?>
</head>
<body style="background-color: rgb(26, 255, 26)">

<!-- Navigation -->
<div class="container-fluid sticky-top" style="background-color: rgb(4, 38, 84);">
    <h5 class="text-center text-light d-md-none">Apple Mapper</h5>
    
    <nav class="navbar navbar-expand-md text-light " role="navigation" id="main_navbar">
        <h5 class="text-center text-light d-none d-md-block">Apple Mapper</h5>
        <button class="navbar-toggler" type="button" style="color: rgb(236, 132, 17)" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
            Menu
            <span class="navbar-toggler-icon">
                <i class="fa fa-bars" style="color: #fff; font-size: 28px;"></i>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" style="color: rgb(236, 132, 17);" href="./admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: rgb(236, 132, 17);" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</div>

 <!--Validate Input-->
 <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require('process_edit_apple_breed.php');
    } // End of the main Submit conditional.
 ?>

<!-- Form -->
<div class="container"
     style="background-color: rgb(4, 38, 84); border-radius: 25px; margin-top: 80px; margin-bottom: 80px; max-width: 400px;">
    <div class="d-flex justify-content-center h-100">
        <div>
            <div class="d-flex justify-content-center"
                 style="margin-top: 10px; margin-bottom: 10px;">
                <img class="rounded-circle" src="../images/leafy.jpg" width="170px" height="150px"
                     style="background-color: rgb(255, 255, 255);" alt="Logo">
            </div>
            <div class="d-flex justify-content-center"
                 style="margin-top: 10px; margin-bottom: 10px;">
                <h2 style="color: rgb(236, 132, 17)">Edit Breed</h2>
            </div>
            <div class="d-flex justify-content-center">
                <form action="edit_apple_breed.php?id=<?php echo htmlspecialchars($breed_id, ENT_QUOTES) ?>" method="post"
                      name="breed_form" id="breed_form" style="display: inline-block;">
                    <!-- Add or Edit breed Fields -->
                    <div class="row input-group mb-3">
                        <div class="col-lg-4 input-group-append">
                            <span class="input-group-text"
                                  style="color: rgb(236, 132, 17); background-color: rgb(4, 38, 84); border: none; margin-right: 10px; margin-bottom: 5px;">Breed Name:</span>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" id="breed_name" name="breed_name"
                                   placeholder="Enter Breed Name" maxlength="60" required
                                   value="<?php if (isset($_POST['breed_name'])) echo htmlspecialchars($_POST['breed_name'], ENT_QUOTES); else echo htmlspecialchars($breed_name, ENT_QUOTES); ?>">
                        </div>
                    </div>
                    
                    <!-- End of Add or Edit breed Fields -->

                    <div class="text-center" style="color: red;">
                        <?php if (isset($invalid_breed_name)) echo $invalid_breed_name;
                        $invalid_breed_name = ""; ?>
                    </div>


                    <div class="text-center" style="color: red;">
                        <?php
                        if (isset($internal_error)) {
                            echo $internal_error;
                            $internal_error = "";
                        }
                        ?>
                    </div>

                    <div class="text-center" style="color: cyan;">
                        <?php
                        if (isset($success)) {
                            echo $success;
                            $success = "";
                        }
                        ?>
                    </div>

                    <div class="d-flex justify-content-center mt-3" style="margin-bottom: 40px">
                        <input id="submit" class="btn btn-primary rounded-pill" type="submit" name="submit"
                               value="Save Changes"
                               style="background-color: rgb(236, 132, 17); margin-bottom: 5px;">
                    </div>

                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($breed_id, ENT_QUOTES); ?>">


                </form>
            </div>
        </div>
    </div>

</div>

</body>
</html>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id']))
{
    header("Location: ../index.php");
    exit();
}
?>

<?php
require_once('../mysqli_connect.php');

// Initialize variables
$id = null;


// Check if 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the user's information from the database
    $sql = "SELECT * FROM apples WHERE id = ?";
    $stmt = mysqli_prepare($dbcon, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if user with the specified id exists
    if ($result_row = mysqli_fetch_assoc($result)) {
        $apple_id = $result_row['apple_id'];
        $yop = $result_row['yop'];
        $breed_id = $result_row['breed_id'];
        $row = $result_row['row'];
        $column = $result_row['col'];
        $geolocation = $result_row['latitude'] . ', ' . $result_row['longitude'];

    } 
    else
    {
        // User does not exist, display an alert and exit
        echo "<script>alert('Apple with ID $id does not exist.')</script>";
        exit(); // Exit the script
    }

    $query = "SELECT * FROM apple_breeds";
     $q = mysqli_stmt_init($dbcon);
     mysqli_stmt_prepare($q, $query);
     
     // execute query
     mysqli_stmt_execute($q);
     $result = mysqli_stmt_get_result($q);
         
     if (mysqli_num_rows($result) >= 1) {
         while ($breeds_result_row = mysqli_fetch_assoc($result)) {
             // Store the breed ID as the key and the breed name as the value in the array
             $apple_breeds[$breeds_result_row['id']] = $breeds_result_row['breed_name'];
         }
     }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit Apple</title>
    <link rel="icon" href="../images/apple.jpg" type="image/jpg">
    <?php
    $path = __DIR__;
    require_once("../includes/external_file_links.php");
    ?>
</head>
<body style="background-color: rgb(26, 255, 26)">

<!-- Navigation -->
<div class="container-fluid sticky-top" style="background-color: rgb(4, 38, 84);">
    <h5 class="text-center text-light d-md-none">Apple Mapper</h5>
    
    <nav class="navbar navbar-expand-md text-light " role="navigation" id="main_navbar">
        <h5 class="text-center text-light d-none d-md-block">Apple Mapper</h5>
        <button class="navbar-toggler" type="button" style="color: rgb(236, 132, 17)" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
            Menu
            <span class="navbar-toggler-icon">
                <i class="fa fa-bars" style="color: #fff; font-size: 28px;"></i>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" style="color: rgb(236, 132, 17);" href="./admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: rgb(236, 132, 17);" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</div>

 <!--Validate Input-->
 <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require('process_edit_apple.php');
    } // End of the main Submit conditional.
 ?>

<!-- Form -->
<div class="container"
     style="background-color: rgb(4, 38, 84); border-radius: 25px; margin-top: 80px; margin-bottom: 80px; max-width: 400px;">
    <div class="d-flex justify-content-center h-100">
        <div>
            <div class="d-flex justify-content-center"
                 style="margin-top: 10px; margin-bottom: 10px;">
                <img class="rounded-circle" src="../images/leafy.jpg" width="170px" height="150px"
                     style="background-color: rgb(255, 255, 255);" alt="Logo">
            </div>
            <div class="d-flex justify-content-center"
                 style="margin-top: 10px; margin-bottom: 10px;">
                <h2 style="color: rgb(236, 132, 17)">Edit Apple</h2>
            </div>
            <div class="d-flex justify-content-center">
            <form action="edit_apple.php?id=<?php echo htmlspecialchars($id, ENT_QUOTES) ?>" method="post" name="appleform" id="appleform" style="display: inline-block;">                        
                
                <div class="text-center" style="color:red;">
                    <?php 
                            if(isset($internal_error)) 
                           {
                               echo $internal_error;
                               $internal_error = "";
                           }
                                
                    ?>                               
                </div>

                <div class="text-center" style="color:cyan;">
                    <?php 
                            if(isset($success)) 
                            {            
                                echo  $success;
                                $success = "";
                            }
                                
                    ?>                               
                </div>
                
                <div class="text-center mb-3" style="color:red;">
                    <?php 
                            if(isset($apple_error)) 
                           {
                               echo $apple_error;
                               $apple_error = "";
                           }
                                
                    ?>                               
                </div>

                <div class="row input-group mb-3">
                    <div class="col-lg-4 input-group-append">
                        <span class="input-group-text" style="color:rgb(236,132,17);background-color:rgb(4,38,84); border:none;margin-right:10px;margin-bottom:5px;">Apple ID:</span>
                    </div>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" id="apple_id" name="apple_id" placeholder="Enter Apple ID"
                                maxlength="30" required 
                                value="<?php if(isset($_POST['apple_id'])) echo htmlspecialchars($_POST['apple_id'], ENT_QUOTES); else echo htmlspecialchars($apple_id, ENT_QUOTES) ?>">
                    </div>

                </div>

                                          

                <div class="row input-group mb-3">
                    <div class="col-lg-5 input-group-append">
                        <span class="input-group-text" style="color:rgb(236,132,17);background-color:rgb(4,38,84); border:none;margin-right:10px;margin-bottom:5px;">Year of planting:</span>
                    </div>
                    <div class="col-lg-7">
                        <input type="date" class="form-control" id="yop" name="yop" placeholder="YYYY-MM-DD"
                            required
                            value="<?php if(isset($_POST['yop'])) echo htmlspecialchars($_POST['yop'], ENT_QUOTES); else echo htmlspecialchars($yop, ENT_QUOTES) ?>">
                    </div>
                </div>


                <div class="row input-group mb-3">
                    <div class="col-lg-4 input-group-append">
                        <span class="input-group-text" style="color:rgb(236,132,17);background-color:rgb(4,38,84); border:none;margin-right:10px;margin-bottom:5px;">Breed:</span>
                    </div>
                    <div class="col-lg-8">
                        <select class="form-select" id="breed" name="breed" required>
                            <option value="">Select Breed</option>
                            <?php
                            foreach ($apple_breeds as $breedId => $breedName) {
                                $selected = ((isset($_POST['breed']) && $_POST['breed'] == $breedId) || $breed_id == $breedId ) ? 'selected' : $breed_id;
                                echo "<option value=\"$breedId\" $selected>$breedName</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div> 
                
                <div class="row input-group mb-3">

                    <div class="col-lg-4 input-group-append">
                        <span class="input-group-text" style="color:rgb(236,132,17);background-color:rgb(4,38,84); border:none;margin-right:10px;margin-bottom:5px;">Row:</span>
                    </div>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" id="row" name="row" placeholder="Enter row"
                                    maxlength="20" required
                                    value="<?php if(isset($_POST['row'])) echo htmlspecialchars($_POST['row'], ENT_QUOTES); else echo htmlspecialchars($row, ENT_QUOTES) ?>">
                                    
                    </div>                 

                </div> 
                
                 <div class="row input-group mb-3">

                    <div class="col-lg-4 input-group-append">
                        <span class="input-group-text" style="color:rgb(236,132,17);background-color:rgb(4,38,84); border:none;margin-right:10px;margin-bottom:5px;">Column:</span>
                    </div>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" id="column" name="column" placeholder="Enter column"
                                    maxlength="20" required
                                    value="<?php if(isset($_POST['column'])) echo htmlspecialchars($_POST['column'], ENT_QUOTES); else echo htmlspecialchars($column, ENT_QUOTES) ?>">
                                    
                    </div>                 

                </div> 

                 <div class="row input-group mb-3">

                    <div class="col-lg-4 input-group-append">
                        <span class="input-group-text" style="color:rgb(236,132,17);background-color:rgb(4,38,84); border:none;margin-right:10px;margin-bottom:5px;">Geo-location:</span>
                    </div>
                    <div class="col-lg-8">
                        <span id="geolocation_data" style="color:white;"></span>
                        <input type="hidden" class="form-control" id="geolocation" name="geolocation" placeholder="longitude, latitude"
                                   required
                                    value="<?php if(isset($_POST['geolocation'])) echo htmlspecialchars($_POST['geolocation'], ENT_QUOTES); else echo htmlspecialchars($geolocation, ENT_QUOTES) ?>">
                    </div>                                               

                </div> 
                
                <div class="d-flex justify-content-center mt-3" style="margin-bottom:40px"> 
                    <button class="btn btn-primary rounded-pill" type="button" style="width:200px;" onclick="getLocation()">
                        Update Location
                    </button> 
                </div>                                                                                                              

                <div class="d-flex justify-content-center mt-3" style="margin-bottom:40px"> 
                    <input id="submit" class="btn btn-primary rounded-pill" style="width:200px;" type="submit" name="submit" value="Save Changes" style="background-color:rgb(236,132,17);margin-bottom:5px;">            
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>">
                </div>            
                    
            </form>

            </div>
        </div>
    </div>

</div>

<script>
    var loc = document.getElementById('geolocation_data');
    var geolocation_details = "";
    function getLocation()
    {
        if(navigator.geolocation)
            navigator.geolocation.getCurrentPosition(showPosition);
        else
          {
            loc.innerHTML = "Geolocation is not supported by this browser.";
          }
    }
    function showPosition(position)
    {
        loc.innerHTML = "Latitude: " + position.coords.latitude.toFixed(8) + "<br>Longitude: " + position.coords.longitude.toFixed(8);
        geolocation_details = position.coords.latitude + ',' + position.coords.longitude;
        var geolocation_input_field = document.getElementById('geolocation');
        geolocation_input_field.value = geolocation_details;
    }
    
    getLocation();
    
</script>

</body>
</html>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id']))
{
    header("Location: ../index.php");
    exit();
}
?>

<?php
require_once('../mysqli_connect.php');

// Initialize variables
$user_id = null;
$name = '';
$email = '';

// Check if 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Retrieve the user's information from the database
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($dbcon, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if user with the specified id exists
    if ($row = mysqli_fetch_assoc($result)) {
        $name = $row['name'];
        $email = $row['email'];
    } 
    else
    {
        // User does not exist, display an alert and exit
        echo "<script>alert('User with ID $user_id does not exist.')</script>";
        exit; // Exit the script
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit User</title>
    <link rel="icon" href="../images/apple.jpg" type="image/jpg">
    <?php
    $path = __DIR__;
    require_once("../includes/external_file_links.php");
    ?>
</head>
<body style="background-color: rgb(26, 255, 26)">

<!-- Navigation -->
<div class="container-fluid sticky-top" style="background-color: rgb(4, 38, 84);">
    <h5 class="text-center text-light d-md-none">Apple Mapper</h5>
    
    <nav class="navbar navbar-expand-md text-light " role="navigation" id="main_navbar">
        <h5 class="text-center text-light d-none d-md-block">Apple Mapper</h5>
        <button class="navbar-toggler" type="button" style="color: rgb(236, 132, 17)" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
            Menu
            <span class="navbar-toggler-icon">
                <i class="fa fa-bars" style="color: #fff; font-size: 28px;"></i>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" style="color: rgb(236, 132, 17);" href="./admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: rgb(236, 132, 17);" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</div>

 <!--Validate Input-->
 <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require('process_edit_user.php');
    } // End of the main Submit conditional.
 ?>

<!-- Form -->
<div class="container"
     style="background-color: rgb(4, 38, 84); border-radius: 25px; margin-top: 80px; margin-bottom: 80px; max-width: 400px;">
    <div class="d-flex justify-content-center h-100">
        <div>
            <div class="d-flex justify-content-center"
                 style="margin-top: 10px; margin-bottom: 10px;">
                <img class="rounded-circle" src="../images/leafy.jpg" width="170px" height="150px"
                     style="background-color: rgb(255, 255, 255);" alt="Logo">
            </div>
            <div class="d-flex justify-content-center"
                 style="margin-top: 10px; margin-bottom: 10px;">
                <h2 style="color: rgb(236, 132, 17)">Edit User</h2>
            </div>
            <div class="d-flex justify-content-center">
                <form action="edit_user.php" method="post"
                      name="user_form" id="user_form" style="display: inline-block;">
                    <!-- Add or Edit User Fields -->
                    <div class="row input-group mb-3">
                        <div class="col-lg-4 input-group-append">
                            <span class="input-group-text"
                                  style="color: rgb(236, 132, 17); background-color: rgb(4, 38, 84); border: none; margin-right: 10px; margin-bottom: 5px;">Name:</span>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Enter Name" maxlength="60" required
                                   value="<?php if (isset($_POST['name'])) echo htmlspecialchars($_POST['name'], ENT_QUOTES); else echo htmlspecialchars($name, ENT_QUOTES); ?>">
                        </div>
                    </div>
                    <div class="row input-group mb-3">
                        <div class="col-lg-4 input-group-append">
                            <span class="input-group-text"
                                  style="color: rgb(236, 132, 17); background-color: rgb(4, 38, 84); border: none; margin-right: 10px; margin-bottom: 5px;">Email:</span>
                        </div>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" id="email" name="email"
                                   placeholder="Enter Email" maxlength="60" required
                                   value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email'], ENT_QUOTES); else echo htmlspecialchars($email, ENT_QUOTES); ?>">
                        </div>
                    </div>
                    <!-- End of Add or Edit User Fields -->

                    <div class="text-center" style="color: red;">
                        <?php if (isset($invalid_name)) echo $invalid_name;
                        $invalid_name = ""; ?>
                    </div>

                    <div class="text-center" style="color: red;">
                        <?php if (isset($invalid_email)) echo $invalid_email;
                        $invalid_email = ""; ?>
                    </div>

                    <div class="text-center" style="color: red;">
                        <?php
                        if (isset($internal_error)) {
                            echo $internal_error;
                            $internal_error = "";
                        }
                        ?>
                    </div>

                    <div class="text-center" style="color: cyan;">
                        <?php
                        if (isset($success)) {
                            echo $success;
                            $success = "";
                        }
                        ?>
                    </div>

                    <div class="d-flex justify-content-center mt-3" style="margin-bottom: 40px">
                        <input id="submit" class="btn btn-primary rounded-pill" type="submit" name="submit"
                               value="Save Changes"
                               style="background-color: rgb(236, 132, 17); margin-bottom: 5px;">
                    </div>

                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_id, ENT_QUOTES); ?>">


                </form>
            </div>
        </div>
    </div>

</div>

</body>
</html>
<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id']))
{
    header("Location: ../index.php");
    exit();
}
?>

<?php
//This script is a query that INSERTs a record in the users table.
//Check that form has been submitted:
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require('../mysqli_connect.php'); //Connect to the db
    }

    $errors = array(); //Initialize an error array.

    $breed_nametrim = trim(htmlspecialchars($_POST['breed_name'], ENT_QUOTES));
    
    if(empty($breed_nametrim) || (strlen($breed_nametrim) > 60))
    {
        $errors[] = 'Please enter a valid breed_name.';
    }


    if(empty($errors)) //If everything is OK.
    {
        try
        {
            $query = "SELECT id FROM apple_breeds WHERE (breed_name=?)";
            $q = mysqli_stmt_init($dbcon);
            mysqli_stmt_prepare($q, $query);
            //use prepared statement to ensure that only text is inserted
            //bind fields to SQL Statement
            mysqli_stmt_bind_param($q, 's',$breed_nametrim);
            // execute query
            mysqli_stmt_execute($q);
            $result = mysqli_stmt_get_result($q);
            
            if(mysqli_num_rows($result) == 0 )
            {       

            
               //Make the query:
               $query = "INSERT INTO apple_breeds (id, breed_name)";                
               $query .= "VALUES(' ', ?)";
               $q = mysqli_stmt_init($dbcon);
               mysqli_stmt_prepare($q, $query);
               //use prepared statement to ensure that only text is inserted
               //bind fields to SQL Statement
               
               mysqli_stmt_bind_param($q, 's', $breed_nametrim);
               // execute query
               mysqli_stmt_execute($q);

               if(mysqli_stmt_affected_rows($q) == 0) //No record inserted
               {

                    //Debugging message below do not use in production
                   //echo '<p>' . mysqli_error($dbcon) . '<br><br>Query: ' . $query . '</p>';

                   //Public message:
                   $internal_error = "The system is busy please try later.";

                   mysqli_close($dbcon); // Close the database connection.
                   //exit();
               }    
               else
               {
                     //Public message:
                    $success = "Breed registered successfully.";                                    

                    mysqli_close($dbcon); // Close the database connection.
               }           
                
            }
            else
            {
                //Public message:
                $success = "Breed already registered.";
                                
                                
                mysqli_close($dbcon); // Close the database connection.
                
                //exit();
            }
            
        }
        catch(Exception $e) // We finally handle any problems here
        {
            //print "An Exception occurred. Message: " . $e->getMessage();
            $internal_error = "The system is busy please try later";
            
        }
        catch(Error $e)
        {
            //print "An Error occurred. Message: " . $e->getMessage();            
            $internal_error = "The system is busy please try later";
        }
    }
    else //Report the  errors
    {
        $errorstring = "Error! The following error(s) occured:<br>";
        foreach($errors as $msg) //Print each error
        {
            $errorstring .= " - $msg<br>\n";
        }
        $errorstring .= "Please try again.<br>";
        $internal_error = $errorstring . "</p>";
    }// End of if(empty($errors)) IF.
?>
