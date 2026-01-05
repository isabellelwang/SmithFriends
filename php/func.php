<?php 
function validate_login($file, $user, $pw) {
    if (file_exists($file)) {
        $x = file_get_contents($file); 
        $items = json_decode($x, true);
    }
    
    $seen = false; 
    for ($i = 0; $i < count($items); $i++) {
        if (strcmp($user, $items[$i]["username"]) == 0) {
            if (strcmp($pw, $items[$i]["password"]) == 0) {
                $seen = true; 
            }
        }
    }
    return $seen; 
}

function check_admin($file, $user) {
    if (file_exists($file)) {
        $x = file_get_contents($file); 
        $items = json_decode($x, true); 
    }

    $is_admin = false; 
    for ($i = 0; $i < count($items); $i++) {
        if (strcmp($user, $items[$i]["username"]) == 0) {
            $is_admin = $items[$i]["is_admin"]; 
        }
    }
    return $is_admin; 
}

function findAllLocations($file) {
    $arr = []; 
    if (file_exists($file)) {
        $x = file_get_contents($file); 
        $items = json_decode($x, true); 

        for ($i=0; $i<count($items); $i++) {
        array_push($arr, $items[$i]["location"]); 
        }
        
        $locations = array_values(array_unique($arr));
        return $locations;
    } 
    return arr; 
}

//returns all events
function getEvents($file) {
    if (file_exists($file)) {
        $json_content = file_get_contents($file); 
        $items = json_decode($json_content, true);
    } else {
        $items = array();
    }   
    return $items; 
}

function getUserEvents($file, $user) {
    if (file_exists($file)) {
        $json_content = file_get_contents($file); 
        $listings = json_decode($json_content, true);
    } else {
        $listings = array();
    }   
    
    $events = []; 

    foreach ($listings as $listing) {
        if ($listing['host'] == $user) {
            $events[] = $listing; 
        }
    }

    return $events; 
}

function convertToMilitaryTime($time) {
    $time_arr = explode(" ", $time); 
    $time_int = (int)$time_arr[0]; 
    $period = $time_arr[1]; 
    
    if ($period == "AM") {
        if ($time_int == 12) {
            return "0";  
        } else {
            return (string)$time_int; 
        }
    } else if ($period == "PM") {
        if ($time_int == 12) {
            return "12";  
        } else {
            return (string)($time_int + 12); 
        }
    }
    
    return (string)$time_int;  
}

// return array of Ids of bookmarked events
function getBookmarkedId($username) {
    $usersFile = '../data/users.json';
    $bookmarkedIds = [];
    
    if (file_exists($usersFile)) {
        $jsonContent = file_get_contents($usersFile);
        $users = json_decode($jsonContent, true);
        
        foreach ($users as $user) {
            if ($user['username'] == $username) {
                $bookmarkedIds = $user['bookmarkedPosts'];
                break;
            }
        }
    }

    return $bookmarkedIds; 
}
?>



