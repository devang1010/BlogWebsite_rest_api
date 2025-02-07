<?php
header("Content-Type: application/json");

echo json_encode([
    "status" => "success",
    "message" => "Welcome to Blog API",
    "POST endpoints" => [
        "POST /api/Post.php" => "Create a blog post",
        "GET /api/Post.php" => "Get all posts",
        "GET /api/Post.php?id=POST_ID" => "Get a single post",
        "PUT /api/Post.php?id=POST_ID" => "Update a post",
        "DELETE /api/Post.php?id=POST_ID" => "Delete a post"
    ],
    "User endpoints" =>[
        "POST /api/User.php" => "Create a blog User",
        "GET /api/User.php" => "Get all User",
        "GET /api/User.php?id=USER_ID" => "Get a single User",
        "PUT /api/User.php?id=USER_ID" => "Update a User",
        "DELETE /api/User.php?id=USER_ID" => "Delete a User"
    ]
]);
?>