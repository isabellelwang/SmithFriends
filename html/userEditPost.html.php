<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="../css/style.css">
    <?php session_start(); ?> 
</head>
<body>
    <a href="userHome.html.php">Return to Home Page</a>
    <div id="messageBox" class="messageBox"></div>
    
    <div class="create-events">
        <h2>Create Events!</h2>
        
        <form id="addForm" 
              action="../php/createListing.php" 
              method="POST" 
              enctype="multipart/form-data">
            
            <label>Event Name:</label>
            <input type="text" id="eventName" name="eventName" required>
            
            <label>Event Location:</label>
            <input type="text" id="eventLocation" name="eventLocation" required>
            
            <label>Date of Event:</label>
            <input type="date" id="eventDate" name="eventDate" required>
            
            <label>Choose start time:</label>
            <select id="eventStartTime" name="eventStartTime" required>
                <option value="Any">Any</option>
     
            </select>
            
            <label>Choose end time:</label>
            <select id="eventEndTime" name="eventEndTime" required>
                <option value="Any">Any</option>
            </select>
            
            <label>Upload Image:</label>
            <input type="file" id="eventImage" name="eventImage" accept="image/*">
            
            <label>Description:</label>
            <textarea id="eventDescription" name="eventDescription" placeholder="Enter event description..."></textarea>
            
            <input type="submit" id="submitBtn" value="Create Event">
        </form>
    </div>
    
    <h3 class="createEventText"> See Your Created Events </h3>
    <div id="listingTable">
    </div> 

    <div class="modal" id="edit-modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('edit-modal', 'editForm')">&times;</span>
                <h2>Filter Time</h2>
            <form id="editForm" 
              action="../php/editEvent.php" 
              method="POST" 
              enctype="multipart/form-data">
            <input type="hidden" id="hiddenID" name="hiddenID">
            
            <label>Event Name:</label>
            <input type="text" id="editName" name="editName">
            
            <label>Event Location:</label>
            <input type="text" id="editLocation" name="editLocation">
            
            <label>Date of Event:</label>
            <input type="date" id="editDate" name="editDate">
            
            <label>Choose start time:</label>
            <select id="editStartTime" name="editStartTime">
                <option value="Any">Any</option>
            </select>
            
            <label>Choose end time:</label>
            <select id="editEndTime" name="editEndTime">
                <option value="Any">Any</option>
            </select>
            
            <label>Upload Image:</label>
            <input type="file" id="editImage" name="editImage" accept="image/*">
            
            <label>Description:</label>
            <textarea id="editDescription" name="editDescription" placeholder="Enter event description..."></textarea>
            
            <input type="submit" id="submitBtn" value="Edit Event">
        </form>
            </div>
        </div>
    <script src="../js/script.js"></script>
    <script>
        var listings = <?php 
            if (file_exists("../data/listing.json")) {
                $json_content = file_get_contents("../data/listing.json"); 
                $items = json_decode($json_content, true);
            } else {
                $items = array();
            }   
            echo json_encode($items); ?>;

        var user = <?php echo json_encode($_SESSION["username"]) ?>; 
        populateTimeFilter("editStartTime", "editEndTime"); 
        populateTimeFilter("eventStartTime", "eventEndTime"); 

        makeUserListingTable(listings, user); 

    </script>
</body>
</html>