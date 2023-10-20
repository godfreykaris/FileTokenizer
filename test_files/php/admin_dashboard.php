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
        <title>Admin | Apple Farm</title>
        
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
                            <a class="nav-link" style="color:rgb(236,132,17);" href="../apples/view-apples.php">View Apples</a>
                        </li>                            
                        <li class="nav-item">
                            <a class="nav-link" style="color:rgb(236,132,17);" href="../logout.php">Logout</a>
                        </li>                        
                    </ul>
                </div>
            </nav>
        </div>
    
    
        <div class="container" style="margin-top: 80px; margin-bottom: 80px; max-width: 400px;">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text">Add, Edit or Delete Users</p>
                    
                    <a href="add_user.php" class="btn btn-primary">Add User</a>
                    <a href="view_users.php" class="btn btn-primary">View Users</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Apples</h5>
                    <p class="card-text">Edit or Delete Apples in View Apples</p>
                    
                    <a href="../apples/view-apples.php" class="btn btn-primary">View Apples</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Apple Breeds</h5>
                    <p class="card-text">Add, Edit or Delete Apple breeds</p>

                    <a href="add_apple_breed.php" class="btn btn-primary">Add Apple Breed</a>
                    <a href="view_apple_breeds.php" class="btn btn-primary">View Apple Breeds</a>
                </div>
            </div>

        </div>

     

       
    </body>
</html>