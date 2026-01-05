<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./css/style.css">
    <script> 
    var NRPTS = 10;
      const colors = [
        "blue", 
        "rgb(224, 200, 82)"
      ];
      const pts = [];
      var interval = null;
      var count = 0;
      var ctx = null; 
      </script>
</head>
<body class="loginBody">
    <div class="login-container">
        <canvas
      id="myCanvas"
      width="260"
      height="150"
    ></canvas>
        <h2>Smith Friends</h2>
        <form action="./html/lib.html.php" method="post">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        
        <button class="register-btn" onclick="openModal()">Register New Account</button>
    </div>

    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Register</h2>
            
            <div id="messageBox"></div>
            
            <form id="registerForm" onsubmit="return registerUser(event)">
                <label>Username:</label>
                <input type="text" id="regUsername" name="username" required>
                
                <label>Password:</label>
                <input type="password" id="regPassword" name="password" required>

                <label> Location: </label>
                <input type="text" id="regLocation" name="location" required> 
                        
                <input type="submit" value="Register">
            </form>
        </div>
    </div>
    <script src="./js/script.js"></script> 
    <script>
        function openModal() {
            document.getElementById('registerModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('registerModal').style.display = 'none';
            document.getElementById('registerForm').reset();
            document.getElementById('messageBox').innerHTML = '';
        }
        
        window.onclick = function(event) {
            var modal = document.getElementById('registerModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Register user function
        function registerUser(event) {
            event.preventDefault();
            
            var username = document.getElementById('regUsername').value.trim();
            var password = document.getElementById('regPassword').value;
            var location = document.getElementById('regLocation').value; 
            
            document.getElementById('messageBox').innerHTML = '';
            
            
            // Validate username and password not empty
            if (username == '' || password == '' || location == '') {
                showMessage('Fields cannot be empty!', 'error');
                return false;
            }
            
            // Send AJAX request to register
            var xmlhttp = new XMLHttpRequest();
            
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log("Response:", this.responseText);
                    
                    try {
                        var response = JSON.parse(this.responseText);
                        
                        if (response.success) {
                            showMessage(response.message, 'success');
                            
                            // Close modal after 2 seconds and reload
                            setTimeout(function() {
                                closeModal();
                            }, 1500);
                        } else {
                            showMessage(response.message, 'error');
                        }
                    } catch (e) {
                        showMessage('Error: ' + e.message, 'error');
                    }
                }
            };
            
            xmlhttp.open("POST", "./php/registerUser.php", true);
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password) + "&location="+encodeURIComponent(location));
            
            return false;
        }

        // Show message in the modal
        function showMessage(message, type) {
            var messageBox = document.getElementById('messageBox');
            messageBox.innerHTML = '<div class="message ' + type + '">' + message + '</div>';
        }

        window.addEventListener('DOMContentLoaded', function() {

    var c = document.getElementById("myCanvas"); 
    ctx = c.getContext("2d");
    ctx.clearRect(0, 0, 260, 150); 
    createCircles(); 
    moveCircles(); 
});
    </script>
</body>
</html>