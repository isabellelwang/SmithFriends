<?php 
session_start(); 

$username = $_SESSION['username'];


// Define directories
$profile_dir = "../Users/" . $username . "/";

// Handle file upload
if (isset($_FILES["profilePic"]) && $_FILES["profilePic"]["error"] == 0) {
    // Validate image
    $check = getimagesize($_FILES["profilePic"]["tmp_name"]);
    if ($check === false) {
        exit;
    }
    
    // Get and validate file extension
    $imageFileType = strtolower(pathinfo($_FILES["profilePic"]["name"], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowed_types)) {
        exit;
    }
    
    // Check file size (500KB)
    if ($_FILES["profilePic"]["size"] > 500000) {
        exit;
    }
    
    // Delete old profile pictures ONLY from profile directory
    $old_pictures = glob($profile_dir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
    foreach ($old_pictures as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    
    $target_file = $profile_dir . basename($_FILES["profilePic"]["name"]) ;
    
    // Upload file
    move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file); 

    $users_file = "../data/users.json"; 
    if (file_exists($users_file)) {
        $user_data = json_decode(file_get_contents($users_file), true);
    } 

    for ($i=0; $i<count($user_data); $i++) {
    if ($user_data[$i]["username"] == $_SESSION["username"]) {
        if($user_data[$i]["is_admin"] === true || $user_data[$i]["is_admin"] === "true") {
            header("Location: ../html/adminProfile.html.php");
            exit(); 
        }
        else {
            header("Location: ../html/userProfile.html.php");
            exit(); 
        }
    } 
}

} 
?>