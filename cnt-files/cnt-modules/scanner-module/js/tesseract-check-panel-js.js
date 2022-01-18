
const placer = document.querySelector("#liberarTessa");
const tesseractForm = placer.querySelector("#tesseractForm");
const itemPlacer = tesseractForm.querySelector("ul");

const showTesseractItems = () => {
    // Get the tesseract data from local storage
    const tesseractData = localStorage.getItem("tesseract-data");
    const data = JSON.parse(tesseractData);
    if(!data) return;
    let index = 0;
    const originalNode = document.querySelector("#cloneNode");
    for(let item of data) {
        const clone = originalNode.cloneNode(true);
        const image_path_process = "../../../../images/scanner/" + item.scannerID + "/" + item.who + "/" + item.path_process.replace("./scanner/", "");
        const image_path_results = "../../../../images/scanner/" + item.scannerID + "/" + item.who + "/" + item.path_results.replace("./scanner/", "");
        clone.id = "item-" + index;
        // PLACE CNPJ E NFE SE LOCALIZAR
        const cnpj = clone.querySelector("#cnpj");
        let cnpjValue = item.identify.cnpj ? item.identify.cnpj : "";
        if(cnpjValue.length > 14) cnpjValue = cnpjValue.substring(0, 14);
        cnpj.value = cnpjValue;
        cnpj.id = cnpj.id + "-" + index;
        cnpj.name = cnpj.id;
        const nfe = clone.querySelector("#nfe");
        let nfeValue = item.identify.nfe?.replace(/\D/g,'') || "";
        if(nfeValue.length > 9) nfeValue = nfeValue.substring(0, 9);
        nfe.value = nfeValue;
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
        const imagesArea = clone.querySelector("#imagesArea");
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
    e.classList.add("active");
    var base = e.parentElement.parentElement.parentElement.parentElement;
    if(e.id === "canhotoButton") base.querySelector("#nfeButton").classList.remove("active");
    else base.querySelector("#canhotoButton").classList.remove("active");
    var images = base.querySelector("#imagesArea");
    for (let index = 0; index < images.children.length; index++) {
        const data = images.children[index];
        if(data.tagName === "IMG") data.classList.toggle("d-none");
    }
}

/*VIEW CANHOTOS*/
async function ver_canhoto(e) {
    const popup = window.open(e.src, "_blank");
}

/*SALVAR TESSA*/
async function salvarTessa(e) {
    // Get the number of items (groups of cnpj / nfe / etc)
    const numberOfItems = itemPlacer.children.length;
    if(numberOfItems <= 0) return;
    // If there's at least 1 item
    const data = [numberOfItems];
    try {
        // Add each item to the array
        let i = 0;
        while(i < numberOfItems) {
            const hiddenData = JSON.parse(tesseractForm.elements["oculta-" + i].value);
            const nfeValue = tesseractForm.elements["nfe-" + i].value.toString();
            const cnpjValue =  tesseractForm.elements["cnpj-" + i].value.toString();
            if(!nfeValue || !cnpjValue) throw new Error("Por favor, preencha todos os dados");
            data[i] = {
                nfe: nfeValue,
                cnpj: cnpjValue,
                username: hiddenData.user,
                scanner: hiddenData.scanner,
                date: getDate(),
                time: getTime(),
                image: hiddenData.image
            }
            // Add the data to log
            const addToLog = await axios.post("../../core/add-to-log-file-core.php", data[i]);
            i++;
        }
        const result = await axios.post("../../core/save-manually-identified-tesseract-core.php", {
            save: data
        });
        window.close();
    } catch(error) {
        if(error.message === "Por favor, preencha todos os dados") alert(error.message);
        console.log(error);
    }
}