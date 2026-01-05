<html> 
    <head> 
<?php 
include '../php/func.php'; 
session_start();
?> 
        <link rel="stylesheet" href="../css/style.css">
        <script> 
        const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
        ]; 
        var bookmarkedIds = <?php echo json_encode(getBookmarkedId($_SESSION["username"])); ?>;
        </script> 
    </head> 
    <body id="homePageBody">
        <div class="homePageBar"> 
            <h1> Smith Friends </h1> 
            <h3> Welcome, admin <?php echo $_SESSION["username"] ?>!</h3> 
<?php 
            $path = "../Users/" . $_SESSION["username"] . "/";
            $profile_pic = glob($path . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE); 

            if (count($profile_pic) == 0) {
                $pfp = "../Users/defaultPFP.png"; 
            }
            else {
                $pfp = $profile_pic[0]; 

    
            }
?>          
            <a class = "addEventText" href="adminEditPost.html.php">Add/Edit Events </a>
            <a href="adminProfile.html.php">
                <img src="<?php echo $pfp;?>">
            </a>
        </div>
        
        <!-- Content container with 30/70 split -->
        <div class="homePageContentContainer">
            <div class="homePageLeft"> 
                <h2>Your Upcoming Events</h2>
                <!-- Calendar Reference: https://dev.to/wizdomtek/creating-a-dynamic-calendar-using-html-css-and-javascript-29m -->
                <div class="calendar">
                    <div class="calendar-header">
                        <button id="prev-month">‹</button>
                        <div id="month-year"></div>
                            <button id="next-month">›</button>
                        </div>
                        <div class="calendar-body">
                        <div class="calendar-weekdays">
                            <div class="calendar-weekdays">
                                <div>Sun</div>
                                <div>Mon</div>
                                <div>Tue</div>
                                <div>Wed</div>
                                <div>Thu</div>
                                <div>Fri</div>
                                <div>Sat</div>
                            </div>
                        </div>
                        <div class="calendar-dates">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="homePageListing">
                <h3>Find Events</h3> 
                <button class="filter-btn" onclick="getAllListings()">See All Events </button> 
                <button class="filter-btn" onclick="showBookmarked(event)">See Bookmarked</button> 
                <button class="filter-btn" onclick="openModal('modal-location')">Filter Events</button>
                <button class="filter-btn" onclick="openModal('modal-time')">Filter Times</button>
                <div class="listings-grid" id="listingsContainer">
                    No events found :( 
                </div>
            </div>
        </div>

        <div class="modal" id="modal-location">
            <div class="modal-content">
                <span class="close" onclick="closeModal('modal-location', 'filterForm')">&times;</span>
                <h2>Filter Location</h2>
               <form id="filterForm" onsubmit="filterLocations(event)">
                    <label for="location">Choose Location: </label>
                    <select id="loc" name="filter-location">
                        <option value="Any">Any</option>
                    </select>
                    <input type="submit" value="Apply">
                </form>
            </div>
        </div>

        <div class="modal" id="modal-time">
            <div class="modal-content">
                <span class="close" onclick="closeModal('modal-time', 'timeForm')">&times;</span>
                <h2>Filter Time</h2>
                <form id="timeForm" onsubmit="filterTimes(event)">
                    <label>Choose start time: </label>
                    <select id="starttime">
                        <option value="Any">Any</option>
                    </select>
                    <label>Choose end time: </label>
                    <select id="endtime">
                        <option value="Any">Any</option>
                    </select>
                    <input type="submit" value="Apply">
                </form>
            </div>
        </div>

        
    <script src="../js/script.js"></script>
    <script>    
        var bookmarkedIds = <?php echo json_encode(getBookmarkedId($_SESSION["username"])); ?>;
        const calendarDates = document.querySelector('.calendar-dates');
        const monthYear = document.getElementById('month-year');
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');

        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        });

        nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
        });

        calendarDates.addEventListener('click', (e) => {
            if (e.target.textContent !== '') {
                filterDates(e); 
            }
});     

        function showBookmarked(e) {
            e.preventDefault(); 
            // Get all events
            const events = <?php echo json_encode(getEvents("../data/listing.json")); ?>; 
            const bookmarkedEvents = events.filter(event => 
        bookmarkedIds.includes(event.id) || bookmarkedIds.includes(String(event.id))
    );

    
            const container = document.getElementById('listingsContainer'); 
            if (bookmarkedEvents.length == 0) {
                container.innerHTML = "<h5> No Events Happening :( </h5>"; 
            }
            else {
                updateListings(bookmarkedEvents, container, bookmarkedIds); 
                var bookmarks = document.getElementsByClassName("unbookmark-btn"); 
            }
        }

        function filterDates(e) {
            e.preventDefault(); 
            const events = <?php echo json_encode(getEvents("../data/listing.json")); ?>; 
            const container = document.getElementById('listingsContainer'); 

            var day = String(e.target.textContent); 
            var month = String(currentMonth + 1); 
            var year = String(currentYear);
            var selectedDate = year + "-" + month + "-" + day; 
            
            dated_events = []; 
            for (const event of events) {
                var eventDate = event.date;

                if (eventDate == selectedDate) {
                    dated_events.push(event); 
                }
            }
            
            if (dated_events.length == 0) {
                container.innerHTML = "<h5> No Events Happening :( </h5>"; 
            }
            else {
                updateListings(dated_events, container, bookmarkedIds); 
            }
        }
    
        function filterTimes(e) {
            e.preventDefault(); 
            const events = <?php echo json_encode(getEvents("../data/listing.json")); ?>; 
            const container = document.getElementById('listingsContainer');

            var startTime_str = document.getElementById("starttime").value; 
            var endTime_str = document.getElementById("endtime").value; 


            filtered_events = []; 
            if (startTime_str == "Any") {
                if (endTime_str == "Any") {
                    getAllListings(); 
                    return 
                }
                else {
                    var endTime_int = convertTo24Hour(endTime_str); 

                    for (const event of events) {
                        //look through each end time for each event and check if it occurs before the selected end Time
                        end_str = event['end-time']; 
                        end_arr = end_str.split(':'); 
                        end_int = parseInt(end_arr[0]); 
                        if (end_int <= endTime_int) {
                            filtered_events.push(event); 
                        }
                    }
                }
            } else {
                if (endTime_str == "Any") {
                    var startTime_int = convertTo24Hour(startTime_str); 

                    for (const event of events) {
                        //look through each end time for each event and check if it occurs before the selected end Time
                        start_str = event['start-time']; 
                        start_arr = start_str.split(':'); 
                        start_int = parseInt(start_arr[0]); 
                        if (start_int >= startTime_int) {
                            filtered_events.push(event); 
                        }
                    }
                }
                else {
                    var startTime_int = convertTo24Hour(startTime_str); 
                    var endTime_int = convertTo24Hour(endTime_str); 

                    for (const event of events) {
                        //look through each end time for each event and check if it occurs before the selected end Time
                        start_str = event['start-time']; 
                        start_arr = start_str.split(':'); 
                        start_int = parseInt(start_arr[0]); 

                        end_str = event['end-time']; 
                        end_arr = end_str.split(':'); 
                        end_int = parseInt(end_arr[0]); 

                        if ((start_int >= startTime_int) && (end_int <= endTime_int)) {
                            filtered_events.push(event); 
                        }
                    }
                }

                if (filtered_events.length == 0) {
                        container.innerHTML = "<h5> No Event Happening :( </h5>"; 
                 }
                else {
                    updateListings(filtered_events , container, bookmarkedIds); 
                }
            }
        }

        function filterLocations(e) {
            e.preventDefault(); //prevents a reload so the function can process the code. 
            <?php 
            if (file_exists("../data/listing.json")) {
                $json_content = file_get_contents("../data/listing.json"); 
                $items = json_decode($json_content, true);
            } else {
                $items = array();
            }   
?>
            const events = <?php echo json_encode($items); ?>; 
            var dropdown = document.getElementById("loc"); 
            var filteredLocation = dropdown.value; 

            if (filteredLocation == "Any") {
                getAllListings(); 
            }
            else {
                var location_events = []; 
                for (const event of events) {
                    if (event.location == filteredLocation) {
                        location_events.push(event); 
                    }
                }

                const container = document.getElementById('listingsContainer');

                // Clear existing content
                container.innerHTML = '';
                updateListings(location_events, container, bookmarkedIds); 
            }
        }

        function populateLocationFilter() {
            var locations = <?php echo json_encode(findAllLocations("../data/listing.json")); ?>;
            var selection = document.getElementById("loc"); 

            locations.forEach(location => {
                var opt = document.createElement('option'); 
                opt.value = location; 
                opt.textContent = location; 
                selection.appendChild(opt); 
            }); 
        }

        function getAllListings() {
<?php 
            if (file_exists("../data/listing.json")) {
                $json_content = file_get_contents("../data/listing.json"); 
                $items = json_decode($json_content, true);
            } else {
                $items = array();
            }   
?>
            const events = <?php echo json_encode($items); ?>; 
            
            const container = document.getElementById('listingsContainer');
            
            // Clear existing content
            container.innerHTML = '';

            updateListings(events, container, bookmarkedIds); 
        }
        
        getAllListings();
        populateLocationFilter(); 
        populateTimeFilter("starttime", "endtime"); 
        renderCalendar(currentMonth, currentYear);
    </script>
    </body> 
</html>