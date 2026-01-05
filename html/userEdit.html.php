<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Profile Dashboard</title>
    <style>
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            border-collapse: collapse;
        }
    </style>
    <link rel="stylesheet" href="../css/style.css">
    <?php 
    session_start(); 
    // Read users.json at the TOP before any HTML/JS
    if (file_exists("../data/users.json")) {
        $x = file_get_contents("../data/users.json"); 
        $items = json_decode($x, true);
    } else {
        $items = array();
    }

    $usersList = array();
    foreach ($items as $userData) {
        if ($userData['username'] == $_SESSION['username']) {
            $usersList[] = $userData;
        }
    }
    ?> 
    <script> 
     var arr  = <?php echo json_encode($usersList); ?>;
    </script>
</head>
<body id="editUserBody">
    <h1>User Profile Dashboard</h1>
    <a href="userProfile.html.php">Return to Profile</a>
    
    <script src="../js/script.js"></script>
    
    <script>
        // Initialize list from PHP - this must come AFTER PHP code runs
        var list = <?php echo json_encode($usersList); ?>;
    
        // Initial table creation
        makeUserTable(arr); 
    </script>
</body>
</html>