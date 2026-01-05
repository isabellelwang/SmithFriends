<?php
session_start();
// Get the username from POST
$target_id = $_POST['id'];

$listingFile = '../data/listing.json';
$userFile = '../data/users.json';

// Read users.json
if (!file_exists($listingFile)) {
    echo json_encode(["error" => "Listing file not found"]);
    exit;
}

if(!file_exists($userFile)) {
    echo json_encode(["error" => "Users file not found"]);
    exit;
}   

$content = file_get_contents($listingFile);
$curr_listings = json_decode($content, true);

$users = json_decode(file_get_contents($userFile), true);

$new_listings = array(); 
for ($i = 0; $i < count($curr_listings); $i++) {
    if ($curr_listings[$i]['id'] != $target_id) {
        $new_listings[] = $curr_listings[$i];
    }
    // remove the image file of the deleted listing
    else if ($curr_listings[$i]['id'] == $target_id) {
        $imagePath = $curr_listings[$i]['eventImage'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}

// Update users' bookmarkedPosts
for ($i = 0; $i < count($users); $i++) {
    $bookmarked = $users[$i]['bookmarkedPosts'];
    $updatedBookmarks = [];
    for ($j = 0; $j < count($bookmarked); $j++) {
        if ($bookmarked[$j] != $target_id) {
            $updatedBookmarks[] = $bookmarked[$j];
        }
    }
    $users[$i]['bookmarkedPosts'] = $updatedBookmarks;
}

// Save the updated JSON
file_put_contents($listingFile, json_encode($new_listings, JSON_PRETTY_PRINT));
file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT));

// Return the updated event list
echo json_encode($new_listings);
?>