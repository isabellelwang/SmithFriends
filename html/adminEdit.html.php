<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            border-collapse: collapse;
        }
    </style>
    <link rel="stylesheet" href="../css/style.css">
    <?php 
    // Read users.json at the TOP before any HTML/JS
    if (file_exists("../data/users.json")) {
        $x = file_get_contents("../data/users.json"); 
        $items = json_decode($x, true);
    } else {
        $items = array();
    }

    $usersList = array();
    foreach ($items as $userData) {
        $usersList[] = $userData;
    }
    ?> 
    <script> 
     var arr  = <?php echo json_encode($usersList); ?>;
    </script>
</head>
<body id="editUserBody">
    <h1>Admin Dashboard</h1>
    <a href="adminProfile.html.php">Return to Profile Page</a>
    
    <script src="../js/script.js"></script>
    
    <script>
        // Initialize list from PHP - this must come AFTER PHP code runs
        var list = <?php echo json_encode($usersList); ?>;
    
        // Initial table creation
        makeUserTable(arr); 
    </script>
</body>
</html>