<?php
session_start();

$id = $_POST['id'];
$username = $_SESSION["username"]; 

$usersFile = '../data/users.json';
$jsonContent = file_get_contents($usersFile);
$users = json_decode($jsonContent, true);

foreach ($users as &$user) {
    if ($user['username'] == $username) {

        // Check if already bookmarked
        $index = array_search($id, $user['bookmarkedPosts']);
        
        // comparing a number and a false so use !==. 
        if ($index !== false) {
            // Remove bookmark if index not in bookmarkedPosts
            array_splice($user['bookmarkedPosts'], $index, 1);
            $action = "removed";
        } else {
            // Add bookmark
            $user['bookmarkedPosts'][] = (int)$id;
            $action = "added";
        }
        
        // Save
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        
        echo json_encode([
            "success" => true,
            "action" => $action
        ]);
        
        break;
    }
}
unset($user); 

?>