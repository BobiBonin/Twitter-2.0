document.addEventListener('DOMContentLoaded', random, false);

$(document).ready(function () {
    // Add smooth scrolling to all links
    $("a").on('click', function (event) {

        // Make sure this.hash has a value before overriding default behavior
        if (this.hash !== "") {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Store hash
            var hash = this.hash;

            // Using jQuery's animate() method to add smooth page scroll
            // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 800, function () {

                // Add hash (#) to URL when done scrolling (default click behavior)
                window.location.hash = hash;
            });
        } // End if
    });
});



function random() {
    var request = new XMLHttpRequest();
    request.open("GET", "../commandPattern.php?target=user&action=showRandomUsers");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);

            var random_users_div = document.getElementById("random_users");
            var randoms = document.getElementById("randoms");
            randoms.style.width = "100%";
            randoms.style.height = "80%";
            randoms.innerHTML = "";

            for (var key in response) {
                var user_div = document.createElement("div");
                user_div.id = "first";
                var image_div = document.createElement("div");
                image_div.id = "random_img_div";
                var img = document.createElement("img");
                img.id = "random_img";
                img.src = response[key]['user_pic'];
                var name = document.createElement("h1");
                name.id = "random_name";
                var a = document.createElement("a");
                a.href = "profile.php?" + response[key]["user_name"];
                a.id = "a_name";

                a.innerText = response[key]["user_name"];
                a.addEventListener("mouseover", function () { /*При ховър се показва допълнителна информация за юзъра*/

                    var user_name = this.innerHTML;
                    var request = new XMLHttpRequest();
                    request.open("GET", "../commandPattern.php?name=" + user_name + "&target=user&action=showProfile");
                    request.onreadystatechange = function (ev) {
                        if (this.status == 200 && this.readyState == 4) {
                            var response = JSON.parse(this.responseText);

                            var pos = document.getElementById("position_div");
                            pos.style.visibility = "visible";
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
                    request2.open("GET", "../commandPattern.php?name=" + user_name + "&target=user&action=showSmallDiv");
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
                    position.style.left = posx + "px";
                    position.style.top = posy + "px";
                    position.style.display = "block";
                    position.style.opacity = "1";
                    position.style.transition = "opacity 1.25s linear";

                });
                /*При махане на мишката прозореца се скрива*/
                a.addEventListener("mouseout", function () {
                    var position = document.getElementById("position_div");
                    position.style.display = "none";
                });

                var button = document.createElement("button");
                button.innerText = "Последвай";
                button.classList.add("follow_btn");
                button.value = response[key]["user_name"];
                button.addEventListener("click", function () {
                    var name = this.value;
                    var request = new XMLHttpRequest();
                    request.open("GET", "../commandPattern.php?name=" + name + "&target=user&action=followUser");
                    request.onreadystatechange = function (ev) {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            if (response == "1") {
                                random();
                                showMynumbers();
                            }
                        }
                    };
                    request.send();


                });
                var find = document.createElement("div");
                var h1 = document.createElement("h1");
                find.id = "last_div";
                h1.innerText = "Намери хора, които познаваш";
                h1.addEventListener('click', function () {   /*При натискане фокуса се премества върху сърч полето*/
                    var search = document.getElementById("searchInput");
                    search.focus();
                });
                find.appendChild(h1);
                randoms.appendChild(user_div);
                user_div.appendChild(image_div);
                image_div.appendChild(img);
                name.appendChild(a);
                user_div.appendChild(name);
                user_div.appendChild(button);
            }
            randoms.appendChild(find);
        }
    };
    request.send();
}

var home = document.getElementById("navHome");
home.style.borderBottom = "3px solid #1b95e0";

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
                var receiver = response[response.length - 1][response[i]['message_id']]["receiver"][0]["user_name"];
                var sLink = 'profile.php?' + sender;
                var rLink = 'profile.php?' + receiver;
                msg.innerHTML = '<h3>From <a href="' + sLink + '" style="color: #006dbf">' + sender + '</a> to <a href="' + rLink + '" style="color: #006dbf">' + receiver + '</a> on ' + response[i]['message_date'] + ' </h3>';
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

function characters(el) {
    counter = document.getElementById('counter');
    send = document.getElementById('sendMsgButton');
    counter.style.color = 'black';
    send.disabled = false;
    send.style.backgroundColor = '#1da1f2';
    send.style.cursor = "pointer";

    counter.innerHTML = el.value.length;
    if (el.value.length <= 100) {
        counter.style.color = '#00ff00';
    }
    if (el.value.length <= 200 && el.value.length > 100) {
        counter.style.color = '#ff9933';
    }
    if (el.value.length <= 300 && el.value.length > 200) {
        counter.style.color = '#ff6600';
    }
    if (el.value.length > 300) {
        send.disabled = true;
        send.style.backgroundColor = "gray";
        send.style.cursor = "not-allowed";
        counter.style.color = '#ff0000';
    }


}

var send = document.getElementById('input_submit');
send.disabled = true;
send.style.backgroundColor = "gray";
send.style.cursor = "not-allowed";
counter.style.color = '#ff0000';

function charactersForTweets(el) {
    var counter = document.getElementById('tweetCounter');
    var send = document.getElementById('input_submit');

    counter.style.color = 'black';
    send.disabled = false;
    send.style.backgroundColor = '#1da1f2';
    send.style.cursor = "pointer";
    send.onmouseover = function () {
        send.style.backgroundColor = "#006dbf";
    };
    counter.innerHTML = el.value.length;
    if (el.value.length == 0) {
        send.disabled = true;
        send.style.backgroundColor = "gray";
        send.style.cursor = "not-allowed";
        counter.style.color = '#ff0000';
    }
    if (el.value.length <= 100 && el.value.length > 0) {
        counter.style.color = '#23af10';
    }
    if (el.value.length <= 135 && el.value.length > 100) {
        counter.style.color = '#ff9933';
    }
    if (el.value.length <= 175 && el.value.length > 135) {
        counter.style.color = '#ff6600';
    }
    if (el.value.length > 200) {
        send.disabled = true;
        send.style.backgroundColor = "gray";
        send.style.cursor = "not-allowed";
        counter.style.color = '#ff0000';
    }
    if (el.value.replace(/\s/g, '').length == 0) {
        send.disabled = true;
        send.style.backgroundColor = "gray";
        send.style.cursor = "not-allowed";
        counter.style.color = '#ff0000';
    }
}

function hide() {
    var div2 = document.getElementById("ul");
    div2.innerHTML = "";
    div2.style.visibility = "hidden";
}
