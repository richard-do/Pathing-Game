<?php
$hostname = "localhost";
$username = "your_username";
$password = "your_password";
$databasename = "database_name";
$mysqli = new mysqli($hostname, $username, $password, $databasename);

// grab values from cgi
$dream = isset($_POST["dream"]) ? $_POST["dream"] : "";
$type = isset($_POST["type"]) ? $_POST["type"] : "";
$id = isset($_POST["id"]) ? $_POST["id"] : "";
$command = isset($_POST["command"]) ? $_POST["command"] : "";

// prevent a situation where refreshing the page can cause empty entries
if ($type != ""){
$query = "INSERT INTO dreams (id, dream, type) VALUES (NULL, ?, ?)";
$stmt = $mysqli->stmt_init();
     if ($stmt->prepare($query)){
         $stmt->bind_param("ss", $dream, $type);
         $stmt->execute();
         $stmt->close();
     } else {
         console.log("Failed to deliver data");
     }
}
// checks to see if delete was issued
// remove from table if so
if( $command == "delete") {
    $query = "DELETE FROM dreams WHERE id=?";
    $stmt = $mysqli -> stmt_init();
    if($stmt-> prepare($query)) {
        $stmt -> bind_param("i", $id);
        $stmt -> execute();
        $stmt -> close();
    }
    echo "Success!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>The Totally Unique Title of this Completely Generic Form Table</title>

        <link rel="stylesheet" href="styles.css">

        <!-- load ajax -->
        <script type="text/javascript" src="ajax.js"></script>
        <script type="text/javascript">

        window.addEventListener("DOMContentLoaded", ()=>{
            const message = document.getElementById('message');
        });

        //removes an entry from the database
        function remove(id){
            var client = new HttpClient();
            const string = `command=delete&id=${encodeURI(id)}`;
            client.makeRequest("dreams.php", string);

            // lets us know if the removal from table was successful
            client.callback = function(result) {
                 if (result == "Success!") {
                     popup(result, "#acd241"); // green
                 } else {
                     popup(result, "#ebcb35"); // yellow
                 }
            }


            // hide the element
            document.getElementById(id).style.display="none";
        }

        // totally not a stolen copy pasta
        // of dan's popup code
        function popup(words, color) {
            message.innerHTML = words;
            message.style.display = "block";
            message.style.backgroundColor = color;
            document.addEventListener("mousedown", pop);
            function pop(e) {
                message.style.display = "none";
            };
        }

        </script>
    </head>
    <body>
        <div id="message"></div>

        <?php
         // grabs the table
         $query = "SELECT id, type, dream FROM dreams ORDER BY id DESC LIMIT 10";
         $stmt = $mysqli->stmt_init();
         if ($stmt->prepare($query)) {
         	$stmt->execute();

            // order matches the fields we selected
         	$stmt->bind_result($tempId, $tempType, $tempDream);

            // display the values within the table
         	echo "<br><br><table class="."center".">
         			<tr><th>Type</th><th>Dream</th>";
         	while ($stmt->fetch()) {
         		echo "<tr id=".$tempId.">
     				    <td>".$tempType."</td>
         				<td>".$tempDream."</td>
                        <td><button onclick=remove('".$tempId."')>X</button></td>
         			  </tr>\n";
         	}
         	echo "</table>";
         	$stmt->close();
         } else {
         	$error = "Sorry could not retrieve table";  echo $error;  return;
         }
         ?>
    </body>
</html>
