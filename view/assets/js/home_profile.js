

var req = new XMLHttpRequest();
req.open("get", "../commandPattern.php?target=user&action=profile");
req.onreadystatechange = function (ev) {
    var resp = this.responseText;
    resp = JSON.parse(resp);
    var user_name = resp["user_name"];
    var user_pic = resp["user_pic"];
    var user_cover = resp["user_cover"];
//24.03.2017 Boris
    var img = document.getElementById("hm_prof_img");
    img.src = user_pic;

    var cover = document.getElementById("cover");
    cover.src = user_cover;

    var profile_h2 = document.getElementById("profile_h2");
    profile_h2.innerHTML = user_name;

    var profile_h4 = document.getElementById("profile_h4");
    profile_h4.innerHTML = "@" + user_name;


    var request2 = new XMLHttpRequest();
    request2.open("GET", "../commandPattern.php?name=" + resp["user_name"] + "&target=user&action=getFFT");
    request2.onreadystatechange = function (ev) {
        if (this.status == 200 && this.readyState == 4) {
            var response = JSON.parse(this.responseText);

            var a = document.getElementById("home_prof_following");
            a.innerText = response[0][0]['num'];

            var a = document.getElementById("home_prof_followers");
            a.innerText = response[1][0]['num'];

            var a = document.getElementById("home_prof_tweets");
            a.innerText = response[2][0]['num'];

        }
    };
    request2.send();
};
req.send();
