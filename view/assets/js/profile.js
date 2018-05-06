window.onload = random();
/*Георги -- 20.03.2018 -- Скриване и показване на профилната снимка в навигейшън бара*/
var header = document.getElementById("my_nav");
window.onscroll = function (event) {
    requestAnimationFrame(checkPos);
};

function checkPos() {                                                /*Проверява позицията на скрола*/
    var circle = document.getElementById('circle');
    var cover = document.getElementById("cover");
    var card = document.getElementById("profile_card");
    var top_bar = document.getElementById("top_bar");
    var main = document.getElementById("main");
    var y = window.scrollY;
    if (y >= 330) {                                                 /*Ако е над 300px скрива снимката*/
        circle.style.marginTop = "-200px";
        circle.style.transition = "margin-top 200ms linear";
        document.getElementById("nav_image").style.visibility = "visible";
        header.style.position = "fixed";
        cover.style.position = "fixed";
        header.style.top = "70px";
        cover.style.top = "-330px";
        card.style.opacity = "1";
        main.style.marginTop = "484px";
        top_bar.style.top = "0";
    }
    else {                                                /*В противен случай и връща параметрите по подразбиране*/
        document.getElementById("nav_image").style.visibility = "hidden";
        main.style.marginTop = "10px";
        cover.style.top = "";
        cover.style.position = "";
        header.style.position = "";
        circle.style.marginTop = "";
        circle.style.visibility = "";
        circle.style.top = "";
    }
}

/*Георги -- 23.03.2018 --  Запълване на профилите в зависимост дали търсим някой или разглеждаме своя собствен*/
var queryString = decodeURIComponent(window.location.search);
queryString = queryString.substring(1);
var queries = queryString.split("&");

if(queries[0] == "error"){
    window.alert("Некоректни данни!");
}
/*---------------------------------------------------------------------------------------------------*/
if (queryString.length != 0) { /*Георги --27.03.2018-- Ако в URL има параметър се запълва чужд профил*/
    var request = new XMLHttpRequest();
    request.open("GET", "../commandPattern.php?name=" + queries[0] + "&target=user&action=showProfile");
    request.onreadystatechange = function (ev) {
        if (this.status == 200 && this.readyState == 4) {
            var response = JSON.parse(this.responseText);
            if (response === "exception") {
                window.location.assign("exception_page.php");
            }
            if (response == "my") {
                window.location.assign("profile.php");
            } else {
                var img = document.getElementById("circle_img");
                var small_img = document.getElementById("nav_img");
                var a = document.getElementById("nav_name");
                var button = document.createElement("button");
                var cover = document.getElementById("cover_img");
                var name = document.getElementById("name");
                var name_ = document.getElementById("name_");
                var description = document.getElementById("descriptionid");
                var city = document.getElementById('cityid');
                var reg_date = document.getElementById("reg_date");
                var email = document.getElementById("emailid");
                name.innerText = response[0]['user_name'];
                name_.innerText = "@" + response[0]['user_name'];
                description.innerHTML = response[0]['user_description'];
                if (response[0]['user_city'] === null) {
                    city.innerText += '';
                } else {
                    city.innerText += 'Живее в: ' + response[0]['user_city'];
                }

                reg_date.innerText = 'Регистриран на: ' + response[0]['user_date'].substring(0, 10);
                email.innerText = 'Имейл: ' + response[0]['user_email'];
                /*Проверка какъв бутон да бъде поставен*/
                var is_follow = new XMLHttpRequest();
                is_follow.open("GET", "../commandPattern.php?name=" + queries[0] + "&target=user&action=isFollow");
                is_follow.onreadystatechange = function (ev2) {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        if (response === "exception") {
                            window.location.assign("exception_page.php");
                        }
                        if (response == "0") {
                            button.innerText = "Последване";
                            button.id = "edit_btn";
                            button.name = "follow";
                        } else {
                            button.innerText = "Премахни";
                            button.id = "edit_btn_remove";
                            button.name = "follow";
                        }
                    }
                };
                is_follow.send();
                /*В зависимост какъв е бутона се изпълнява LIKE или DISLIKE функция*/
                button.addEventListener("click", function () {
                    if (this.innerHTML == "Последване") {
                        var request = new XMLHttpRequest();
                        request.open("GET", "../commandPattern.php?name=" + queries[0] + "&target=user&action=followUser");
                        request.onreadystatechange = function (ev) {
                            if (this.readyState == 4 && this.status == 200) {
                                var response = JSON.parse(this.responseText);
                                if (response == "1") {
                                    button.innerText = "Премахни";
                                    button.id = "edit_btn_remove";
                                    showNumbers();
                                }
                                if (response === "exception") {
                                    window.location.assign("exception_page.php");
                                }
                            }
                        };
                        request.send();
                    } else if (this.innerHTML == "Премахни") {
                        var request = new XMLHttpRequest();
                        request.open("GET", "../commandPattern.php?name=" + queries[0] + "&target=user&action=unfollowUser");
                        request.onreadystatechange = function (ev) {
                            if (this.readyState == 4 && this.status == 200) {
                                var response = JSON.parse(this.responseText);
                                if (response == "1") {
                                    button.innerText = "Последване";
                                    button.id = "edit_btn";
                                    showNumbers();
                                }
                                if (response === "exception") {
                                    window.location.assign("exception_page.php");
                                }
                            }
                        };
                        request.send();
                    }
                });
                document.getElementById("first_in_my_nav").appendChild(button);
                a.innerText = '@' + response[0]['user_name'];
                a.href = "profile.php?" + response[0]['user_name'];
                img.src = "";
                img.src = response[0]['user_pic'];
                small_img.src = response[0]['user_pic'];
                cover.src = response[0]["user_cover"];
            }
        }
    };
    request.send();

    /* Георги --27.03.2018--  Втори рекуест (чужд профил) за визуализиране на цифрите на броя юзъри които
    * следва, го следват и публикуваните туитове*/
    function showNumbers() {
        var request2 = new XMLHttpRequest();
        request2.open("GET", "../commandPattern.php?name=" + queries[0] + "&target=user&action=getFFT");
        request2.onreadystatechange = function (ev) {
            if (this.status == 200 && this.readyState == 4) {
                var response = JSON.parse(this.responseText);
                if (response === "exception") {
                    window.location.assign("exception_page.php");
                }
                var a = document.getElementById("following");
                var span = document.getElementById('sledva');
                span.innerText = response[0][0]['num'];

                var a = document.getElementById("followers");
                var span = document.getElementById('sledvat');
                span.innerText = response[1][0]['num'];

                var a = document.getElementById("twits");
                var span = document.getElementById('tuitove');
                span.innerText = response[2][0]['num'];

            }
        };
        request2.send();
    }

    showNumbers();

    /*Георги --27.03.2018--С натискане върху линка "следва" се визуализират прозорци с информация
     * за всеки го следващ юзър */
    function showFollowing() {
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?name=" + queries[0] + "&target=user&action=showOtherUserFollowings");
        request.onreadystatechange = function (ev) {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response === "exception") {
                    window.location.assign("exception_page.php");
                }
                var center = document.getElementById("center_tweet");
                center.style.width = "869px";
                center.style.backgroundColor = "transparent";
                var right = document.getElementById("random_users");
                right.style.visibility = "hidden";
                right.style.width = "1px";
                center.innerHTML = "";
                for (var key in response) {
                    var div = document.createElement("div");
                    div.classList.add("followingUserInfo");
                    var cover_div = document.createElement("div");
                    cover_div.classList.add("followingUserInfo_cover");
                    var img = document.createElement("img");
                    img.id = "followingUserInfo_image";
                    img.src = response[key]['user_cover'];

                    center.appendChild(div);
                    div.appendChild(cover_div);
                    cover_div.appendChild(img);

                    var div_name = document.createElement("div");
                    div_name.classList.add("followingUserDiv_name");
                    var user_img = document.createElement("img");
                    user_img.id = "followingUserProfile_image";
                    user_img.src = response[key]["user_pic"];
                    var h2 = document.createElement("h2");
                    h2.id = "profile_h2";
                    h2.innerText = response[key]["user_name"];
                    var a = document.createElement("a");
                    a.id = "profile_h4";
                    a.innerText = "@" + response[key]["user_name"];
                    a.href = "profile.php?" + response[key]["user_name"];

                    div.appendChild(div_name);
                    div_name.appendChild(user_img);
                    div_name.appendChild(h2);
                    div_name.appendChild(a);
                }
            }
        };
        request.send();
    }

    /*Георги --27.03.2018--С натискане върху линка "последователи" се визуализират прозорци с информация
     * за всеки последовател*/
    function showFollowers() {
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?name=" + queries[0] + "&target=user&action=showFollowers");
        request.onreadystatechange = function (ev) {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response === "exception") {
                    window.location.assign("exception_page.php");
                }
                var center = document.getElementById("center_tweet");
                center.style.width = "869px";
                var right = document.getElementById("random_users");
                right.style.visibility = "hidden";
                right.style.width = "1px";
                center.innerHTML = "";
                for (var key in response) {
                    var div = document.createElement("div");
                    div.classList.add("followingUserInfo");
                    var cover_div = document.createElement("div");
                    cover_div.classList.add("followingUserInfo_cover");
                    var img = document.createElement("img");
                    img.id = "followingUserInfo_image";
                    img.src = response[key]['user_cover'];

                    center.appendChild(div);
                    div.appendChild(cover_div);
                    cover_div.appendChild(img);

                    var div_name = document.createElement("div");
                    div_name.classList.add("followingUserDiv_name");
                    var user_img = document.createElement("img");
                    user_img.id = "followingUserProfile_image";
                    user_img.src = response[key]["user_pic"];
                    var h2 = document.createElement("h2");
                    h2.id = "profile_h2";
                    h2.innerText = response[key]["user_name"];
                    var a = document.createElement("a");
                    a.id = "profile_h4";
                    a.innerText = "@" + response[key]["user_name"];
                    a.href = "profile.php?" + response[key]["user_name"];

                    div.appendChild(div_name);
                    div_name.appendChild(user_img);
                    div_name.appendChild(h2);
                    div_name.appendChild(a);
                }
            }
        };
        request.send();
    }

    /*Георги --27.03.2018--С натискане върху линка "туитове" се визуализират прозорец с всички туитове на потребителя*/
    function showTwits() {
        var center = document.getElementById("center_tweet");
        center.style.width = "600px";
        center.innerHTML = "";
        center.style.backgroundColor = "";

        var right = document.getElementById("random_users");
        right.style.visibility = "visible";
        right.style.width = "280px";
        showOtherUsersTwits(queries[0]);
    }

    function showOtherUsersTwits(name) {
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?name=" + name + "&target=twit&action=showOtherUsersTweets");
        request.onreadystatechange = function (ev) {
            if (this.status == 200 && this.readyState == 4) {
                var resp = this.responseText;
                resp = JSON.parse(resp);
                if (resp === "exception") {
                    window.location.assign("exception_page.php");
                }
                var div = document.getElementById("center_tweet");
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
                    } else {
                        tweet.innerHTML += "<br><i id='heart" + resp[i]['twat_id'] + "' style='color: black;' value=" + resp[i]['twat_id'] + " onclick=\"likeTweet(" + resp[i]['twat_id'] + ",this)\" class=\"fa fa-heart hrt\"></i>";

                    }

                    tweet.innerHTML += ' <b id="likesCounter' + resp[i]['twat_id'] + '">' + resp[i]['likes'][0]["likes"] + '</b>';


                    var comment_div = document.createElement('div');
                    comment_div.className = "home_tweet_comments";
                    comment_div.id = resp[i]['twat_id'];
                    div.appendChild(tweet);
                    div.appendChild(comment_div);

                    const a = resp[i]['twat_id'];
                    tweet.addEventListener("click", function () {
                        changeMaxHeight(a);

                        function changeMaxHeight(a) {
                            var asd = document.getElementById(a);
                            if (asd.style.maxHeight == '10000px') {
                                asd.style.maxHeight = '0px';
                            } else {
                                asd.style.maxHeight = '10000px';
                            }
                        }
                    });

//                adding comments to the tweets
                    test(resp[i]['twat_id']);

                }
            }
        };
        request.send();
    }

    showOtherUsersTwits(queries[0]);

}
else {/*-------------------------------------------------------------------------------------------*/

    /*Георги --27.03.2018-- Ако в URL НЯМА параметър се запълва профила на логнатия потребител*/
    var request = new XMLHttpRequest();
    request.open("GET", "../commandPattern.php?target=user&action=profile");
    request.onreadystatechange = function (ev) {
        if (this.status == 200 && this.readyState == 4) {
            var response = JSON.parse(this.responseText);
            if (response === "exception") {
                window.location.assign("exception_page.php");
            }
            var img = document.getElementById("circle_img");
            var small_img = document.getElementById("nav_img");
            var a = document.getElementById("nav_name");
            var button = document.createElement("button");
            var cover = document.getElementById("cover_img");
            var name = document.getElementById("name");
            var name_ = document.getElementById("name_");
            var description = document.getElementById("descriptionid");
            var city = document.getElementById('cityid');
            var reg_date = document.getElementById("reg_date");
            var email = document.getElementById("emailid");
            name.innerText = response['username'];
            name_.innerText = "@" + response['username'];
            description.innerHTML = response['description'];
            if (response['city'] === "") {
                city.innerText += '';
            } else {
                city.innerText += 'Живее в: ' + response['city'];
            }
            reg_date.innerText = 'Регистриран на: ' + response['date'];
            email.innerText = 'Имейл: ' + response['email'];
            document.getElementById("first_in_my_nav").appendChild(button);
            button.innerText = "Редактиране на профила";
            button.id = "edit_btn";
            button.name = "edit_btn";
            button.addEventListener("click", function () {

                window.scrollTo(500, 0);
                var edit = document.getElementById("edit_outer_wrapper");
                var editt = document.getElementById("profile_edit");
                edit.style.visibility = "visible";
                var body = document.getElementsByTagName("BODY")[0];
                body.style.overflow = "hidden";
                var msgsY = document.getElementById('msgsY');
                msgsY.addEventListener('click', function () {
                    edit.style.visibility = "hidden";
                    editt.style.visibility = "hidden";
                    body.style.overflow = "scroll";
                });

                var edit_div = document.getElementById("profile_edit");
                edit_div.style.visibility = "visible";
                edit_div.style.zIndex = "1000";
                var request = new XMLHttpRequest();
                request.open("GET", "../commandPattern.php?target=user&action=profile");
                request.onreadystatechange = function (ev2) {
                    if (this.readyState == 4 && this.status == 200) {
                        var result = JSON.parse(this.responseText);
                        if (result === "exception") {
                            window.location.assign("exception_page.php");
                        }
                        var username = document.getElementById("username");
                        var email = document.getElementById("email");
                        var city = document.getElementById("city");
                        var description = document.getElementById("description");
                        var password = document.getElementById("password");
                        var btn = document.getElementById("btn_edit");
                        btn.addEventListener("click", function () {
                            if (password.value == 0) {
                                password.style.border = "1px solid red";
                                event.preventDefault();
                            }
                            if(username.value == 0){
                                username.style.border = "1px solid red";
                                event.preventDefault();
                            }
                            if(email.value == 0){
                                email.style.border = "1px solid red";
                                event.preventDefault();
                            }
                            if (validateEmail(email.value) !== true) {
                                email.style.border = "1px solid red";
                                event.preventDefault();
                            }
                        });
                        username.value = result['username'];
                        email.value = result['email'];
                        city.value = result['city'];
                        description.value = result['description'];
                    }
                };
                request.send();
            });
            a.innerText = '@' + response['username'];
            a.href = "profile.php?" + response['username'];
            img.src = "";
            img.src = response['image_url'];
            small_img.src = response['image_url'];
            cover.src = response['cover_url'];
        }
    };
    request.send();


    /* Георги --27.03.2018--  Втори рекуест (логнат профил) за визуализиране на цифрите на броя юзъри които
    * следва, го следват и публикуваните туитове*/
    function showMynumbers() {
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?target=user&action=showMyProfile");
        request.onreadystatechange = function (ev) {
            if (this.status == 200 && this.readyState == 4) {
                var response = JSON.parse(this.responseText);
                if (response === "exception") {
                    window.location.assign("exception_page.php");
                }
                var a = document.getElementById("following");
                var span = document.getElementById('sledva');
                span.innerText = response[0][0]['num'];

                var a = document.getElementById("followers");
                var span = document.getElementById('sledvat');
                span.innerText = response[1][0]['num'];

                var a = document.getElementById("twits");
                var span = document.getElementById('tuitove');
                span.innerText = response[2][0]['num'];

            }
        };
        request.send();
    }

    showMynumbers();

    /*Георги --27.03.2018--С натискане върху линка "следва" се визуализират прозорци с информация
    * за всеки го следващ юзър, както и бутон за "unfollow" */
    function showFollowing() {
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?target=user&action=showFollowings");
        request.onreadystatechange = function (ev) {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response === "exception") {
                    window.location.assign("exception_page.php");
                }
                var center = document.getElementById("center_tweet");
                center.style.width = "869px";
                center.style.backgroundColor = "transparent";
                var right = document.getElementById("random_users");
                right.style.visibility = "hidden";
                right.style.width = "1px";
                center.innerHTML = "";
                for (var key in response) {
                    var div = document.createElement("div");
                    div.classList.add("followingUserInfo");
                    var cover_div = document.createElement("div");
                    cover_div.classList.add("followingUserInfo_cover");
                    var img = document.createElement("img");
                    img.id = "followingUserInfo_image";
                    img.src = response[key]['user_cover'];

                    center.appendChild(div);
                    div.appendChild(cover_div);
                    cover_div.appendChild(img);

                    var div_name = document.createElement("div");
                    div_name.classList.add("followingUserDiv_name");
                    var user_img = document.createElement("img");
                    user_img.id = "followingUserProfile_image";
                    user_img.src = response[key]["user_pic"];
                    var h2 = document.createElement("h2");
                    h2.id = "profile_h2";
                    h2.innerText = response[key]["user_name"];
                    var a = document.createElement("a");
                    a.id = "profile_h4";
                    a.innerText = "@" + response[key]["user_name"];
                    a.href = "profile.php?" + response[key]["user_name"];

                    var button = document.createElement("button");
                    button.id = "btn_unfollow";
                    button.innerText = "Премахни";
                    button.value = response[key]["user_name"];
                    button.addEventListener("click", function () {
                        var name = this.value;
                        var request = new XMLHttpRequest();
                        request.open("GET", "../commandPattern.php?name=" + name + "&target=user&action=unfollowUser");
                        request.onreadystatechange = function (ev) {
                            if (this.readyState == 4 && this.status == 200) {
                                var response = JSON.parse(this.responseText);
                                if (response == "1") {
                                    showFollowing();
                                    showMynumbers();
                                } else if (response === "exception") {
                                    window.location.assign("exception_page.php");
                                }
                            }
                        };
                        request.send();
                    });

                    div.appendChild(div_name);
                    div_name.appendChild(user_img);
                    div_name.appendChild(h2);
                    div_name.appendChild(a);
                    div.appendChild(button);
                }
            }
        };
        request.send();
    }

    /*Георги --29.03.2018--С натискане върху линка "последователи" се визуализират прозорци с информация
     * за всеки последовател*/
    function showFollowers() {
        var request = new XMLHttpRequest();
        request.open("GET", "../commandPattern.php?target=user&action=showMyFollowers");
        request.onreadystatechange = function (ev) {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (response === "exception") {
                    window.location.assign("exception_page.php");
                }
                var center = document.getElementById("center_tweet");
                center.style.width = "869px";
                var right = document.getElementById("random_users");
                right.style.visibility = "hidden";
                right.style.width = "1px";
                center.innerHTML = "";
                for (var key in response) {
                    var div = document.createElement("div");
                    div.classList.add("followingUserInfo");
                    var cover_div = document.createElement("div");
                    cover_div.classList.add("followingUserInfo_cover");
                    var img = document.createElement("img");
                    img.id = "followingUserInfo_image";
                    img.src = response[key]['user_cover'];

                    center.appendChild(div);
                    div.appendChild(cover_div);
                    cover_div.appendChild(img);

                    var div_name = document.createElement("div");
                    div_name.classList.add("followingUserDiv_name");
                    var user_img = document.createElement("img");
                    user_img.id = "followingUserProfile_image";
                    user_img.src = response[key]["user_pic"];
                    var h2 = document.createElement("h2");
                    h2.id = "profile_h2";
                    h2.innerText = response[key]["user_name"];
                    var a = document.createElement("a");
                    a.id = "profile_h4";
                    a.innerText = "@" + response[key]["user_name"];
                    a.href = "profile.php?" + response[key]["user_name"];

                    div.appendChild(div_name);
                    div_name.appendChild(user_img);
                    div_name.appendChild(h2);
                    div_name.appendChild(a);
                }
            }
        };
        request.send();
    }

    /*Георги --27.03.2018--С натискане върху линка "туитове" се визуализират прозорец с всички туитове на потребителя*/
    function showTwits() {
        var center = document.getElementById("center_tweet");
        center.style.width = "600px";
        center.innerHTML = "";
        center.style.backgroundColor = "";
        var right = document.getElementById("random_users");
        right.style.visibility = "visible";
        right.style.width = "280px";
        showMyTwits();
    }

    showMyTwits();
}

/*Георги --28.03.2018-- Рекуест за избрани на случаен принцип профили*/
function random() {
    var request = new XMLHttpRequest();
    request.open("GET", "../commandPattern.php?target=user&action=showRandomUsers");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response === "exception") {
                window.location.assign("exception_page.php");
            }
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
                            if (response === "exception") {
                                window.location.assign("exception_page.php");
                            }
                            var pos = document.getElementById("position_div");
                            pos.style.height = "300px";
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
                            console.log(response);
                            if (response === "exception") {
                                window.location.assign("exception_page.php");
                            }
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





                });
                /*При махане на мишката прозореца се скрива*/
                a.addEventListener("mouseout", function () {
                    var position = document.getElementById("position_div");
                    position.style.height = "0px";
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
                            if (response === "exception") {
                                window.location.assign("exception_page.php");
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

/*Георги 01.04.2018 -- Показва туитовете !!!*/
function showMyTwits() {
    var req = new XMLHttpRequest();
    req.open("get", "../commandPattern.php?target=twit&action=showMyTweets");
    req.onreadystatechange = function (ev) {
        if (this.status == 200 && this.readyState == 4) {
            var resp = this.responseText;
            resp = JSON.parse(resp);
            if (resp === "exception") {
                window.location.assign("exception_page.php");
            }
            var div = document.getElementById("center_tweet");
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
                } else {
                    tweet.innerHTML += "<br><i id='heart" + resp[i]['twat_id'] + "' style='color: black;' value=" + resp[i]['twat_id'] + " onclick=\"likeTweet(" + resp[i]['twat_id'] + ",this)\" class=\"fa fa-heart hrt\"></i>";

                }

                tweet.innerHTML += ' <b id="likesCounter' + resp[i]['twat_id'] + '">' + resp[i]['likes'][0]["likes"] + '</b>';


                var comment_div = document.createElement('div');
                comment_div.className = "home_tweet_comments";
                comment_div.id = resp[i]['twat_id'];
                div.appendChild(tweet);
                div.appendChild(comment_div);

                const a = resp[i]['twat_id'];
                tweet.addEventListener("click", function () {
                    changeMaxHeight(a);

                    function changeMaxHeight(a) {
                        var asd = document.getElementById(a);
                        if (asd.style.maxHeight == '10000px') {
                            asd.style.maxHeight = '0px';
                        } else {
                            asd.style.maxHeight = '10000px';
                        }
                    }
                });

//                adding comments to the tweets
                test(resp[i]['twat_id']);

            }
        }
    };
    req.send();
}

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

            if (response === "exception") {
                window.location.assign("exception_page.php");
            }
            var wrap = document.getElementById('msgWrap');
            wrap.innerHTML = '';

            for (var i = 0; i < response.length - 1; i++) {
                var msg = document.createElement('div');  //creating message div
                msg.className = 'msgs_in_wrap';
                var sender = response[response.length - 1][response[i]['message_id']]["sender"][0]["user_name"];
                var receiver = response[response.length - 1][response[i]['message_id']]["receiver"][0]["user_name"]
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
    send.onmouseover = function () {
        send.style.backgroundColor = "#006dbf";
    };
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

                if (response === "exception") {
                    window.location.assign("exception_page.php");
                }
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
            if (response === "exception") {
                window.location.assign("exception_page.php");
            }
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

            if (resp === "exception") {
                window.location.assign("exception_page.php");
            }
            heart.style.color = 'red';
            if (resp[0]['is_liked'] == 0) {
                var request = new XMLHttpRequest();
                request.open("GET", "../commandPattern.php?twat_id=" + id + "&target=twit&action=likeTweet");
                request.onreadystatechange = function (ev) {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        if (response === "exception") {
                            window.location.assign("exception_page.php");
                        }
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
                        var response = JSON.parse(this.responseText);
                        if (response === "exception") {
                            window.location.assign("exception_page.php");
                        }
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
            if (response === "exception") {
                window.location.assign("exception_page.php");
            }
            var position = document.getElementById("position_div");
            position.style.height = "300px";
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
            if (response === "exception") {
                window.location.assign("exception_page.php");
            }
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

}

function hide1() {
    var position = document.getElementById("position_div");
    position.style.height = "0px";
}

function modal(img) {
    var modal = document.getElementById('myModal');
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");
    img.onclick = function () {
        modal.style.display = "block";
        modalImg.src = this.src;
        captionText.innerHTML = this.alt;
    };

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    };
}

function validateEmail(email) {
    var exp = /(\w(=?@)\w+\.{1}[a-zA-Z]{2,})/i;
    return (exp.test(email));
}
