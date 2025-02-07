<?php 
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require "../config/database.php";
    require "../config/auth.php";

    $method = $_SERVER['REQUEST_METHOD'];
    
    // Get all users or a specific user
    if ($method == "GET") {
        if (isset($_GET["id"])) {
            $id = intval($_GET["id"]);
            $sql = "SELECT id, username, email FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                echo json_encode(["status" => "ok", "data" => $row]);
            } else {
                echo json_encode(["status" => "error", "message" => "User not found"]);
            }
        } else {
            $sql = "SELECT id, username, email FROM users";
            $result = mysqli_query($conn, $sql);
            $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode(["status" => "ok", "data" => $users]);
        }
    }
    
    // Register user
    elseif ($method == "POST" && isset($_GET['action']) && $_GET['action'] == "signup") {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit();
        }

        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = md5((trim($data['password'])));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format"]);
            exit();
        }

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "ok", "message" => "User registered successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error registering user"]);
        }
    }
    
    // User login
    elseif ($method == "POST" && isset($_GET['action']) && $_GET['action'] == "login") {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['email']) || empty($data['password'])) {
            echo json_encode(["status" => "error", "message" => "Email and password required"]);
            exit();
        }

        $email = trim($data['email']);
        $password = md5((trim($data['password'])));
        $sql = "SELECT id, password FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            if ($password === $user['password']) {
                $token = generateToken();
                $sql = "UPDATE users SET token = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $token, $user['id']);
                mysqli_stmt_execute($stmt);
                
                echo json_encode(["status" => "success", "message" => "Login successful", "token" => $token]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid password"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
    }

    // Update user details
    elseif ($method == "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['id']) || empty($data['username']) || empty($data['email'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            exit();
        }

        $id = intval($data['id']);
        $username = trim($data['username']);
        $email = trim($data['email']);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format"]);
            exit();
        }

        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "ok", "message" => "User updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating user"]);
        }
    }

    // Delete user
    elseif ($method == "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id']);
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "ok", "message" => "User deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting user"]);
        }
    }

    mysqli_close($conn);
?>