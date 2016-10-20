<?php
    // First we execute our common code to connection to the database and start the session 
    require("../common.php"); 
    var_dump($_POST);

    if (isset($_POST["create_team"])) 
    {
        if(!empty($_POST))
        {
            //Ensure that the user has entered a non-empty Team Name
            if(empty($_POST['team_name']))
            {
                header("Location: /../webpages/myTeams.php?message=1");
                die("Please Enter a Team Name");
            }

            //Ensure that the user has entered a non-empty Max Player
            if($_POST['max_players'] === "--")
            {
                header("Location: /../webpages/myTeams.php?message=2");
                die("Please enter the Max Players for your new team");
            }

            //Checking if the Team Already exists
            $existing_team_query = "
                SELECT
                1
                FROM Teams
                WHERE 
                    Team_name = :team_name
            ";

            $existing_team_params = array(
                ':team_name' => $_POST['team_name']
            );

             try
            {
                $stmt = $db->prepare($existing_team_query);
                $result = $stmt->execute($existing_team_params);
            }
            catch(PDOException $ex)
            {
                die("Failed to run query:" . $ex->getMessage());
            }

            $row = $stmt->fetch();

            //If a row was returned, then we found a matching Team that is already in use.
            if($row)
            {
                $_SESSION['create_team_name'] = $_POST['team_name'];
                //$this->session->mark_as_flash('team_name')
                header("Location: /../webpages/myTeams.php?message=3?");
                //header("Location: /../webpages/myTeams.php?message=3".$team.);
                die("Unfortunately, the name ".$_POST['team_name']." is already in use");
            }

            //Finding the Last Team_ID
            $existing_teamID_query = "
                SELECT 
                Team_ID, Team_Name
                FROM Teams
            ";

             $existing_teamID_params = array(
                ':team_id' => $_POST['team_id']
            ); 
            
            try
            {
                $stmt = $db->prepare($existing_teamID_query);
                $result = $stmt->execute();
            }
            catch(PDOException $ex)
            {
                die("Failed to run query:" . $ex->getMessage());
            }

            $rows = $stmt->fetchAll();
            
            function findMax($rows)
            {
                $high_id = 0;

                foreach ($rows as $team) {
                    
                    while ($high_id <= $team['Team_ID']) {
                        $high_id++;
                    }
                }
                //var_dump($high_id);
                return $high_id;
            }

            $max_teamID = findMax($rows);




            //This query will create the new team that the user wanted.
             $create_team_query = "
                INSERT INTO Teams (
                    Team_Name,
                    Team_ID,
                    Admin_ID,
                    Member_ID,
                    Max_Players
                ) VALUES (
                    :team_name,
                    :team_id,
                    :admin_id,
                    :member_id,
                    :max_players
                ) 
            ";

            $create_team_params = array(
                ':team_name' => $_POST['team_name'],
                ':team_id' => $max_teamID,
                ':admin_id' => $_POST['user_id'],
                ':member_id' => $_POST['user_id'],
                ':max_players' => $_POST['max_players']
            ); 

            try
            {
                //Execute the query to creat the team
                $stmt = $db->prepare($create_team_query);
                $result = $stmt->execute($create_team_params);
            }
            catch(PDOException $ex)
            {
                 // Note: On a production website, you should not output $ex->getMessage(). 
                // It may provide an attacker with helpful information about your code.  
               
                die("Failed to run query: " . $ex->getMessage()); 
            }

            //Redirects the user to the myTeams.php page after they create their team
            header("Location: ../../webpages/myTeams.php");

            //Calling die or exit after redirect will allow PHP script to continue to execute
            die("Redirecting to ../webpages/myTeams.php");
        }   
    }
    if (isset($_POST["edit_team"]))
    {
        var_dump("Made it to the editing team section");
    } 
    if (isset($_POST["delete_team"]))
    {
        var_dump("Made it to the deleting team section");
    } 
?> 