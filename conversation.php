<?php
//messaging landing page listing list of chats
$page_title = 'Conversation';

include('includes/header.php');
require('mysqli_connect.php'); // use require because we want to force this to exist before running our queries
require('session_check.php'); 		
		
echo "<div class='flex-container container'>";

echo "<div class='user-list'>";
$q = "SELECT DISTINCT FOLLOW.user_id_to_follow, USER.first_name, USER.last_name
            FROM USER, FOLLOW
            WHERE USER.user_id = FOLLOW.user_id_to_follow AND 
            FOLLOW.user_id = " . $_SESSION['user_id'] . "
            ORDER BY USER.last_name, USER.first_name";
$re = mysqli_query($connection, $q);

if ($r == 0) //if statement
    { echo "<h2>VIEW CONVERSATION</h2>
    <h2>Sorry, you need to follow users before you can any conversation.</h2>";} 
        
    else { 

    //READ
    echo "<table><thead><td class='center'>VIEW CONVERSATION</td></thead>"; 
    // open table and include table headings

    //READ
    while ($r = mysqli_fetch_assoc($re)) {
    echo "<tr><td class='center'><a href='conversation.php?id=" . $r['user_id'] . "' class='link'>" . $r['first_name'] . " " . $r['last_name'] . "</a></td></tr>";
    }
    echo "</table>"; // close table
    }
echo "</div>";








		
echo "<div class='messages'>";
echo "<h5><a href='user_home.php'>HOME</a> > MESSAGING</h5>";   
echo "<h2><a href='messaging.php?box=i'>INBOX</a> | OUTBOX | <a href='send_message.php'>NEW MESSAGE</a></h2>";

echo "<h2>Chat</h2>";

if(isset($_GET['user_id'])) {
    //if msg exists, then create feedback
    $user_id = $_GET['user_id'];
	echo "<h4>Chatting.</h4>";
} 
		
//And now to perform a simple query to make sure it's working
//$getUsers = "SELECT U2.first_name, U2.last_name, MESSAGE.* FROM MESSAGE ORDER BY user_id DESC";
//$userResults = mysqli_query($connection, $getUsers);

$query = "SELECT USER.first_name AS myfirstname, USER.last_name AS mylastname, MESSAGE.* FROM USER, MESSAGE WHERE USER.user_id = MESSAGE.sender_id AND (USER.user_id = ". $user_id . " OR USER.user_id = ". $_SESSION['user_id']. ") AND (MESSAGE.receiver_id = ". $_SESSION['user_id']. " OR MESSAGE.receiver_id = ". $user_id . ") AND (MESSAGE.sender_id = ". $_SESSION['user_id']. " OR MESSAGE.sender_id = ". $user_id . ") ORDER BY MESSAGE.create_date";
$result = mysqli_query($connection, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $formattedDate = date('m/d/Y h:i:s', strtotime('-2 hours,' . $row['create_date']));
// check to see if this is ME or not - no need to output your own name, but profile_image might be nice
    if ($row['sender_id'] == $_SESSION['user_id']) {
        echo "<div class='chat_left'><p>" . $formattedDate . "<br>" . $row['subject'] . "<br>";
        echo $row['body'] . "</p></div>";
    }
    // it is someone else - display on the right side of the page?
    else {
        echo "<div class='chat_right'><p>" . $formattedDate . "<br>" . "<a href='profile.php?uid=" . $row['sender_id'] . "'>". $row['myfirstname'] . " " . $row['mylastname'] . "</a><br>" . $row['subject'] . "<br>";
        echo $row['body'] . "</p>
        </div>";
    }
           
}


echo "</div>";
echo "</div>";
include('includes/footer.php');
?>