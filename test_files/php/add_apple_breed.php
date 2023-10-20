<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id']))
{
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin | Add Apple Breed</title>
        
        <link rel="icon" href="../images/apple.jpg" type="image/jpg">
        
        <?php
           $path = __DIR__;
           require_once("../includes/external_file_links.php");
        ?>
                
    </head>
    <body style="background-color:rgb(26, 255, 26)"> 
            
     <!-- Navigation -->
     <div class="container-fluid sticky-top" style="background-color:rgb(4,38,84);">

     <h5 class="text-center text-light d-md-none">Apple Mapper</h5>

       <nav class="navbar navbar-expand-md text-light " role="navigation" id="main_navbar">
           <h5 class="text-center text-light d-none d-md-block">Apple Mapper</h5>

                <button class="navbar-toggler" type="button" style="color:rgb(236,132,17)" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <span class="navbar-toggler-icon">
                        <i class="fa fa-bars" style="color:#fff; font-size:28px;"></i>
                    </span>
               </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">                                                  
            <li class="nav-item">
                <a class="nav-link" style="color:rgb(236,132,17);" href="./admin_dashboard.php">Dashboard</a>
            </li>                            
            <li class="nav-item">
                <a class="nav-link" style="color:rgb(236,132,17);" href="../logout.php">Logout</a>
            </li>                        
                </ul>
            </div>
        </nav>
     </div>
    
        <!--Validate Input-->
        <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            require('process_add_apple_breed.php');
        } // End of the main Submit conditional.
        ?>
    
        <div class="container" style="background-color:rgb(4,38,84);border-radius:25px; margin-top:80px; margin-bottom:80px; max-width:400px;">
            <div class="d-flex justify-content-center h-100">
                <div>                                
                    <div class="d-flex justify-content-center" style="margin-top:10px;margin-bottom:10px;">
                        <img  class="rounded-circle" src="../images/leafy.jpg" width="170px" height="150px" style="background-color:rgb(255,255,255);" alt="Logo">
                    </div>
                    <div class="d-flex justify-content-center" style="margin-top:10px;margin-bottom:10px;">
                        <h2 style="color:rgb(236,132,17)">Add Breed</h2>
                    </div>
                    <div class="d-flex justify-content-center">
                        <form action="add_apple_breed.php" method="post" name="add_user_form" id="add_user_form" style="display: inline-block;">                        
                            
                            <div class="row input-group mb-3">
                                <div class="col-lg-4 input-group-append">
                                    <span class="input-group-text" style="color:rgb(236,132,17);background-color:rgb(4,38,84); border:none;margin-right:10px;margin-bottom:5px;">Breed Name:</span>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="breed_name" name="breed_name" placeholder="Enter Breed"
                                            maxlength="60" required 
                                            value="<?php if(isset($_POST['breed_name'])) echo htmlspecialchars($_POST['breed_name'], ENT_QUOTES); ?>">
                                </div>

                            </div>

                            <div class="text-center" style="color:red;">
                                <?php if(isset($invalid_breed_name)) echo $invalid_breed_name;
                                        $invalid_breed_name = "";    
                                ?>                               
                            </div>
                          
                            
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

                            <div class="d-flex justify-content-center mt-3" style="margin-bottom:40px"> 
                                <input id="submit" class="btn btn-primary rounded-pill" type="submit" name="submit" value="Add Breed" style="background-color:rgb(236,132,17);margin-bottom:5px;">            
                            </div>                   
                            
                            
                                    
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
       
    </body>
</html>