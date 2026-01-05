<?php
session_start(); 
include "../php/func.php"; 
// Get the username from POST
$username = $_POST['username'];

$usersFile = '../data/users.json';
$listingFile = '../data/listing.json'; 

// Read users.json
if (!file_exists($usersFile)) {
    echo json_encode(["error" => "Users file not found"]);
    exit;
}

if (!file_exists($listingFile)) {
    echo json_encode(["error" => "Listing file not found"]); 
}

$jsonContent = file_get_contents($usersFile);
$users = json_decode($jsonContent, true);

$listings = json_decode(file_get_contents($listingFile), true); 

// Remove the deleted user's events from other users' bookmarkedPosts
$createdEvents = []; 
foreach ($users as $user) {
    if ($user['username'] == $username) {
        // these are the events that need to be deleted from other users' bookmarks 
        $createdEvents = $user['createdPosts'];
        break;
    }
}


// Delete user from array
$userIndex = -1;
for ($i = 0; $i < count($users); $i++) {
    if ($users[$i]['username'] === $username) {
        $userIndex = $i;
        break;
    }
}

// Get user folder (to delete)
$userFolder = "../Users/" . $username;

// Delete the user's folder and its contents
if (file_exists($userFolder)) {
    // Delete all files in the folder first
    $files = scandir($userFolder);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            unlink($userFolder . '/' . $file);
        }
    }
    // Now delete the empty folder
    rmdir($userFolder);
}

// Remove the user from the array
array_splice($users, $userIndex, 1);

// Remove created listings
$updatedListings = []; 
for ($i = 0; $i < count($listings); $i++) {
    if ($listings[$i]['host'] != $username) {
        $updatedListings[] = $listings[$i];
    } else {
        // remove the image file of the deleted listing
        $imagePath = $listings[$i]['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}   
$listings = $updatedListings;

file_put_contents($listingFile, json_encode($listings, JSON_PRETTY_PRINT));

//update other users' bookmarkedPosts
for ($i = 0; $i < count($users); $i++) {
    $bookmarked = $users[$i]['bookmarkedPosts'];
    $updatedBookmarks = []; 
    foreach ($bookmarked as $bookmark) {
        if (!in_array($bookmark, $createdEvents)) {
            $updatedBookmarks[] = $bookmark;
        }
    }
    $users[$i]['bookmarkedPosts'] = $updatedBookmarks;
}

// Save the updated JSON
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

//if deleting themselves, then redirect to login page. 
if ($_SESSION["username"] == $username) {
    echo json_encode([
        "redirect" => true,
        "url" => "../login.html.php"
    ]);
    exit();
} else {
    // Return the updated user list
    echo json_encode([
        "redirect" => false,
        "users" => $users
    ]);
    exit();
}
?>