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
        require_once('../mysqli_connect.php'); //Connect to the db
    }

    $errors = array(); //Initialize an error array.

    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

    if ($id === false || $id <= 0) 
    {
        // Handle the case where the user's ID is not valid or missing
        $errors[] = 'Invalid apple ID.';
    }
        

    if(empty($errors)) //If everything is OK.
    {
        try
        {
            $query = "SELECT id  FROM apples WHERE (id=?)";
            $q = mysqli_stmt_init($dbcon);
            mysqli_stmt_prepare($q, $query);
            //use prepared statement to ensure that only text is inserted
            //bind fields to SQL Statement
            mysqli_stmt_bind_param($q, 's',$id);
            // execute query
            mysqli_stmt_execute($q);
            $apple_result = mysqli_stmt_get_result($q);
                        
            if(mysqli_num_rows($apple_result) == 1 )
            {      
                 //Delete the user if they exist in the users_register
                    // Execute the DELETE query to remove the user with the given ID
                    $deleteQuery = "DELETE FROM apples WHERE id=?";
                    $deleteStmt = mysqli_stmt_init($dbcon);
                    mysqli_stmt_prepare($deleteStmt, $deleteQuery);
                    mysqli_stmt_bind_param($deleteStmt, 's', $id);
                    mysqli_stmt_execute($deleteStmt);

                    header('Location:../apples/view-apples.php?status=1');       
                
            }
            else
            {
                //Public message:
                echo "<script>alert('Apple does not exist');</script>"; 
                                
                mysqli_close($dbcon); // Close the database connection.
                
                //exit();
            }
            
        }
        catch(Exception $e) // We finally handle any problems here
        {
           //print "An Exception occurred. Message: " . $e->getMessage();
          // echo "<script>alert('Error deleting apple." . mysqli_error($dbcon) . "');</script>";
           echo "<script>alert('The system is busy please try later');</script>";
            
        }
        catch(Error $e)
        {
            //print "An Error occurred. Message: " . $e->getMessage();            
            //echo "<script>alert('Error deleting apple." . mysqli_error($dbcon) . "');</script>";
            echo "<script>alert('The system is busy please try later');</script>";
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
        // echo "<script>alert('Error deleting user." . mysqli_error($dbcon) . "');</script>";
        echo "<script>alert('" . $internal_error . "');</script>";
    }// End of if(empty($errors)) IF.
?>