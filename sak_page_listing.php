<?
require 'init.php'; // database connection, etc

// Selects all fields and all records from the table
$sql = "SELECT * FROM " . YOUNUS . " WHERE 1 ORDER BY fullname ASC ";
$result = lib::db_query($sql);

$num_rows = $result->num_rows;
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Non-Fancy Listing of People Table</title>
		<script type="text/javascript">

      function confirm_delete(primary_key, fullname, email, num_of_squirrels_seen, squirrels_burying_nuts, types_of_squirrels, nuts_forgotten, low_classification_squirrels, flying_squirrel, red_squirrels) {
        var choice = confirm("Are you sure you want to delete " + fullname + "?");

        if ( choice == true ) {
          window.location.href = "sak_page_form2.php?task=delete&primary_key="+primary_key;
        }
      }
    </script>

	</head>
	<body>
	   <a href="sak_page_form.php">Go to the data form. </a>
	   <br><br>

	   <? if ( isset($get_post['deleted_message']) ) { ?>
          <b>The DB record was sucessfuly deleted.</b>
          <br><br>
     <? } ?>

	   <? if ($num_rows == 0) { ?>
	      <b>No records were found in the database.</b>
      <? } else { ?>

        <b>Listing of Database Records:</b>

        <table width="" border="1" cellspacing="0" cellpadding="5">
	      <tr  valign="top">
            <td>Name</td>
            <td>Email</td>
            <td>Number of squirrels seen</td>
            <td>Squirrels burying nuts</td>
            <td>Types of Squirrels</td>
            <td>Number of nuts forgotten</td>
            <td>Low classification of squirrels</td>
            <td>Flying squirrels</td>
            <td>Red squirrels</td>

            <td>&nbsp;</td>
         </tr>
         <? while ( $row = $result->fetch_assoc() ) { ?>
            <tr  valign="top">
               <td><?= $row['fullname'] ?></td>
               <td><?= $row['email'] ?></td>
               <td><?= $row['num_of_squirrels_seen']?></td>
               <td><?= $row['squirrels_burying_nuts']?></td>
               <td><?= $row['types_of_squirrels']?></td>
               <td><?= $row['nuts_forgotten']?></td>
               <td><?= $row['low_classification_squirrels']?></td>
               <td><?= $row['flying_squirrel']?></td>
               <td><?= $row['red_squirrels']?></td>

               <td>
                  <a href="sak_page_form.php?task=edit&primary_key=<?=$primary_key['primary_key']?>">Edit</a>
                  &nbsp;&nbsp;|&nbsp;&nbsp;
                  <a href="#null" onclick="confirm_delete(<?=$primary_key['primary_key']?> , '<?=$fullname['fullname']?>', '<?=$email['email']?>', '<?=$num_of_squirrels_seen['num_of_squirrels_seen']?>', '<?=$squirrels_burying_nuts['squirrels_burying_nuts']?>', '<?=$types_of_squirrels['types_of_squirrels']?>', '<?=$nuts_forgotten['nuts_forgotten']?>', '<?=$low_classification_squirrels['low_classification_squirrels']?>', '<?=$flying_squirrel['flying_squirrel']?>', '<?=$red_squirrels['red_squirrels']?>')">Delete</a>
               </td>
            </tr>
         <? } // end while ?>
         </table>


      <? } // end else ?>

	</body>
</html>