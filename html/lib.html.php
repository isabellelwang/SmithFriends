<html>
<body>

<?php
session_start();
include '../php/func.php';

$seen = validate_login("../data/Users.json", $_POST['username'], $_POST['password']);
$curr_user = $_POST['username'];

if ($seen) {
    $_SESSION['username'] = $curr_user;
    
    $page = ""; 
    if (check_admin("../data/Users.json", $curr_user)) {
        $page = "../html/adminHome.html.php";
    } else {        
        $page = "../html/userHome.html.php";
    }
} else {
    $page = "../login.html.php"; 
}
?>
<script> 

var page = <?php echo json_encode($page); ?>;
window.location.href = page;

</script> 

</body>
</html>


