<?php
/**
 * Created by PhpStorm.
 * User: Eero
 * Date: 27.11.2018
 * Time: 22:03
 */
$connect = mysqli_connect("localhost","process","yield","userbase");
$query = "SELECT * FROM item";
$result = mysqli_query($connect, $query);
$json_array = array();

while($row = mysqli_fetch_assoc($result)) {
    $json_array[] = $row;
}
echo json_encode($json_array);


//INSERT JSON DATA INTO TABLE ->
/*
$data = '[{"ammount":"10","buffer":"BUFFER_1","name":"TEST1"},{"ammount":"15","buffer":"BUFFER_1","name":"TEST2"}]';
$array = json_decode($data, true);

foreach($array as $row) {
    $sql = "INSERT INTO item(ammount, buffer, name) 
      VALUES('".$row["ammount"]."','".$row["buffer"]."','".$row["name"]."')";
    mysqli_query($connect, $sql);
}
*/

?>