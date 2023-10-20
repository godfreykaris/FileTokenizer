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

    $nametrim = trim(htmlspecialchars($_POST['name'], ENT_QUOTES));
    
    if(empty($nametrim) || (strlen($nametrim) > 60))
    {
        $errors[] = 'Please enter a valid name.';
    }

    //Check for a an email address:
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if(!empty($email))
    {
        if((!filter_var($email, FILTER_VALIDATE_EMAIL)) || (strlen($email) > 60))
            $errors[] = 'The e-mail is invalid. Max 60';
        else
            $emailtrim = trim($email);        
    }
    else
    {
        $errors[] = 'Please enter valid email address.';
    }  

    $user_id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

    if ($user_id === false || $user_id <= 0) 
    {
        // Handle the case where the user's ID is not valid or missing
        $errors[] = 'Invalid user ID.';
    }
        

    if(empty($errors)) //If everything is OK.
    {
        try
        {
            $query = "SELECT id, email FROM users WHERE (id=?)";
            $q = mysqli_stmt_init($dbcon);
            mysqli_stmt_prepare($q, $query);
            //use prepared statement to ensure that only text is inserted
            //bind fields to SQL Statement
            mysqli_stmt_bind_param($q, 'i',$user_id);
            // execute query
            mysqli_stmt_execute($q);
            $user_result = mysqli_stmt_get_result($q);
            $user_data;
            
            if(mysqli_num_rows($user_result) == 1 )
            {      
                $user_data = mysqli_fetch_assoc($user_result);
                $id = $user_data['id'];
                
                //Check if another user with the email exists
                $query = "SELECT id, email FROM users WHERE (email=?)";
                $q = mysqli_stmt_init($dbcon);
                mysqli_stmt_prepare($q, $query);
                //use prepared statement to ensure that only text is inserted
                //bind fields to SQL Statement
                mysqli_stmt_bind_param($q, 's',$emailtrim);
                // execute query
                mysqli_stmt_execute($q);
                $result = mysqli_stmt_get_result($q);

                $row = mysqli_fetch_assoc($result);
               
                if(mysqli_num_rows($result) == 0 || (mysqli_num_rows($result) == 1 && $row['id'] == $id) )
                {                
                    //Make the query:
                    $query = "UPDATE users SET name = ?, email = ? WHERE id = ?;";                
                    $q = mysqli_stmt_init($dbcon);
                    mysqli_stmt_prepare($q, $query);
                    //use prepared statement to ensure that only text is inserted
                    //bind fields to SQL Statement

                    mysqli_stmt_bind_param($q, 'ssi', $nametrim, $emailtrim, $id );
                    // execute query
                    mysqli_stmt_execute($q);

                    
                    if(mysqli_stmt_affected_rows($q) === -1) //Query error
                    {

                         //Debugging message below do not use in production
                        echo '<p>' . mysqli_error($dbcon) . '<br><br>Query: ' . $query . mysqli_stmt_affected_rows($q) .'</p>';

                        //Public message:
                        $internal_error = "The system is busy please try later.";

                        mysqli_close($dbcon); // Close the database connection.
                        //exit();
                    }    
                    else
                    {
                        $previous_email = $user_data['email'];
                         // Update the email in the users register table
                        //Make the query:
                         $query = "UPDATE users_register SET  email = ? WHERE email = ?";                
                         $q = mysqli_stmt_init($dbcon);
                         mysqli_stmt_prepare($q, $query);
                         //use prepared statement to ensure that only text is inserted
                         //bind fields to SQL Statement

                         mysqli_stmt_bind_param($q, 'ss', $emailtrim, $previous_email );
                         // execute query
                         mysqli_stmt_execute($q);

                          //Public message:
                         $success = "User updated successfully.";                                    

                         mysqli_close($dbcon); // Close the database connection.
                    }  
                }
                else
                {
                      //Public message:
                     $internal_error = "The email address is taken.";                                    

                     mysqli_close($dbcon); // Close the database connection.
                }            
                
            }
            else
            {
                //Public message:
                $internal_error = "User does not exist";
                                
                                
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