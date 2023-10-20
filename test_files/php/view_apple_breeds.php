<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id']))
{
    header("Location: ../index.php");
    exit();
}
?>

<?php
    if(isset($_GET['status']))
    {
        if($_GET['status'] == 1)
        {
            echo "<script>alert('Apple breed deleted successfully');</script>";
        }
        else
        {
            echo "<script>alert('An error occurred.');</script>";
        }
        
    }
?>

<?php
    require('../mysqli_connect.php');

    // Define the SQL query to count total records
    $count_query = "SELECT COUNT(*) AS total_records FROM apple_breeds";
    $count_result = mysqli_query($dbcon, $count_query);
    $total_records = mysqli_fetch_assoc($count_result)['total_records'];

    // Pagination settings
    $records_per_page = 10; // Number of records per page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
    
    // Calculate the OFFSET for the SQL query
    $offset = ($page - 1) * $records_per_page;
    
    // Define the SQL query with LIMIT and OFFSET using prepared statements
    $sql = "SELECT * FROM apple_breeds LIMIT ?, ?";
    $stmt = mysqli_prepare($dbcon, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $offset, $records_per_page);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
        
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin | View Apple Breeds</title>
        
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
    
        <div class="container" style="margin-top:80px; margin-bottom:80px;">
        <div class="d-flex justify-content-center table-responsive">
            <table class="table table-bordered table-striped table-hover text-center table-primary">
                <thead class="thead-dark">
                    <tr>
                        <th>Breed Name</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through the user data and display it in the table rows.
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['breed_name'] . "</td>";
                        echo "<td>";
                        echo "<a href='edit_apple_breed.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary'>Edit</a>";
                        echo "</td>";
                        echo "<td>";
                        echo "<a href='delete_apple_breed.php?id=" . $row['id'] . "&breed_name=" . $row['breed_name'] . "' class='btn btn-sm btn-danger'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

                    
        <!-- Pagination links -->
        <div class="d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php
                     // Calculate the total number of pages
                     $total_pages = ceil($total_records / $records_per_page);  

                    // Create "Previous" link
                    if ($page > 1) {
                        echo "<li class='page-item'>";
                        echo "<a class='page-link' style='color:black;' href='view_apple_breeds.php?page=" . ($page - 1) . "'>&laquo; Previous</a>";
                        echo "</li>";
                    }

                    // Create pagination links
                    for ($i = 1; $i <= $total_pages; $i++) 
                    {
                        echo "<li class='page-item" . ($page == $i ? " active" : "") . "'>";
                        echo "<a class='page-link' href='view_apple_breeds.php?page=$i'>$i</a>";
                        echo "</li>";
                    }

                    // Create "Next" link
                    if ($page < $total_pages) {
                        echo "<li class='page-item'>";
                        echo "<a class='page-link' style='color:black;' href='view_apple_breeds.php?page=" . ($page + 1) . "'>Next &raquo;</a>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </nav>
        </div>
            
        </div>
       
    </body>
</html>
