<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['user_id']))
{
    header("Location: ../index.php");
    exit();
}
?>

<?php
//This script is a query that INSERTs a record in the breeds table.
//Check that form has been submitted:
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require_once('../mysqli_connect.php'); //Connect to the db
    }

    $errors = array(); //Initialize an error array.

    $breed_nametrim = trim(htmlspecialchars($_POST['breed_name'], ENT_QUOTES));
    
    if(empty($breed_nametrim) || (strlen($breed_nametrim) > 60))
    {
        $errors[] = 'Please enter a valid breed name.';
    }

   

    $breed_id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

    if ($breed_id === false || $breed_id <= 0) 
    {
        // Handle the case where the breed's ID is not valid or missing
        $errors[] = 'Invalid breed ID.';
    }
        

    if(empty($errors)) //If everything is OK.
    {
        try
        {
            $query = "SELECT id, breed_name FROM apple_breeds WHERE (id=?)";
            $q = mysqli_stmt_init($dbcon);
            mysqli_stmt_prepare($q, $query);
            //use prepared statement to ensure that only text is inserted
            //bind fields to SQL Statement
            mysqli_stmt_bind_param($q, 'i',$breed_id);
            // execute query
            mysqli_stmt_execute($q);
            $breed_result = mysqli_stmt_get_result($q);
            $breed_data;
            
            if(mysqli_num_rows($breed_result) == 1 )
            {      
                $breed_data = mysqli_fetch_assoc($breed_result);
                $breed_id = $breed_data['id'];
                
                //Check if another breed with the name exists
                $query = "SELECT id, breed_name FROM apple_breeds WHERE (breed_name=?)";
                $q = mysqli_stmt_init($dbcon);
                mysqli_stmt_prepare($q, $query);
                //use prepared statement to ensure that only text is inserted
                //bind fields to SQL Statement
                mysqli_stmt_bind_param($q, 's',$breed_nametrim);
                // execute query
                mysqli_stmt_execute($q);
                $result = mysqli_stmt_get_result($q);

                $row = mysqli_fetch_assoc($result);
               
                if(mysqli_num_rows($result) == 0 || (mysqli_num_rows($result) == 1 && $row['id'] == $breed_id) )
                {                
                    //Make the query:
                    $query = "UPDATE apple_breeds SET breed_name=? WHERE id=?;";                
                    $q = mysqli_stmt_init($dbcon);
                    mysqli_stmt_prepare($q, $query);
                    //use prepared statement to ensure that only text is inserted
                    //bind fields to SQL Statement

                    mysqli_stmt_bind_param($q, 'si', $breed_nametrim, $breed_id );
                   
                    
                    if( !mysqli_stmt_execute($q)) //Query error
                    {

                         //Debugging message below do not use in production
                        //echo '<p>' . mysqli_error($dbcon) . '<br><br>Query: ' . $query . mysqli_stmt_affected_rows($q) .'</p>';

                        //Public message:
                        $internal_error = "The system is busy please try later.";

                        mysqli_close($dbcon); // Close the database connection.
                        //exit();
                    }    
                    else
                    {
                                                 //Public message:
                         $success = "Breed updated successfully.";                                    

                         mysqli_close($dbcon); // Close the database connection.
                    }  
                }
                else
                {
                      //Public message:
                     $internal_error = "The breed name exists.";                                    

                     mysqli_close($dbcon); // Close the database connection.
                }            
                
            }
            else
            {
                //Public message:
                $internal_error = "Breed does not exist";
                                
                                
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