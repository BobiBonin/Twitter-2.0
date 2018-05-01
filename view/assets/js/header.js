
/*Georgi -- 23.03.2018 -- Търсене на потребители*/
function search() {
    var pole = document.getElementById("searchInput").value;
    var request = new XMLHttpRequest();
    request.open("get", "../commandPattern.php?name=" + pole + "&target=user&action=searchUserAndTags");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response.length == 0) {                             /* Ако няма резултат изписва "Няма резултат.."*/
                var ul = document.createElement("ul");
                ul.id = "search_ul";
                var div = document.getElementById("asd");
                var div2 = document.getElementById("ul");
                div2.innerHTML = "";
                div2.style.visibility = "visible";
                var li = document.createElement("li");
                var a = document.createElement("a");
                a.innerHTML = "Няма резултати..";
                li.appendChild(a);
                ul.appendChild(li);
            } else {                                                 /* В противен случай изкарва всичко от масива*/
                var ul = document.createElement("ul");
                ul.id = "search_ul";
                var div = document.getElementById("asd");
                var div2 = document.getElementById("ul");
                div2.innerHTML = "";
                div2.style.visibility = "visible";
                for (var key in response) {
                    for (var value in response[key]) {
                        if (key == 0) {
                            var li = document.createElement("li");
                            var a = document.createElement("a");
                            a.href = "#";
                            a.style.fontSize = "15px";
                            a.style.cssFloat = "left";
                            a.style.color = "gray";
                            a.style.padding = "10px";
                            var res = response[key][value];
                            if (res.length > 20) {
                                res = res.substring(0, 19) + "..";
                            }
                            a.innerHTML = res;
                            a.addEventListener("click", function displayTags() {  /*При клик върху таг пренасочва към избрания него*/
                                // TODO: CREATE LINKS FOT TAGS !!!!!!!!!!
                                var asd = this.innerHTML;
                                var pole = asd.slice(1);
                                window.location.assign("./home_hashtags.php?"+pole);

                            });
                            li.appendChild(a);
                            ul.appendChild(li);
                        } else {
                            var li = document.createElement("li");
                            var a = document.createElement("a");
                            a.href = "#";
                            var img = document.createElement("img");
                            a.innerHTML = response[key][value]["user_name"];
                            a.style.margin = "5px";
                            a.style.marginTop = "30px";
                            a.addEventListener("click", function () {  /*При клик върху име пренасочва към избрания профил*/
                                var name = this.innerText;
                                var queryString = "?" + name;
                                window.location.href = "profile.php" + queryString;
                            });
                            img.src = response[key][value]["user_pic"];
                            img.style.cssFloat = "left";
                            li.appendChild(img);
                            li.appendChild(a);
                            ul.appendChild(li);
                        }

                    }

                }
            }
            div2.appendChild(ul);
            div.appendChild(div2);
        }
    };
    request.send();
}

/*Georgi -- 23.03.2018 -- Показване на снимката на логнатия потребител до сърч полето*/

var request = new XMLHttpRequest();
request.open("GET", "../commandPattern.php?target=user&action=profile");
request.onreadystatechange = function (ev) {
    if (this.status == 200 && this.readyState == 4) {
        var response = JSON.parse(this.responseText);
        var img = document.getElementById("profile_icon");
        img.src = response['user_pic'];
    }
};
request.send();

function hide() {
    var div2 = document.getElementById("ul");
    div2.innerHTML = "";
    div2.style.visibility = "hidden";
}