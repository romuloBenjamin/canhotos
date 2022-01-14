
const placer = document.querySelector("#liberar-tessa");
const tesseractForm = placer.querySelector("#formulario-tesseract");
const itemPlacer = tesseractForm.querySelector("ul");

const showTesseractItems = () => {
    // Get the tesseract data from local storage
    const tesseractData = localStorage.getItem("tesseract-data");
    const data = JSON.parse(tesseractData);
    if(!data) return;
    console.log(data);
    let index = 0;
    const originalNode = document.querySelector("#cloneNode");
    for(let item of data) {
        console.log(item)
        const clone = originalNode.cloneNode(true);
        const image_path_process = "../../../../images/scanner/" + item.scannerID + "/" + item.who + "/" + item.path_process.replace("./scanner/", "");
        const image_path_results = "../../../../images/scanner/" + item.scannerID + "/" + item.who + "/" + item.path_results.replace("./scanner/", "");
        clone.id = "item-" + index;
        // PLACE CNPJ E NFE SE LOCALIZAR
        const cnpj = clone.querySelector("#cnpj");
        cnpj.value = item.identify.cnpj ? item.identify.cnpj : "";
        cnpj.id = cnpj.id + "-" + index;
        cnpj.name = cnpj.id;
        const nfe = clone.querySelector("#nfe");
        nfe.value = item.identify.nfe?.replace(/\D/g,'');
        console.log(nfe.value);
        nfe.id = nfe.id + "-" + index;
        nfe.name = nfe.id;
        const hidden = clone.querySelector("#oculta");
        hidden.id = hidden.id + "-" + index;
        hidden.name = hidden.id;
        const hiddenData = {
            scanner: item.scannerID,
            image: item.images,
            user: item.who
        };
        hidden.value = JSON.stringify(hiddenData);
        // PLACE IMAGES
        const imagesArea = clone.querySelector("div.image-data > div.area-images");
        imagesArea.querySelector("#cloneImage").id = "sample-canhotos-" + index;
        imagesArea.querySelector("#cloneImage2").id = "sample-nfe-" + index;
        // PLACE IMAGES
        imagesArea.querySelector("#sample-canhotos-" + index + "").setAttribute("src", image_path_process + "/" + item.images);
        imagesArea.querySelector("#sample-canhotos-" + index + "").setAttribute("alt", item.images);
        imagesArea.querySelector("#sample-nfe-" + index + "").setAttribute("src", image_path_results + "/" + item.images);
        imagesArea.querySelector("#sample-nfe-" + index + "").setAttribute("alt", item.images);
        itemPlacer.appendChild(clone);
        index++;
    }
    originalNode.classList.add("d-none");
    placer.classList.remove("d-none");
}

showTesseractItems();

/*VIEW NFE*/
async function ver_sample(e) {
    var base = e.parentElement.parentElement;
    var images = base.nextElementSibling;
    for (let index = 0; index < images.children.length; index++) {
        const data = images.children[index];
        if(data.tagName === "IMG"){
            if(data.classList.contains("d-none")) e.innerHTML = "VER CANHOTOS";
            if(!data.classList.contains("d-none")) e.innerHTML = "VER NFE";
            data.classList.toggle("d-none");
        }
    }
}
/*VIEW CANHOTOS*/
async function ver_canhoto(e) {
    var base = e.parentElement.parentElement;
    var image = base.querySelectorAll("div.area-images > img.canhoto_view");
    image.forEach(data => {
        data.addEventListener("mousemove", magnify(data.id, 4));
    });
    base.addEventListener("mouseleave", (e) => {
        var targ = e.target.querySelector("div.img-magnifier-glass");
        if(targ != null) e.target.querySelector("div.img-magnifier-glass").remove();
    });
}
/*MAGNIFY CANHOTO*/
function magnify(imgID, zoom) {
    var img, glass, w, h, bw;
    img = document.getElementById(imgID);
    /*create magnifier glass:*/
    glass = document.createElement("DIV");
    glass.setAttribute("class", "img-magnifier-glass");
    /*insert magnifier glass:*/
    img.parentElement.insertBefore(glass, img);
    /*set background properties for the magnifier glass:*/
    glass.style.backgroundImage = "url('" + img.src + "')";
    glass.style.backgroundRepeat = "no-repeat";
    glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
    bw = 3;
    w = glass.offsetWidth / 2;
    h = glass.offsetHeight / 2;
    /*execute a function when someone moves the magnifier glass over the image:*/
    glass.addEventListener("mousemove", moveMagnifier);
    img.addEventListener("mousemove", moveMagnifier);
    /*and also for touch screens:*/
    glass.addEventListener("touchmove", moveMagnifier);
    img.addEventListener("touchmove", moveMagnifier);
    function moveMagnifier(e) {
        var pos, x, y;
        /*prevent any other actions that may occur when moving over the image*/
        e.preventDefault();
        /*get the cursor's x and y positions:*/
        pos = getCursorPos(e);
        x = pos.x;
        y = pos.y;
        /*prevent the magnifier glass from being positioned outside the image:*/
        if (x > img.width - (w / zoom)) {x = img.width - (w / zoom);}
        if (x < w / zoom) {x = w / zoom;}
        if (y > img.height - (h / zoom)) {y = img.height - (h / zoom);}
        if (y < h / zoom) {y = h / zoom;}
        /*set the position of the magnifier glass:*/
        glass.style.left = (x - w) + "px";
        glass.style.top = (y - h) + "px";
        /*display what the magnifier glass "sees":*/
        glass.style.backgroundPosition = "-" + ((x * zoom) - w + bw) + "px -" + ((y * zoom) - h + bw) + "px";
    }
    function getCursorPos(e) {
        var a, x = 1, y = 1;
        e = e || window.event;
        /*get the x and y positions of the image:*/
        a = img.getBoundingClientRect();
        /*calculate the cursor's x and y coordinates, relative to the image:*/
        x = e.pageX - a.left;
        y = e.pageY - a.top;
        /*consider any page scrolling:*/
        x = x - window.pageXOffset;
        y = y - window.pageYOffset;
        return {x : x, y : y};
    }
}
/*SALVAR TESSA*/
async function salvarTessa(e) {
    // Get the number of items (groups of cnpj / nfe / etc)
    console.log(itemPlacer.children.length)
    const numberOfItems = itemPlacer.children.length;
    if(numberOfItems <= 0) return;
    // If there's at least 1 item
    const data = [numberOfItems];
    // Add each item to the array
    let i = 0;
    while(i < numberOfItems) {
        const hiddenData = JSON.parse(tesseractForm.elements["oculta-" + i].value);
        data[i] = {
            nfe: tesseractForm.elements["nfe-" + i].value.toString(),
            cnpj: tesseractForm.elements["cnpj-" + i].value.toString(),
            username: hiddenData.user,
            scanner: hiddenData.scanner,
            date: getDate(),
            time: getTime(),
            image: hiddenData.image
        }
        // Add the data to log
        const addToLog = await axios.post("../../core/add-to-log-file-core.php", data[i]);
        console.log(addToLog.data);
        i++;
    }
    console.log(data);
    try {
        const result = await axios.post("../../core/save-manually-identified-tesseract-core.php", {
            save: data
        });
        console.log(result);
    } catch(error) {
        console.log(error);
    }
    window.close();
}