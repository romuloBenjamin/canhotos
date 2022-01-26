
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
        console.log(item)
        const clone = originalNode.cloneNode(true);
        const image_path_process = "../../../../images/scanner/" + item.scannerId + "/" + item.username + "/process";
        const image_path_results = "../../../../images/scanner/" + item.scannerId + "/" + item.username + "/results";
        clone.id = "item-" + index;
        // PLACE CNPJ E NFE SE LOCALIZAR
        const cnpj = clone.querySelector("#cnpj");
        let cnpjValue = item.cnpj ? item.cnpj : "";
        if(cnpjValue.length > 14) cnpjValue = cnpjValue.substring(0, 14);
        cnpj.value = cnpjValue;
        cnpj.id = cnpj.id + "-" + index;
        cnpj.name = cnpj.id;
        const nfe = clone.querySelector("#nfe");
        let nfeValue = item.nfe?.replace(/\D/g,'') || "";
        if(nfeValue.length > 9) nfeValue = nfeValue.substring(0, 9);
        nfe.value = nfeValue;
        nfe.id = nfe.id + "-" + index;
        nfe.name = nfe.id;
        const hidden = clone.querySelector("#oculta");
        hidden.id = hidden.id + "-" + index;
        hidden.name = hidden.id;
        const hiddenData = {
            scanner: item.scannerId,
            image: item.image,
            user: item.username
        };
        hidden.value = JSON.stringify(hiddenData);
        // PLACE IMAGES
        const imagesArea = clone.querySelector("#imagesArea");
        imagesArea.querySelector("#cloneImage").id = "sample-canhotos-" + index;
        imagesArea.querySelector("#cloneImage2").id = "sample-nfe-" + index;
        // PLACE IMAGES
        imagesArea.querySelector("#sample-canhotos-" + index + "").setAttribute("src", image_path_process + "/" + item.image);
        imagesArea.querySelector("#sample-canhotos-" + index + "").setAttribute("alt", item.image);
        imagesArea.querySelector("#sample-nfe-" + index + "").setAttribute("src", image_path_results + "/" + item.image);
        imagesArea.querySelector("#sample-nfe-" + index + "").setAttribute("alt", item.image);
        itemPlacer.appendChild(clone);
        index++;
    }
    originalNode.classList.add("d-none");
    placer.classList.remove("d-none");
}

showTesseractItems();

// Toggle the images according to the clicked button's id
function toggleImages(buttonId, images) {
    for (let index = 0; index < images.children.length; index++) {
        const image = images.children[index];
        if(buttonId === "canhotoButton") {
            if(image.id.includes("canhoto")) image.classList.remove("d-none");
            else image.classList.add("d-none");
        } else {
            if(image.id.includes("canhoto")) image.classList.add("d-none");
            else image.classList.remove("d-none");
        }
    }
} 

/*VIEW NFE*/
function ver_sample(e) {
    e.classList.add("active");
    var base = e.parentElement.parentElement.parentElement.parentElement;
    if(e.id === "canhotoButton") base.querySelector("#nfeButton").classList.remove("active");
    else base.querySelector("#canhotoButton").classList.remove("active");
    const images = base.querySelector("#imagesArea");
    toggleImages(e.id, images);
}

/*VIEW CANHOTOS*/
async function ver_canhoto(e) {
    const popup = window.open(e.src, "_blank");
}

function rotate(e) {
    const image = e.parentElement.querySelector(".canhoto_view");
    if(image.classList.contains("rotated")) image.classList.remove("rotated");
    else image.classList.add("rotated");
}

/*SALVAR TESSA*/
async function salvarTessa(e) {
    let username;
    let scannerId;
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
            if(!nfeValue || !cnpjValue || nfeValue.length < 9 || cnpjValue.length < 14) throw new Error("Por favor, preencha todos os dados corretamente.");
            data[i] = {
                nfe: nfeValue,
                cnpj: cnpjValue,
                username: hiddenData.user,
                scanner: hiddenData.scanner,
                date: getDate(),
                time: getTime(),
                image: hiddenData.image
            }
            if(!username) username = hiddenData.user;
            if(!scannerId) scannerId = hiddenData.scanner;
            // Add the data to log
            console.log(data[i])
            const addToLog = await axios.post("../../core/add-to-log-file-core.php", data[i]);
            i++;
        }
        const result = await axios.post("../../core/save-manually-identified-tesseract-core.php", {
            save: data
        });
        // Get the current json
        let json = await getIdentifyProcessesRunningJson("../../jsons/lista-identify-processes-running-json.json");
        
        // Remove the current scanner from the userinfo
        let userInfo = json[username];
        if(userInfo) delete userInfo[scannerId];

        await updateIdentifyProcessesRunningJson({
            [username]: userInfo
        }, "../../core/lista-identify-processes-running-core.php");
        window.close();
    } catch(error) {
        if(error.message === "Por favor, preencha todos os dados") alert(error.message);
        console.log(error);
    }
}