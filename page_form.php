<?
require 'init.php'; // database connection, etc

$task = $get_post['task'];
switch ($task)  {

    case 'save':
        // Create a database record From Form Submission

        // orig values
        if ( isset($get_post['crud_id']) && $get_post['crud_id'] > 0 ) {
          $crud_id = $get_post['crud_id'];  // need to update existing person DB record
        }
        else {
          $crud_id = 0;                    // need to create new person DB record
        }

        // addslashes() escapes characters like ' and " in the data that can break SQL statements
        // remove addslashes() and submit data containing a ' and see what happens.

        // VARIABLE =                         HTML NAME / ID
        $fullname = addslashes(trim($get_post['fullname']));
        $email = addslashes(trim($get_post['email']));
        $num_of_squirrels_seen = addslashes(trim($get_post['num_of_squirrels_seen']));
        $squirrels_burying_nuts = addslashes(trim($get_post['squirrels_burying_nuts']));
        $types_of_squirrels = addslashes(trim($get_post['types_of_squirrels']));
        $nuts_forgotten = addslashes(trim($get_post['nuts_forgotten']));
        
        $low_classification_squirrels1 = addslashes(trim($get_post['low_classification_squirrels1']));
        $low_classification_squirrels2 = addslashes(trim($get_post['low_classification_squirrels2']));
        $low_classification_squirrels = $low_classification_squirrels1 . " " . $low_classification_squirrels2;

        $flying_squirrel = addslashes(trim($get_post['flying_squirrel']));

        $red_squirrels_db_var = implode(" ", $get_post['red_squirrels']);

        // Server-Side Validation
        if ( !$fullname || !$email ) {
          // reload this page with error message
          // Ideally, client-side form validation would have caught this

          if ( $crud_id > 0 ) {
            $transfer_url = "page_form.php?incomplete=yes&task=edit&crud_id=$crud_id";
          }
          else {
            $transfer_url = "page_form.php?incomplete=yes";
          }
          header ("Location: $transfer_url");
          exit;
        }

        if ( $crud_id > 0 ) {
          // Build the UPDATE statement
          $sql = "UPDATE " . YOUNUS . " SET fullname ='$fullname',
                                            email = '$email',
                                            num_of_squirrels_seen = '$num_of_squirrels_seen',
                                            squirrels_burying_nuts = '$squirrels_burying_nuts',
                                            types_of_squirrels = '$types_of_squirrels',
                                            nuts_forgotten = '$nuts_forgotten',
                                            low_classification_squirrels = '$low_classification_squirrels',
                                            flying_squirrel = '$flying_squirrel',
                                            red_squirrels = '$red_squirrels_db_var'
                                        WHERE crud_id=$crud_id ";
        }
        else {
          // Build the INSERT statement

            $sql = "INSERT INTO " . YOUNUS . " (`crud_id`, `fullname`, `email`, `num_of_squirrels_seen`, `squirrels_burying_nuts`, `types_of_squirrels`, `nuts_forgotten`, `low_classification_squirrels`, `flying_squirrel`, `red_squirrels`) 
            VALUES (NULL,'$fullname','$email','$num_of_squirrels_seen', '$squirrels_burying_nuts', '$types_of_squirrels', '$nuts_forgotten', '$low_classification_squirrels', '$flying_squirrel', '$red_squirrels_db_var')";

        }

        // Execute the SQL query
        lib::db_query($sql);

        // Transfer to the listing page -- not good to leave the browser sitting on a post data transaction
        // The PHP header function adds the Location directive to the HTTP response header, which then causes the browser to do the transfer.
        header ("Location: page_listing.php");
        exit;
        break;
        /////////////////////////////
        // End Save Case
        /////////////////////////////

    case 'delete':
      // Just delete that puppy

       if ( isset($get_post['crud_id']) && $get_post['crud_id'] > 0 ) {
          $crud_id = $get_post['crud_id'];
       }

       // Build the DELETE statement
       $sql = "DELETE FROM " . YOUNUS . " WHERE crud_id=$crud_id ";
       lib::db_query($sql);

       header ("Location: page_listing.php?deleted_message=yes");
       exit;
       break;

      /////////////////////////////
      // End delete Case
      /////////////////////////////

    case 'edit':

      if ( ! isset($get_post['crud_id']) || $get_post['crud_id'] <= 0 ) {
        // if no incoming crud_id, just give blank form
        break;
      }

      $crud_id = $get_post['crud_id'];

      $sql = "SELECT * FROM " . YOUNUS . " WHERE  crud_id=$crud_id ";

      $result = lib::db_query($sql);
      $row = $result->fetch_assoc();  // will only be one row

      $fullname = htmlspecialchars($row['fullname']);
      $email = htmlspecialchars($row['email']);
      $num_of_squirrels_seen = htmlspecialchars($row['num_of_squirrels_seen']);
      $squirrels_burying_nuts = htmlspecialchars($row['squirrels_burying_nuts']);
      $types_of_squirrels = htmlspecialchars($row['types_of_squirrels']);
      $low_classification_squirrels = htmlspecialchars($row['low_classification_squirrels']);
      $flying_squirrel = htmlspecialchars($row['flying_squirrel']);
      $red_squirrels = htmlspecialchars($row['red_squirrels']);

      $low_classification_squirrels_with_spaces = "-----" . $low_classification_squirrels;

      /*
        It would be tempting to allpy htmlspecialchars() to every column from the DB like below

        foreach ($row as $key => $value) {
          $row[$key] = htmlspecialchars($value);
        }

        But if a column contains serialized data, htmlspecialchars() can mess that up.
        Plus, the radio/checkbox/menu values are not user entered, so shouldn't need cleaned up anyway.
      */

      break;

      /////////////////////////////
      // End edit Case
      /////////////////////////////

    default:
    // switch default case (no task submitted)
    // just drops into the page with blank HTML form
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>PHP/MySQL CRUD Example</title>
	</head>
	<body>
	   <? if( $get_post['incomplete'] ) { ?>
	      Your Form Submission was Missing Data
	      <br><br>
	   <? } ?>

	   <br>

	   <a href="page_listing.php">Go To CRUD Listing Page</a> or create a new Database Record below.

	   <br><br>

	   <!-- This Form Submits to this same file!  -->

       <form action="page_form.php" method="POST" >
            <input type="hidden" name="task" value="save">
	        <input type="hidden" name="crud_id" value="<?=$crud_id?>">
   
            <!-- Name -->
            <label for="name">Name:</label><br>
            <input type="text" name="fullname" value="<?=$fullname?>"><br><br>
            
            <!-- Email -->
            <label for="name">Email:</label><br>
            <input type="text" name="email" value="<?=$email?>"><br><br>

            <!-- Number -->
            <label for="number">How many squirrels did you see today? (1-100):</label><br><br>
            <input type="number" name="num_of_squirrels_seen" value="<?=$num_of_squirrels_seen?>" min="1" max="100"><br><br>

            <!-- Range -->
            <label for="range">How many squirrels did you see burying nuts today? (1-100):</label><br><br>
            <input type="range" name="squirrels_burying_nuts" value="<?=$squirrels_burying_nuts?>" min="1" max="100"><br><br>
            
            <!-- Textarea -->
            <label for="textarea">How many types of squirrels do you know of? List the ones you know: </label><br><br>
            <textarea name="types_of_squirrels" rows="4" cols="50"><?=$types_of_squirrels?></textarea><br><br>

            <!-- Checkboxes -->
            <div>Choose what percent of nuts you think squirrels forget they bury every year:</div>

            <input type="checkbox" name="nuts_forgotten" value="10" <? if($row['nuts_forgotten'] == '10'){echo 'checked="yes"';} ?> >
            <label for="checkbox1"> 10% </label><br>

            <input type="checkbox" name="nuts_forgotten" value="40" <? if($row['nuts_forgotten'] == '40'){echo 'checked="yes"';} ?>>
            <label for="checkbox2"> 40% </label><br>

            <input type="checkbox" name="nuts_forgotten" value="80" <? if($row['nuts_forgotten'] == '80'){echo 'checked="yes"';} ?>>
            <label for="checkbox3"> 80% </label><br><br>

            <!-- MultiSelect Checkboxes -->
            <div>Which are you most surprised to find out are considered part of lower classifications of squirrels, choose 1 from each category</div>
            <input type="radio" name="low_classification_squirrels1" value="marmot" <? if (strpos($low_classification_squirrels_with_spaces, "marmot") > 0){echo ' checked ';} ?>>
            <label for="radio1">Marmot</label><br>

            <input type="radio" name="low_classification_squirrels1" value="pine_squirrel" <? if (strpos($low_classification_squirrels_with_spaces, "pine_squirrel") > 0){echo ' checked ';} ?>>
            <label for="radio1">Pine squirrel</label><br><br>

            <input type="radio" name="low_classification_squirrels2" value="groundhog" <? if (strpos($low_classification_squirrels_with_spaces, "groundhog") > 0){echo ' checked ';} ?>>
            <label for="radio2">Groundhog</label><br>

            <input type="radio" name="low_classification_squirrels2" value="prarie_dogs" <? if (strpos($low_classification_squirrels_with_spaces, "prarie_dogs") > 0){echo ' checked ';} ?>>
            <label for="radio2">Prarie Dogs</label><br><br>

            <!--Single select dropdown -->
            <label for="single_dropdown">Which of the following is the scientific name for flying squirrels</label><br>
                <select name="flying_squirrel">
                  <option value="tamias" <? if($flying_squirrel== 'tamias'){echo ' selected ';} ?>>Tamias</option>
                  <option value="pteromyini" <? if($flying_squirrel == 'pteromyini'){echo ' selected ';} ?>>Pteromyini</option>
                  <option value="eutamias_sibiricus" <? if($flying_squirrel == 'eutamias_sibiricus'){echo ' selected ';} ?>>Eutamias sibiricus</option>
                  <option value="meles_meles" <? if($flying_squirrel == 'meles_meles'){echo ' selected ';} ?>>Meles meles</option>
                </select>
                <br><br>

            <!--Multi select dropdown -->
            <label for="multi_dropdown">Which two refer to red squirrels? (Hold down command/ctrl to select both answers):</label><br>
            <select name="red_squirrels[]" multiple size="4">
              <option value="tamias" <? if (strpos($red_squirrels, "tamias") > 0){echo ' selected ';} ?> ?>Tamias</option>
              <option value="eutamias_sibiricus" <? if (strpos($red_squirrels, "eutamias_sibiricus") > 0){echo ' selected ';} ?>>Eutamias Sibiricus</option>
              <option value="pteromyini" <? if (strpos($red_squirrels, "pteromyini") > 0){echo ' selected ';} ?>>Pteromyini</option>
              <option value="meles_meles" <? if (strpos($red_squirrels, "meles_meles") > 0){echo ' selected ';} ?>>Meles meles</option>
            </select>
            <br><br><br>

            <input type="submit" value="Submit">
        </form>
	</body>
</html>