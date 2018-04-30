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
<script>
    var queryString = decodeURIComponent(window.location.search);
    queryString = queryString.substring(1);
    var queries = queryString.split("&");

    console.log(queries[0]);
    var request = new XMLHttpRequest();
    request.open("GET", "../commandPattern.php?tag=" + queries[0] + "&target=twit&action=displayTags");
    request.onreadystatechange = function (ev) {
        if (this.status == 200 && this.readyState == 4) {
            var resp = JSON.parse(this.responseText);
            console.log(resp);
            var div = document.getElementById("tags_mid_div");
            for (var i = 0; i < resp.length; i++) {
                var tweet = document.createElement("div");
                tweet.className = "ownTweets";
                var link_name = resp[i]["user_name"];
                link_name = link_name.replace(" ", "%20");

//                adding the data into the tweet
                tweet.innerHTML = "<a href=" + "profile.php?" + link_name + "><img class='home_tweet_image' src=" + resp[i]["user_pic"] + "></a>";
                tweet.innerHTML += "<h1 class='tweet_name'><a onmouseover='info(this)' onmouseout='hide1()' href=profile.php?" + link_name + ">" + resp[i]["user_name"] + "</a></h1>";
                tweet.innerHTML += "<h4 class='tweet_date'>" + resp[i]["twat_date"] + "</h4>";
                tweet.innerHTML += "<p class='content' style='border: 0'>" + resp[i]["twat_content"]+"<br>" + "</p>";
                tweet.innerHTML += "<i id='heart" + resp[i]['twat_id'] + "' value=" + resp[i]['twat_id'] + " onclick=\"likeTweet(" + resp[i]['twat_id'] + ",this.id)\" class=\"fa fa-heart hrt\"></i>";
                if (resp[i]['twat_img'] != null) {
                    tweet.innerHTML += '<br>';
                    tweet.innerHTML += '<img onclick="displayPicture(this)" style="height: 280px; width: auto; max-width: 500px; margin-right: 20px; border-radius: 5px;" src="' + resp[i]['twat_img'] + '">';
                }
                var comment_div = document.createElement('div');
                comment_div.className = "home_tweet_comments";
                comment_div.id = resp[i]['twat_id'];
                div.appendChild(tweet);
                div.appendChild(comment_div);


//                adding comments to the tweets
                test(resp[i]['twat_id']);

            }
        }
    };
    request.send();



    function msgs() {
        window.scrollTo(500, 0);
        var messages = document.getElementById("messages_outer_wrapper");
        messages.style.visibility = "visible";
        var body = document.getElementsByTagName("BODY")[0];
        body.style.overflow = "hidden";
        var msgsX = document.getElementById('msgsX');
        msgsX.addEventListener('click', function () {
            messages.style.visibility = "hidden";
            body.style.overflow = "scroll";
            var msgWrap = document.getElementById('msgWrap');
            msgWrap.style.display = 'block';
            var newMsgDiv = document.getElementById('newMsgDiv');
            newMsgDiv.style.display = 'none';

        });
//        TODO ADD THE MESSAGES


        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?target=message&action=getMessages");
        request.onreadystatechange = function (ev) {
            if (this.status == 200 && this.readyState == 4) {
                var response = JSON.parse(this.responseText);
                var wrap = document.getElementById('msgWrap');
                wrap.innerHTML = '';

                for (var i = 0; i < response.length - 1; i++) {
                    var msg = document.createElement('div');  //creating message div
                    msg.className = 'msgs_in_wrap';
                    var sender = response[response.length - 1][response[i]['message_id']]["sender"][0]["user_name"];
                    var receiver = response[response.length - 1][response[i]['message_id']]["receiver"][0]["user_name"]
                    msg.innerHTML = '<h3>From <a href="" style="color: #006dbf">' + sender + '</a> to <a href="" style="color: #006dbf">' + receiver + '</a> on ' + response[i]['message_date'] + ' </h3>';
                    msg.innerHTML += response[i]['message_text'];
                    if (response[i]['message_img'] != null) {
                        msg.innerHTML += '<br>';
                        msg.innerHTML += '<img style="height: 300px; width: auto;" src="' + response[i]['message_img'] + '">';
                    }

                    wrap.appendChild(msg);
                }


            }
        };
        request.send();


//        TODO SEND MESSAGES
    }

    function newMsg() {
        var msgWrap = document.getElementById('msgWrap');
        msgWrap.style.display = 'none';
        var newMsgDiv = document.getElementById('newMsgDiv');
        newMsgDiv.style.display = 'block';

    }

    // function displayPicture(el) {
    //     alert(el.src);
    //     var div = document.createElement('div');
    //     div.style.height='100vh';
    //     div.style.width='100%';
    //     div.style.position='relative';
    //     div.style.zIndex="1100";
    //     div.style.backgroundColor='rgba(0,0,0,0.2)';
    //     var img = document.createElement('img');
    //     img.style.width='70%';
    //     img.src=el.src;
    //
    //
    //     div.appendChild(img);
    //     document.body.appendChild(div);
    //
    // }


    function test(id) {
        var div = document.getElementById(id);
        var cmnt_box = document.createElement("input");
        var btn = document.createElement("button");
        btn.innerHTML = "Коментирай";
        btn.className = "cmnt_btn";
        btn.value = id;

        btn.addEventListener("click", function () {
            var request3 = new XMLHttpRequest();
            request3.open("post", "../commandPattern.php?target=comment&action=postComment");
            request3.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request3.onreadystatechange = function (ev) {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    if (response == "1") {
                        test(id);
                    }

                }
            };
            request3.send("content=" + document.getElementById("asd" + this.value).value + "&tweetId=" + this.value);

        }, true);
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?tweet_id=" + id + "&target=comment&action=showMyTweetComment");
        request.onreadystatechange = function (ev) {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);

                cmnt_box.type = "text";
                cmnt_box.className = "cmnt_box_home";
                cmnt_box.id = "asd" + id;
                cmnt_box.placeholder = "Постави твоя коментар";
                div.innerHTML = "";
                div.appendChild(cmnt_box);
                div.appendChild(btn);
                if (response.length > 0) {
                    for (var i = 0; i < response.length; i++) {
//                        div.innerHTML += response[i]["comment_text"]+"<br>";

                        var cmnt_div = document.createElement('div');
                        cmnt_div.className = "commentss";
                        var link_name = response[i]["user_name"];
                        link_name = link_name.replace(" ", "%20");

                        cmnt_div.innerHTML = "<a onmouseover='info(this)' onmouseout='hide1()' href=" + "profile.php?" + link_name + "><img class='home_comment_image' src=" + response[i]["user_pic"] + "></a>";
                        cmnt_div.innerHTML += "<h1 class='tweet_name'><a href=profile.php?" + link_name + ">" + response[i]["user_name"] + "</a></h1>";
                        cmnt_div.innerHTML += "<h4 class='tweet_date'>" + response[i]["comment_date"] + "</h4>";
                        cmnt_div.innerHTML += "<p class='content' style='border: 0'>" + response[i]["comment_text"] + "</p>";

                        div.appendChild(cmnt_div);


                    }

                }
                else {
                    div.innerHTML += "<br><h3 style='margin-left:70px;'>No comments on this tweet</h3>";
                }
            }
            else {
                comments = "fail";
            }
        };
        request.send();
    }

    function likeTweet(id, tweet_id) {
        var like = document.getElementById(tweet_id);
        like.style.color = 'red';
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?twat_id=" + id + "&target=twit&action=likeTweet");
        request.onreadystatechange = function (ev) {
            if (this.readyState == 4 && this.status == 200) {
            }
        };
        request.send();
    }

    function info(pole) {
        var user_name = pole.innerText;
        link_name = user_name.replace(" ", "%20");
        if (link_name.charAt(0) == "@") {
            link_name = link_name.slice(1);
        }
        console.log(link_name);

        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?name=" + link_name + "&target=user&action=getInfoForTweets");
        request.onreadystatechange = function (ev) {
            if (this.status == 200 && this.readyState == 4) {
                var response = JSON.parse(this.responseText);

                var cover = document.getElementById("small_cover_image");
                cover.src = response[0]['user_cover'];
                var image = document.getElementById("small_image");
                image.src = response[0]['user_pic'];
                var name = document.getElementById("small_h1");
                name.innerText = response[0]['user_name'];
                var small_name = document.getElementById("small_h4");
                small_name.innerText = "@" + response[0]['user_name'];
                var asd = document.getElementById("opisanie");
                asd.innerHTML = response[0]['user_description'];
            }
        };
        request.send();

        var request2 = new XMLHttpRequest();
        request2.open("GET", "../commandPattern.php?name=" + link_name + "&target=user&action=showSmallDiv");
        request2.onreadystatechange = function (ev) {
            if (this.status == 200 && this.readyState == 4) {
                var response = JSON.parse(this.responseText);

                var first = document.getElementById("one");
                var second = document.getElementById("two");
                var third = document.getElementById("three");

                first.innerHTML = response[0][0]['num'];
                second.innerHTML = response[1][0]['num'];
                third.innerHTML = response[2][0]['num'];
            }
        };
        request2.send();
        /*Определя се позицията на която да се покаже прозореца с допълнителната информация*/
        var posx = 0;
        var posy = 0;
        if (!e) var e = window.event;
        if (e.pageX || e.pageY) {
            posx = e.pageX;
            posy = e.pageY;
        }
        else if (e.clientX || e.clientY) {
            posx = e.clientX;
            posy = e.clientY;
        }
        var position = document.getElementById("position_div");
        position.style.visibility = "visible";
        position.style.left = posx + "px";
        position.style.top = posy + "px";
        position.style.display = "block";
        position.style.opacity = "1";
        position.style.transition = "opacity 1.25s linear";
    }

    function hide1() {
        var position = document.getElementById("position_div");
        position.style.visibility = "hidden";
    }

    function postt() {
        var request = new XMLHttpRequest();
        request.open("post", "../commandPattern.php?target=comment&action=postComment");
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.onreadystatechange = function (ev) {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response == "1") {
                    test(id);
                }

            }
        };
        request.send("content=" + document.getElementById("asd" + this.value).value + "&tweetId=" + this.value);

    }

</script>
</html>
</script>
</body>
</html>
