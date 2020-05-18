<?php 
$page_title = 'View Message';

include 'includes/header.php';
require("mysqli_connect.php"); 
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
echo "<h2><a href='messaging.php?box=i'>INBOX</a> | <a href='messaging.php?box=o'>OUTBOX</a> | <a href='send_message.php'>NEW MESSAGE</a></h2>";


if (isset($_GET['box'])) {
	$box = $_GET['box'];
}
else {
	$box = "i";
}


//set user's id for this file
if (isset($_GET['uid'])) {
	$user_id = $_GET['uid'];
	$my_profile = "N";
}
else {
	$user_id = $_SESSION['user_id'];
	$my_profile = "Y";
}

$q = "SELECT * FROM USER WHERE user_id=" . $user_id;
$re = mysqli_query($connection, $q);
$r = mysqli_fetch_array($re);

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$problem = false; // No problems so far.	
	if (empty($_POST['body'])) {
		$problem = true;
	} 
		
	if (!$problem) { // If there weren't any problems...
			
		// Print a message:
		print '<p class="text_success">Your message has been sent!</p>';
	
		//To debug
		//print_r($_POST);

		$sender_id = $_POST['sender_id'];
		$receiver_id = $_POST['receiver_id'];
		$subject = $_POST['subject'];
		$body = $_POST['body'];
		$sql = "INSERT INTO MESSAGE (sender_id, receiver_id, subject, body) VALUES ($sender_id, $receiver_id, '$subject', '$body')";
		$add_newmsg = mysqli_query($connection, $sql);
		echo $sql;		
		header("Location: messaging.php?box=o&msg=ok");	
	} 
	
} // End of handle form IF.
else {
	//To display the content for view message
	if (isset($_GET['id'])) {
		$message_id = $_GET['id'];
	}

	//edited (+ "U2.profile_image")
	if($box=='i') {	
		$query = "SELECT U2.first_name, U2.last_name, MESSAGE.*, U2.profile_image FROM MESSAGE, USER as U1, USER as U2 WHERE MESSAGE.receiver_id = U1.user_id AND MESSAGE.sender_id = U2.user_id AND MESSAGE.receiver_id = " . $_SESSION['user_id'] . " AND message_id = $message_id";
	}	
	
	elseif ($box=='o') {
		$query = "SELECT U1.first_name, U1.last_name, MESSAGE.*, U1.profile_image FROM MESSAGE, USER as U1, USER as U2 WHERE MESSAGE.receiver_id = U1.user_id AND MESSAGE.sender_id = U2.user_id AND MESSAGE.sender_id =  " . $_SESSION['user_id'] . " AND message_id = $message_id";
	}

	$result = mysqli_query($connection, $query);
	$row = mysqli_fetch_array($result);

}


//edited (from HTML to PHP format)
//view message
echo "<h2>VIEW MESSAGE</h2>
	

	<img id='message_view_pic' src='img/";
	
	if ($box == 'i') {
		echo $row['profile_image'];
	}elseif ($box=='o') {
		echo $r['profile_image'];
	}
echo "' alt='Profile Photo Sender'>
	<img id='arrow' src='https://i.ibb.co/86BDT4G/arrow.png' alt=''>
	
	<img id='message_view_pic' src='img/";
	
	if ($box == 'i') {
		echo $r['profile_image'];
	}elseif ($box=='o') {
		echo $row['profile_image'];
	}
echo "' alt='Profile Photo Receiver'>


	<p>SENT BY: ";
	if ($box == 'i') {
		echo "<a href='profile.php?uid=" .$row['sender_id'] . "'>". $row['first_name'] . ' ' . $row['last_name'] ."</a>";
	}elseif ($box=='o') {
		echo "You";
	}
echo "<p>SENT TO: ";
	if ($box == 'i') {
		echo "You";
	}elseif ($box=='o') {
		echo "<a href='profile.php?uid=" .$row['receiver_id'] . "'>". $row['first_name'] . ' ' . $row['last_name'] ."</a>";
	}
echo "</p>";

$formattedDate = date('m/d/Y h:i:s', strtotime('-2 hours,' . $row['create_date']));

echo	"<p>SUBJECT: " . $row['subject'] . "</p>
	<p>MESSAGE: " . $row['body'] . "</p>";
	

	//edited
	// To reply a message
echo "<h2>REPLY</h2>
	<form action='message_view.php' method='post'>
	
	
	<img id='message_view_pic' src='img/";
	if ($box == 'i') {
		echo $r['profile_image'];
	}elseif ($box=='o') {
		echo $r['profile_image'];
	}
echo "' alt='Profile Photo Sender'>
	<img id='arrow' src='https://i.ibb.co/86BDT4G/arrow.png' alt=''>
		<img id='message_view_pic' src='img/";
	if ($box == 'i') {
		echo $row['profile_image'];
	}elseif ($box=='o') {
		echo $row['profile_image'];
	}
echo "' alt='Profile Photo receiver'>
	
	
	<p>REPLY TO: 
	<input type='hidden' readonly name='receiver_id' value='";
	if ($box == 'i') {
		echo $row['sender_id'];}
	elseif ($box=='o') {
		echo $row['receiver_id'];}
	
echo "'>";

	if ($box == 'i') {
		echo "<a href='profile.php?uid=" .$row['sender_id'] . "'>". $row['first_name'] . ' ' . $row['last_name'] ."</a>";
	}
	elseif ($box=='o') {
		echo "<a href='profile.php?uid=" .$row['receiver_id'] . "'>". $row['first_name'] . ' ' . $row['last_name'] ."</a>";
	}
	
 echo "</p>
	<p>SUBJECT: 
	<input class='input' type='text' readonly name='subject' maxlength='150' value='RE: " . $row["subject"] . "'></p>
	<p>MESSAGE:<textarea name='body' placeholder='Type your message here...' value='"; 
	if (isset($_POST['body'])) { 
	print htmlspecialchars($_POST['body']); 
	}
	echo "' required></textarea></p>
	<br>
	<input type='hidden' name='box' value='" . $box . "'>
	<input type='hidden' name='sender_id' value='" . $_SESSION['user_id'] . "'>
	<button id='submit' type='submit' name='send' value='send'>SEND</button>
	</form>";

echo "</div>";


echo "</div>";
include 'includes/footer.php';
?>



