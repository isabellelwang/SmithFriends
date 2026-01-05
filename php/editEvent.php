<?php 
session_start(); 
include "../php/func.php"; 
// Get POST data
$editName = trim($_POST['editName'] ?? '');
$editLocation = trim($_POST['editLocation'] ?? '');
$editDate = trim($_POST['editDate'] ?? '');
$editStartTime = trim($_POST['editStartTime'] ?? '');
$editEndTime = trim($_POST['editEndTime'] ?? '');
$editDescription = trim($_POST['editDescription'] ?? '');


$eventID = trim($_POST['hiddenID']); 
$user = $_SESSION["username"];

// Initialize image path
$imagePath = null;
$listingFile = '../data/listing.json';

// Handle file upload FIRST (before saving to JSON)
if (isset($_FILES["editImage"]) && $_FILES["editImage"]["error"] == 0) {
    $target_dir = "../listings/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $imageFileType = strtolower(pathinfo($_FILES["editImage"]["name"], PATHINFO_EXTENSION));
    
    // Validate image
    $check = getimagesize($_FILES["editImage"]["tmp_name"]);
    if ($check === false) {
        exit;
    }
    
    // Check file size (500KB)
    if ($_FILES["editImage"]["size"] > 5000000) {
        exit;
    }
    
    // Create unique filename
    $uniqueName = uniqid() . '_' . basename($_FILES["editImage"]["name"]);
    $target_file = $target_dir . $uniqueName;
    
    // Upload file
    if (move_uploaded_file($_FILES["editImage"]["tmp_name"], $target_file)) {
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


foreach ($listings as &$event) {
    if ($event['id'] == $eventID) {
        if($editName != '') {
            $event['event'] = $editName; 
        }
        if($editLocation != '') {
            $event['location'] = $editLocation; 
        }
        if($editDate != '') {
            $event['date'] = $editDate; 
        }
        if($editStartTime != '') {
            $event["start-time"] = convertToMilitaryTime($editStartTime) . ":00"; 
        }
        if($editEndTime != '') {
            $event["end-time"] = convertToMilitaryTime($editEndTime) . ":00"; 
        }
        if($editDescription!= '') {
            $event['description']= $editDescription; 
        }
        // Update image only if new one was uploaded
        if ($imagePath != null) {
            $event['image'] = $imagePath;
        }
        break;
    }
}
unset($event); // Break the reference

// Encode to JSON
$json = json_encode($listings, JSON_PRETTY_PRINT);

// Write to file
$result = file_put_contents($listingFile, $json, LOCK_EX);

echo json_encode(["success" => true, "message" => "Event created successfully!"]);

$users_file = "../data/users.json"; 
if (file_exists($users_file)) {
    $user_data = json_decode(file_get_contents($users_file), true);
} 

for ($i=0; $i<count($user_data); $i++) {
    if ($user_data[$i]["username"] == $_SESSION["username"]) {
        if($user_data[$i]["is_admin"] == "true") {
            header("Location: ../html/adminEditPost.html.php");
            break; 
        }
        else {
            header("Location: ../html/userEditPost.html.php");
        }
    }
}
exit;
?>