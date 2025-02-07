<?php 
    
    header("Content-Type: application/json");
    require "../config/database.php";

    $method = $_SERVER["REQUEST_METHOD"];

    // Get all the posts
    if($method == "GET"){
        if(isset($_GET["id"])){
            $id = intval($_GET["id"]);
            $sql = "SELECT * FROM `posts` WHERE `id` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($result)){
                echo json_encode(["status" => "ok", "data" => $row]);
            }else{
                echo json_encode(["status" => "error", "message" => "No such post found"]);
            }
        }
        else{
            $sql = "SELECT * FROM `posts`";
            $result = mysqli_query($conn, $sql);
            $posts = [];
            while($row = mysqli_fetch_assoc($result)){
                $posts[] = $row;
            }
            echo json_encode(["status" => "ok", "data" => $posts]);
        }
    }

    // Create a new post
    elseif($method == "POST"){
        $data = json_decode(file_get_contents("php://input"), true);
        $title = $data['title'];
        $body = $data['body'];
        $auther = $data['auther'];
        $sql = "INSERT INTO `posts` (`title`, `body`, `auther`) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $title, $body, $auther);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(["status" => "ok", "message" => "Post has been published."]);
        }else{
            echo json_encode(["status" => "error", "message" => "Error occured while publishing post"]);
        }
    }

    // Update post
    elseif($method == "PUT"){
        $data = json_decode(file_get_contents("php://input"), true);
    
        if(isset($data['id'], $data['title'], $data['body'], $data['auther'])) {
            $id = $data['id'];
            $title = $data['title'];
            $body = $data['body'];
            $auther = $data['auther'];
    
            $sql = "UPDATE `posts` SET `title` = ?, `body` = ?, `auther` = ? WHERE `id` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $title, $body, $auther, $id);
    
            if(mysqli_stmt_execute($stmt)){
                echo json_encode(["status" => "success", "message" => "Post has been updated"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Post has not been updated"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        }
    }
    
    // Delete Post
    elseif($method == "DELETE"){
        $data = json_decode(file_get_contents("php://input"), true);

        if(isset($data['id'])){
            $id = $data['id'];

            $sql = "DELETE FROM `posts` WHERE `id` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            if(mysqli_stmt_execute($stmt)){
                echo json_encode(["status" => "success", "message" => "Post has been deleted"]);
            }else{
                echo json_encode(["status" => "error", "message" => "Error while deleting post"]);
            }
        }
    }
?>