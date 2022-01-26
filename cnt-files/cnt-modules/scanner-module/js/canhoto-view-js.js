let imageData = window.localStorage.getItem("imageData");
if(imageData) {
    imageData = JSON.parse(imageData);
    const image = document.querySelector("#image");
    image.src = imageData.imageSource;
    image.alt = imageData.imageName;
    if(imageData.rotated) image.classList.add("rotated");
}

const onImageClick = (element) => {
    if(element.classList.contains("shrinkToFit")) {
        element.classList.remove("shrinkToFit");
        element.classList.add("overflowingHorizontalOnly");
        element.width = element.naturalWidth;
        element.height = element.naturalHeight;
        console.log(element)
    } else {
        element.classList.remove("overflowingHorizontalOnly");
        element.classList.add("shrinkToFit");
        delete element.width;
        delete element.height;
    }
}