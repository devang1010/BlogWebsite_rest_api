<?php 
    require "../config/database.php";

    function generateToken(){
        return bin2hex(random_bytes(32));
    }

    function authenticateUser($token){
        global $conn;
        $sql = "SELECT * FROM users WHERE token = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }
?>