<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/style/home_style.css">
    <link rel="stylesheet" href="assets/style/messages.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/Tweeter_icon.png"/>
    <title>Document</title>
</head>
<body>

<?php
include_once "header.html";
?>

<div id="messages_outer_wrapper">
    <div id="messages_inner_wrapper">
        <h1 id="drct_msgs">Direct Messages</h1>
        <i class="fa fa-times fa-5x" aria-hidden="true" id="msgsX"
           style="color: black; font-size: 25px; margin-top:10px; margin-right: 10px; float: right"></i>
        <div id="newMsg" onclick="newMsg()">New Message</div>
        <div style="clear: both"></div>
        <div id="msgWrap"></div>
        <div id="newMsgDiv">

            <form method="post" action="../commandPattern.php?target=message&action=addMessage"
                  enctype="multipart/form-data">
                <input type="text" id="msgUserSearch" placeholder="Choose who to send a message to!" name="receiverName"
                       required>
                <textarea id="msgInput" name="text" oninput="characters(this)"></textarea>
                Send Picture <input type="file" name="message_img">

                <input type="submit" value="Send" id="sendMsgButton">
                <b style="float: right; margin-right: 10px; margin-top: 5px;"><i id="counter">0</i>/300</b>
            </form>

        </div>
    </div>
</div>
<div id="tags_mid_div">

</div>
<script src="assets/js/home_hashtags.js">
    
</script>
</body>

</html>
