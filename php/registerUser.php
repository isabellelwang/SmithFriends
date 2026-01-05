<?php
// Get username and password from POST
$username = $_POST['username']; 
$password = $_POST['password']; 
$location = $_POST['location']; 

$usersFile = '../data/users.json';

// Read users.json
if (!file_exists($usersFile)) {
    // If file doesn't exist, create it with empty array
    $users = array();
} else {
    $content = file_get_contents($usersFile);
    $users = json_decode($content, true);
}

// Check if username already exists
foreach ($users as $user) {
    if ($user['username'] == $username) {
        echo json_encode([
            "success" => false,
            "message" => "Username already exists"
        ]);
        exit;
    }
}
// username doesn't exist
echo json_encode([
        "success" => true,
        "message" => "Successfully Registered!"
]);

// Create new user entry
$newUser = array(
    "username" => $username,
    "password" => $password,
    "location" => $location, 
    "createdPosts" => array(), 
    "bookmarkedPosts" => array(), 
    "is_admin" => false
);

// Add new user to array
$users[] = $newUser;

// Create user folder
$userFolder = "../Users/" . $username;
mkdir($userFolder); 

# JSON Pretty print is for formatting. 
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT)); 