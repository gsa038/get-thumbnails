const realFileButton = document.getElementById("real-file-btn");
const customFileButton = document.getElementById("custom-file-btn");
const imageLoaderButton = document.getElementById("image-loader-btn");

customFileButton.addEventListener("click", function() {
    if (imageLoaderButton.hasAttribute('href')) {
        imageLoaderButton.removeAttribute("href");
    }
    realFileButton.click();
});

realFileButton.addEventListener("change", fileValidation);

function fileValidation() {
    if (realFileButton.value) {
        const fileType =  realFileButton.files[0].type;
        if (fileType.indexOf('image') === 0) {
            setCustomFileButton("GOOD FILE", "bg-green");
            setLoaderButton("fa-arrow-up image-loader-btn");
            imageLoaderButton.addEventListener("click", sendImageFile);
        } else {
            setCustomFileButton('BAD FILE! MUST BE AN IMAGE', 'bg-red');
            setLoaderButton('fa-image bad-image-loader-btn');
            removeEventListener('click', imageLoaderButton);
        }
    } else {
        setCustomFileButton("CHOOSE FILE", "bg-yellow");
        setLoaderButton("fa-image no-image-loader-btn");
        removeEventListener('click', imageLoaderButton);
    }
};

function setCustomFileButton(innerHTML, additionClassName) {
    const customButtonBaseClass = "btn btn-rounded btn-lg text-white";
    customFileButton.innerHTML = innerHTML;
    customFileButton.className = customButtonBaseClass + " " + additionClassName;
}

function setLoaderButton(additionClassName) {
    const imageLoaderButtonBaseClass = "fas fa-10x";
    imageLoaderButton.className = imageLoaderButtonBaseClass + " " + additionClassName;
}

function sendImageFile(){
    const formData = new FormData();
    const imageFile = realFileButton.files[0];

    formData.append('image', imageFile);
    $.ajax({
      url: 'http://api.localhost:8881/thumbnails',
      type: 'post',
      dataType: 'json',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response){
          if (response['statusCode'] === 200) {
            $('#image-loader-btn').attr({
                class: 'fas fa-10x fa-arrow-down down-image-loader-btn',
                href : 'http://api.localhost:8881/thumbnails/' + response['archive']
            });
          } else {
            setCustomFileButton('API BAD REQUEST', 'bg-red');
            setLoaderButton('bad-image-loader-btn');
            realFileButton.files[0] = null;
          }
      },
      error: function(response) {
        setCustomFileButton('API PROBLEM', 'bg-red');
        setLoaderButton('bad-image-loader-btn');
        realFileButton.files[0] = null;
     }
    });
    imageLoaderButton.removeEventListener('click', sendImageFile);
};