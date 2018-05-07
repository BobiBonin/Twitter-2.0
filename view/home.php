<?php
include_once 'page_lock.php';
?>
<!--24.03.2017 Boris-->
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Home</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/Tweeter_icon.png"/>
    <link rel="stylesheet" href="assets/style/home_style.css">
    <link href="assets/style/messages.css" rel="stylesheet">
    <link href="assets/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body onmouseover="hide1()">
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
<?php
include "header.html";
?>
<div class="home_wrap" onclick="hideNotif()">
    <div id="home_left_div" >
        <?php
        include "home_profile.html";
        ?>
    </div>


    <div id="home_mid_div">
        <?php
        include "compose_twat.html";
        include_once "displayTweets.html";
        ?>
    </div>


    <div id="home_right_div">
        <div id="random_users">
            <div id="who_to_follow">
                <h1>Кого да следваш</h1><a href="#" onclick="random()">.Oбновяване.</a>
            </div>
            <div id="randoms">

            </div>
        </div>
        <div id="position_div">
            <div id="small_cover">
                <img id="small_cover_image" src="" alt="">
            </div>
            <div id="small_profile_pic">
                <img id="small_image" src="" alt="">
            </div>

            <div id="small_name">
                <h1 id="small_h1"></h1>
                <h4 id="small_h4"></h4>
            </div>
            <div id="small_description">
                <h5 id="opisanie"></h5>
            </div>
            <div id="small_info">
                <div class="info">
                    <h1>Следва</h1>
                    <span id="one"></span>
                </div>
                <div class="info">
                    <h1>Последователи</h1>
                    <span id="two"></span>
                </div>
                <div class="info">
                    <h1>Туитове</h1>
                    <span id="three"></span>
                </div>

            </div>
        </div>
    </div>
</div>


</body>
<script src="assets/js/home.js"></script>
</html>

