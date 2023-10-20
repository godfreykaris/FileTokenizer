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
