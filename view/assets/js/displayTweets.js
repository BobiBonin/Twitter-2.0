var req = new XMLHttpRequest();
req.open("get", "../commandPattern.php?target=twit&action=displayTweets");
req.onreadystatechange = function (ev) {
    if (this.status == 200 && this.readyState == 4) {
        var resp = this.responseText;
        resp = JSON.parse(resp);

        var div = document.getElementById("home_mid_div");
        for (var i = 0; i < resp.length; i++) {
            var tweet = document.createElement("div");
            tweet.className = "ownTweets";
            var link_name = resp[i]["user_name"];
            link_name = link_name.replace(" ", "%20");

//                adding the data into the tweet
            tweet.innerHTML = "<a href=" + "profile.php?" + link_name + "><img class='home_tweet_image' src=" + resp[i]["user_pic"] + "></a>";
            tweet.innerHTML += "<h1 class='tweet_name'><a onmouseover='info(this)' onmouseout='hide1()' href=profile.php?" + link_name + ">" + resp[i]["user_name"] + "</a></h1>";
            tweet.innerHTML += "<h4 class='tweet_date'>" + resp[i]["twat_date"] + "</h4>";
            tweet.innerHTML += "<p class='content' style='border: 0'>" + resp[i]["twat_content"] + "<br>" + "</p>";

            if (resp[i]['twat_img'] != null) {
                tweet.innerHTML += '<br>';
                tweet.innerHTML += '<img id="contentIMG" style="height: 280px; width: auto; max-width: 500px; margin-right: 20px;" onclick="modal(this)" src="' + resp[i]['twat_img'] + '">';
                tweet.innerHTML +=
                    '<div id="myModal" class="modal">\n' +
                    '  <span class="close">&times;</span>\n' +
                    '  <img class="modal-content" id="img01">\n' +
                    '  <div id="caption"></div>\n' +
                    '</div>';
            }
            if (resp[i]['youLike'][0]["is_liked"] == 1) {
                tweet.innerHTML += "<br><i id='heart" + resp[i]['twat_id'] + "' style='color: red;' value=" + resp[i]['twat_id'] + " onclick=\"likeTweet(" + resp[i]['twat_id'] + ",this)\" class=\"fa fa-heart hrt\"></i>";
            }else{
                tweet.innerHTML += "<br><i id='heart" + resp[i]['twat_id'] + "' style='color: black;' value=" + resp[i]['twat_id'] + " onclick=\"likeTweet(" + resp[i]['twat_id'] + ",this)\" class=\"fa fa-heart hrt\"></i>";

            }

            tweet.innerHTML += ' <b id="likesCounter' + resp[i]['twat_id'] + '">' + resp[i]['likes'][0]["likes"] + '</b>';


            var comment_div = document.createElement('div');
            comment_div.className = "home_tweet_comments";
            comment_div.id = resp[i]['twat_id'];
            div.appendChild(tweet);
            div.appendChild(comment_div);

            // tweet.addEventListener('click', function () {
            //
            //         var cmnts = document.getElementById(resp[i]['twat_id']);
            //         cmnts.style.maxHeight='10000px';
            //
            // }(i));

//                adding comments to the tweets
            test(resp[i]['twat_id']);

        }
    }
};
req.send();

function modal(img){
    var modal = document.getElementById('myModal');
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");
    img.onclick = function(){
        modal.style.display = "block";
        modalImg.src = this.src;
        captionText.innerHTML = this.alt;
    };

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    };
}



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
//                else {
//                    div.innerHTML += "<br><h3 style='margin-left:70px;'>No comments on this tweet</h3>";
//                }
        }
        else {
            comments = "fail";
        }
    };
    request.send();
}

function likeTweet(id, heart) {
    var request0 = new XMLHttpRequest();
    request0.open("GET", "../commandPattern.php?tweet_id=" + id + "&target=twit&action=checkIfLiked");
    request0.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            resp = this.responseText;
            resp = JSON.parse(resp);
            heart.style.color = 'red';
            if (resp[0]['is_liked'] == 0) {
                var request = new XMLHttpRequest();
                request.open("GET", "../commandPattern.php?twat_id=" + id + "&target=twit&action=likeTweet");
                request.onreadystatechange = function (ev) {
                    if (this.readyState == 4 && this.status == 200) {
                        var count = document.getElementById('likesCounter' + id);
                        count.innerHTML++;
                        heart.style.color = 'red';
                    }
                };
                request.send();
            }
            else {
                var request = new XMLHttpRequest();
                request.open("GET", "../commandPattern.php?twat_id=" + id + "&target=twit&action=dislikeTweet");
                request.onreadystatechange = function (ev) {
                    if (this.readyState == 4 && this.status == 200) {
                        var count = document.getElementById('likesCounter' + id);
                        count.innerHTML--;
                        heart.style.color = 'black';
                    }
                };
                request.send();
            }
        }
    };
    request0.send();
}

function info(pole) {
    var user_name = pole.innerText;
    link_name = user_name.replace(" ", "%20");
    if (link_name.charAt(0) == "@") {
        link_name = link_name.slice(1);
    }


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
