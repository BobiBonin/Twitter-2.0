var request = new XMLHttpRequest();
request.open("GET", "../commandPattern.php?target=user&action=profile");
request.onreadystatechange = function (ev) {
    if (this.status == 200 && this.readyState == 4) {
        var response = JSON.parse(this.responseText);
        if (response === "exception") {
            window.location.assign("exception_page.php");
        }
        var img = document.getElementById("twat_box_icon");
        var img2 = document.getElementById("profile_icon");
        img.src = response['image_url'];
        img2.src = response['image_url'];
    }
};
request.send();

function ValidateFileUpload() {
    var fuData = document.getElementById('twat_img');
    var FileUploadPath = fuData.value;

//To check if user upload any file
    if (FileUploadPath == '') {
        alert("Please upload an image");

    } else {
        var Extension = FileUploadPath.substring(
            FileUploadPath.lastIndexOf('.') + 1).toLowerCase();

//The file uploaded is an image

        if (Extension == "gif" || Extension == "png" || Extension == "bmp"
            || Extension == "jpeg" || Extension == "jpg") {
// To Display
            var label = document.getElementById('imgLabel');
            label.innerHTML = '';
            label.innerHTML += ' Image is ready!';
            label.style.width = 'auto';
            var send = document.getElementById('input_submit');
            send.disabled = false;
            send.style.backgroundColor = '#1da1f2';
            send.style.cursor = "pointer";
            if (document.getElementById('twat_input').value.length > 200) {
                var send = document.getElementById('input_submit');
                send.disabled = true;
                send.style.backgroundColor = "gray";
                send.style.cursor = "not-allowed";
            }
            if (fuData.files && fuData.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah').attr('src', e.target.result);
                };
                reader.readAsDataURL(fuData.files[0]);
            }

        }

//The file upload is NOT an image
        else {
            var label = document.getElementById('imgLabel');
            alert("Pictures only allows file types of GIF, PNG, JPG, JPEG and BMP. ");
            label.innerHTML = '<i class="fa fa-picture-o" aria-hidden="true" style="font-size: 22px"></i>';
            label.style.width = '45px';
            if (document.getElementById('twat_input').value.length == 0 || document.getElementById('twat_input').value.length > 200) {
                var send = document.getElementById('input_submit');
                send.disabled = true;
                send.style.backgroundColor = "gray";
                send.style.cursor = "not-allowed";
            }
            if (document.getElementById('twat_input').value.replace(/\s/g, '').length == 0) {
                send.disabled = true;
                send.style.backgroundColor = "gray";
                send.style.cursor = "not-allowed";
                counter.style.color = '#ff0000';
            }
        }
    }
}