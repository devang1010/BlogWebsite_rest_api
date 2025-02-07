<?php 
    $servername = "localhost";
    $Username = "root";
    $Password = "";
    $database = "blog_website_restapi";

    $conn = mysqli_connect($servername, $Username, $Password, $database);
    if(!$conn){
        echo "Error connecting to database<br>";
    }
?>