<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/Tweeter_icon.png"/>
    <link rel="stylesheet" href="assets/style/profile_style.css">
    <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="assets/style/messages.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style/home_style.css">
</head>
<body>
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
<div id="edit_outer_wrapper">
    <div id="edit_inner_wrapper">
        <h1 id="edit_profile1">Редактиране на профила</h1>
        <i class="fa fa-times fa-5x" aria-hidden="true" id="msgsY"
           style="color: black; font-size: 25px; margin-top:10px; margin-right: 10px; float: right"></i>
        <div id="profile_edit">
            <form method="post" action="../commandPattern.php?target=user&action=editProfile"
                  enctype="multipart/form-data">
                <table>
                    <tr>
                        <td>Име:</td>
                        <td><input type="text" name="username" id="username" required></td>
                    </tr>
                    <tr>
                        <td>Имейл:</td>
                        <td><input type="email" name="email" id="email" required></td>
                    </tr>
                    <tr>
                        <td>Град:</td>
                        <td><input type="text" name="city" id="city" placeholder="Град"></td>
                    </tr>
                    <tr>
                        <td>Описание:</td>
                        <td><textarea rows="5" cols="26" name="description" id="description"
                                      placeholder="Кратко описание.."></textarea></td>
                    </tr>
                    <tr>
                        <td>Парола:</td>
                        <td><input type="password" name="password" id="password" placeholder="Въведете парола" required></td>
                    </tr>
                    <tr>
                        <td>Профилна снимка:</td>
                        <td><input type="file" name="user_pic" class="file" value="Profile picture"></td>
                    </tr>
                    <tr>
                        <td>Снимка за корица:</td>
                        <td><input type="file" name="user_cover" class="file"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Edit" id="btn_edit" name="btn_edit"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php
include_once "page_lock.php";
include_once "header.html";
?>
<div id="cover">
    <img id="cover_img" src="" alt="">
</div>
<nav id="my_nav">
    <div class="in_my_nav" id="first_in_my_nav">
        <ul>
            <li><a href="#" id="following" onclick="showFollowing()">Следва<span id="sledva"></span></a></li>
            <li><a href="#" id="followers" onclick="showFollowers()">Последователи<span id="sledvat"></span></a></li>
            <li><a href="#" id="twits" onclick="showTwits()">Туитове<span id="tuitove"></span></a></li>
        </ul>
        <div id="profile_card">
            <div id="nav_image">
                <img src="" id="nav_img">
            </div>
            <a id="nav_name" href="#">@Georgi</a>
        </div>
</nav>
<div class="in_my_nav">
    <div id="circle">
        <img id="circle_img" src="">
    </div>
</div>


<div id="main">
    <div id="user_info">
        <h1 id="name"><a></a></h1>
        <h2 id="name_"><a></a></h2>
        <p id="descriptionid"></p>
        <div id="cityid">
            <i class="fa fa-map-marker" aria-hidden="true"></i>

        </div>
        <div id="reg_date">
            <i class="fa fa-calendar" aria-hidden="true"></i>

        </div>
        <div id="emailid">
            <i class="fa fa-envelope-o" aria-hidden="true"></i>

        </div>
    </div>

    <div id="center_tweet">

    </div>

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

<script src="assets/js/profile.js">

</script>
</body>
</html>
