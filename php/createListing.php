<?php 
session_start(); 
include "../php/func.php"; 

// Get POST data
$eventName = trim($_POST['eventName'] ?? '');
$eventLocation = trim($_POST['eventLocation'] ?? '');
$eventDate = trim($_POST['eventDate'] ?? '');
$eventStartTime = trim($_POST['eventStartTime'] ?? '');
$eventEndTime = trim($_POST['eventEndTime'] ?? '');
$eventDescription = trim($_POST['eventDescription'] ?? '');
$user = $_SESSION["username"];

// Initialize image path
$imagePath = null;
$listingFile = '../data/listing.json';

// Handle file upload FIRST (before saving to JSON)
if (isset($_FILES["eventImage"]) && $_FILES["eventImage"]["error"] == 0) {
    $target_dir = "../listings/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $imageFileType = strtolower(pathinfo($_FILES["eventImage"]["name"], PATHINFO_EXTENSION));
    
    // Validate image
    $check = getimagesize($_FILES["eventImage"]["tmp_name"]);
    if ($check === false) {
        exit;
    }
    
    // Check file size (500KB)
    if ($_FILES["eventImage"]["size"] > 5000000) {
        exit;
    }
    
    // Create unique filename
    $uniqueName = uniqid() . '_' . basename($_FILES["eventImage"]["name"]);
    $target_file = $target_dir . $uniqueName;
    
    // Upload file
    if (move_uploaded_file($_FILES["eventImage"]["tmp_name"], $target_file)) {
        $imagePath = "../listings/" . $uniqueName;
    } else {
        exit;
    }
}

// Read existing listings
if (file_exists($listingFile)) {
    $content = file_get_contents($listingFile);
    $listings = json_decode($content, true);
} else {
    $listings = array();
}

// Find next ID
$maxId = 0;
foreach ($listings as $listing) {
    if (isset($listing['id']) && $listing['id'] > $maxId) {
        $maxId = $listing['id'];
    }
}
$newId = $maxId + 1;

// Create new listing (NOW imagePath has the correct value)
$newListing = array(
    "event" => $eventName,
    "image" => $imagePath,
    "id" => $newId,
    "date" => $eventDate,
    "location" => $eventLocation,
    "start-time" => convertToMilitaryTime($eventStartTime) . ":00",
    "end-time" => convertToMilitaryTime($eventEndTime) . ":00",
    "description" => $eventDescription,
    "host" => $user
);

// Add to listings array
$listings[] = $newListing;

// Encode to JSON
$json = json_encode($listings, JSON_PRETTY_PRINT);

// Write to file
file_put_contents($listingFile, $json, LOCK_EX);

$users_file = "../data/users.json"; 
if (file_exists($users_file)) {
    $user_data = json_decode(file_get_contents($users_file), true);
} 

foreach ($user_data as &$user) {
    if ($user['username'] == $_SESSION["username"]) {
        $user['createdPosts'][] = $newId; 
        file_put_contents($users_file, json_encode($user_data, JSON_PRETTY_PRINT));
        break; 
    }
}

for ($i=0; $i<count($user_data); $i++) {
    if ($user_data[$i]["username"] == $_SESSION["username"]) {
        if($user_data[$i]["is_admin"] === true || $user_data[$i]["is_admin"] === "true") {
            header("Location: ../html/adminEditPost.html.php");
            exit(); 
        }
        else {
            header("Location: ../html/userEditPost.html.php");
            exit(); 
        }
    } 
}
exit;
?>