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

    
    // apple id
    $apple_idtrim = trim(htmlspecialchars($_POST['apple_id'], ENT_QUOTES));
    
    if(empty($apple_idtrim))
    {
        $errors[] = 'Please enter a valid apple id.';
    }
    
    // yop
    $yoptrim = trim(htmlspecialchars($_POST['yop'], ENT_QUOTES));
    
    if(empty($yoptrim))
    {
        $errors[] = 'Please enter a valid yop.';
    }

    // breed
    $breedtrim = trim(htmlspecialchars($_POST['breed'], ENT_QUOTES));
    
    if(empty($breedtrim))
    {
        $errors[] = 'Please enter a valid breed.';
    }

    // row
    $rowtrim = trim(htmlspecialchars($_POST['row'], ENT_QUOTES));
    
    if(empty($rowtrim))
    {
        $errors[] = 'Please enter a valid row.';
    }
    
    // column
    $columntrim = trim(htmlspecialchars($_POST['column'], ENT_QUOTES));
    
    if(empty($columntrim))
    {
        $errors[] = 'Please enter a valid column.';
    }

     // geolocation
     $geolocationtrim = trim(htmlspecialchars($_POST['geolocation'], ENT_QUOTES));
    
     if(empty($geolocationtrim))
     {
         $errors[] = 'Please update location.';
     }
     else
     {
        $separator_index = strpos($geolocationtrim, ',');
        $longitude = floatval(substr($geolocationtrim,0, $separator_index - 1));
        $latitude = floatval(substr($geolocationtrim, $separator_index + 1, strlen($geolocationtrim) - 1));
        
     }

     $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

     if ($id === false || $id <= 0) 
     {
         $errors[] = 'Invalid Apple ID.';
     }
     else
     {
        $idtrim = $id;
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
            mysqli_stmt_bind_param($q, 's',$idtrim);
            // execute query
            mysqli_stmt_execute($q);
            $result = mysqli_stmt_get_result($q);
            
            if(mysqli_num_rows($result) == 1 )
            {                
                $query = "SELECT apple_id  FROM apples WHERE (apple_id=?) or (row=? and col=?)";
                $q = mysqli_stmt_init($dbcon);
                mysqli_stmt_prepare($q, $query);
                //use prepared statement to ensure that only text is inserted
                //bind fields to SQL Statement
                mysqli_stmt_bind_param($q, 'sss',$apple_idtrim, $rowtrim, $columntrim);
                // execute query
                mysqli_stmt_execute($q);
                $result = mysqli_stmt_get_result($q);
                $num_of_rows = mysqli_num_rows($result);

                if($num_of_rows)
                {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                }
                
                if(($num_of_rows == 0) || (($num_of_rows == 1) && ($row['apple_id'] == $apple_idtrim)) )
                {                
                    
                   //Make the query to update the existing apple:
                   $query = "UPDATE apples SET yop=?, breed_id=?, row=?, col=?, latitude=?, longitude=? WHERE apple_id=?";
                   $q = mysqli_stmt_init($dbcon);
                   mysqli_stmt_prepare($q, $query);
                   //use prepared statement to ensure that only text is inserted
                                   
                   mysqli_stmt_bind_param($q, 'ssssdds', $yoptrim, $breedtrim, $rowtrim, $columntrim, $latitude, $longitude, $apple_idtrim);
                  
                    if( mysqli_stmt_execute($q)) //One record inserted
                    {
                        $success = "Apple Edited successfully.";                                        
                    }
                    else //If it did not run OK.
                    {                            
                        //Debugging message below do not use in production
                        //echo '<p>' . mysqli_error($dbcon) . '<br><br>Query: ' . $query . '</p>';

                        //Public message:
                        $internal_error = "The system is busy please try later.";

                        mysqli_close($dbcon); // Close the database connection.

                    }
                }
                else
                {
                    //Public message:
                    $apple_error = "The Apple ID is already registered or there is a registered apple at the same row and column.";
                                    
                                    
                    mysqli_close($dbcon); // Close the database connection.
                    
                   
                }

                
            }
            else
            {
                //Public message:
                $apple_error = "The Apple ID does not exist.";
                                
                                
                mysqli_close($dbcon); // Close the database connection.
                
               
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
