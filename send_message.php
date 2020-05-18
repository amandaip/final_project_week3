<?php 

//messaging landing page listing list of chats
$page_title = 'Send A Message';

include('includes/header.php');
require('mysqli_connect.php'); 
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
        print_r($_POST);
    
        $sender_id = $_SESSION['user_id'];
        $receiver_id = $_POST['receiver_id'];
        $subject = $_POST['subject'];
        $body = $_POST['body'];
        $sql = "INSERT INTO MESSAGE (sender_id, receiver_id, subject, body,) VALUES ($sender_id, $receiver_id, '$subject', '$body')";
        $add_newmsg = mysqli_query($connection, $sql);
        //echo $sql;
        header("Location: messaging.php?box=o&msg=ok");	
    } 
        
} // End of handle form IF.
else {

    $query = "SELECT DISTINCT FOLLOW.user_id_to_follow, USER.first_name, USER.last_name
            FROM USER, FOLLOW
            WHERE USER.user_id = FOLLOW.user_id_to_follow AND 
            FOLLOW.user_id = " . $_SESSION['user_id'] . "
            ORDER BY USER.last_name, USER.first_name";
    $result = mysqli_query($connection, $query);
    // echo $query;
    $row_cnt = mysqli_num_rows($result); //row count
    if ($row_cnt == 0) //if statement
        { echo "<h2>Sorry, you need to follow users before you can message them.</h2>";} 
        
    else { 


        echo "<h1>New Message</h1>
            <form action='send_message.php' method='post'>	
            <p>Send to: 
            <select class='input' name='reciever_id'>";
           
            while($row = mysqli_fetch_assoc($result)) {
                echo "<option value = '" . $row["user_id"]. "'>" . $row["first_name"]. " " . $row["last_name"]. "</option>";
            }
            echo "</select>
            <p>Subject: <input class='input' type='text' name='subject' maxlength='150'></p>
            <p>Message:<textarea name='body' placeholder='Type your message here...'  required></textarea></p>
            <p>
            <button id='submit' type='submit' name='send'>SEND</button>
            </form>";
    }

}
echo "</div>";



echo "</div>";

include('includes/footer.php');

?>
