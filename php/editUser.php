<?php
session_start(); 
// Get the JSON data sent from JavaScript
$jsonData = file_get_contents('php://input');
$changes = json_decode($jsonData, true);

$oldUsername = $changes[0];
$newUsername = $changes[1];
$newPassword = $changes[2];
$newLocation = $changes[3]; 

if (empty($oldUsername)) {
    echo "No username provided";
    exit;
}

$usersFile = '../data/users.json';
$listingFile = '../data/listing.json'; 

$jsonContent = file_get_contents($usersFile);
$users = json_decode($jsonContent, true);

// Find the user
$userIndex = -1;
for ($i = 0; $i < count($users); $i++) {
    if ($users[$i]['username'] === $oldUsername) {
        $userIndex = $i;
        break;
    }
}

if ($userIndex === -1) {
    echo "user not found";
    exit;
}

// Update username 
if (!empty($newUsername) && $newUsername != $oldUsername) {
    foreach ($users as $user) {
        if ($user['username'] == $newUsername) {
            echo "username exists";
            exit;
        }
    }
    
    // Update username
    $users[$userIndex]['username'] = $newUsername;

    //Update the host for the listing to the new username. 
    if (file_exists($listingFile)) {
        $listings = json_decode(file_get_contents($listingFile), true); 
    }

    foreach ($listings as &$listing) {
        if ($listing['host'] == $oldUsername) {
            $listing['host'] = $newUsername; 

            file_put_contents($listingFile, json_encode($listings, JSON_PRETTY_PRINT)); 
        }
    }
    unset($listing); 
    
    // Rename the user's folder
    $oldFolder = "../Users/" . $oldUsername;
    $newFolder = "../Users/" . $newUsername;
    
    if (file_exists($oldFolder)) {
        rename($oldFolder, $newFolder);
    }
}

if (!empty($newPassword)) {
    $users[$userIndex]['password'] = $newPassword;
}

if (!empty($newLocation)) {
    $users[$userIndex]['location'] = $newLocation;
}

file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

$_SESSION["username"] = $newUsername; 

for ($i=0; $i<count($users); $i++) {
    if ($users[$i]["username"] == $_SESSION["username"]) {
        if($users[$i]["is_admin"] === true || $users[$i]["is_admin"] === "true") {
            echo json_encode($users);
            exit(); 
        }
        else {
            $curr_user[] = $users[$i]; 
            echo json_encode($curr_user);   
            exit(); 
        }
    } 
}
?>