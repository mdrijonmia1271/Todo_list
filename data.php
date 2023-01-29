<?php
include_once "config.php";

$connnection = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$connnection){
    throw new Exception("Cannot connect to database");
}else{
    echo "Connected"."<br>";
    echo mysqli_query($connnection, "INSERT INTO tasks(task, date) VALUE ('Do something', '2023-06-21')");
    // $result = mysqli_query($connnection, "SELECT * FROM tasks");
    // while($data = mysqli_fetch_assoc($result)){
    //     echo "<pre>";
    //     print_r($data);
    //     echo "</pre>";
    }
// }
?>