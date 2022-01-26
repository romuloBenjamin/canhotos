// The scan directory currently being used
let username = null;
let scanners = null;

const messages = [];

const spinner = document.querySelector("#spinner");
const messageBox = document.querySelector("#messageBox");
const extraMessageBox = document.querySelector("#extraMessageBox");

// Get the current user name
const getUsername = async () => {
    try {
        const response = await axios.get("./cnt-files/cnt-modules/scanner-module/core/get-username-core.php");
        return response.data;
    } catch(error) {
        console.log(error);
    }
    return null;
}

// Get the current user name
const getScanners = async () => {
    try {
        const response = await axios.get("./cnt-files/cnt-modules/scanner-module/core/get-scanner-user-access-core.php");
        return response.data;
    } catch(error) {
        console.log(error);
    }
    return null;
}

// Show the loading spinner
const showSpinner = () => {
    spinner.classList.remove("d-none");
}

// Hide the loading spinner
const hideSpinner = () => {
    spinner.classList.add("d-none");
}

// Count the lines of the element
const countLines = (elementId) => {
    // Get element with 'content' as id                            
    const el = document.getElementById(elementId);
    // Get total height of the content    
    const divHeight = el.offsetHeight
    // height of one line 
    const lineHeight = parseInt(document.defaultView.getComputedStyle(el, null).lineHeight);
    const lines = divHeight / lineHeight;
    console.log(lines);
    return lines;
}

// Shows the passed message in the box
const showMessage = (message) => {
    messages.push(message);
    if(messages.length > 100) messages.shift();
    messageBox.parentNode.classList.remove("d-none");
    messageBox.parentNode.classList.add("d-flex");
    messageBox.innerHTML = messages.join("<br>");
    messageBox.scrollTop = messageBox.scrollHeight;
}

// Create a button with the passed info
const getButton = (id, text, classlist, callback) => {
    const button = document.createElement("BUTTON");
    button.id = id;
    button.innerHTML = text;
    button.classList.add(...classlist);
    button.addEventListener("click", callback, false);
    return button;
}

// Remove special and normalize characters from the scanner name to use as ID and scan directory
const getScannerId = (scannerName) => {
    return scannerName.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().split(" ").join("-");
}

// Read the json with the scanners' data and add the buttons
const addScannerButtons = async () => {
    // Get the username
    if(!username) username = await getUsername();
    if(!username) {
        // User is not logged
        showMessage("Usuário não identificado. Realize o login.");
        return;
    }
    // Get the scanners the user has access to
    if(!scanners) scanners = await getScanners();
    if(!scanners || scanners.length === 0) {
        showMessage("Usuário não tem acesso a nenhum scanner.");
        return;
    }
    // Get the list of scanners
    let json = await axios.get("./cnt-files/cnt-modules/scanner-module/jsons/lista-scanners-json.json");
    const buttonContainer = document.getElementById("buttonContainer");
    // Loop through the scanners' data
    for(let scanner of json.data.scanners) {
        // If the user has access to this scanner, add the scanner button
        if(scanners.includes(scanner.name) || scanner.address === "") {
            const id = getScannerId(scanner.name);
            const text = scanner.name;
            let classList;
            let callback;
            let button;
            if(scanner.address === "") {
                // Identify button
                classList = ["btn", "btn-warning"];
                callback = () => { identify(); };
            } else {
                // Scanner button
                classList = ["btn", "btn-secondary"];
                scanner.scanDir = id;
                callback = () => { scan(scanner); };
            }
            button = getButton(id, text, classList, callback);
            buttonContainer.appendChild(button);
        }
    }
}

addScannerButtons();

// Disable/Enable the identify button
const disableIdentifyButton = (disabled = false) => {
    const identifyButton = document.querySelector("#identificar");
    disabled ? identifyButton.setAttribute("disabled", true) : identifyButton.removeAttribute("disabled");
}

// Get the scanner data and calls the scan process on the chosen server if available
const scan = async (scanner) => {
    showSpinner();
    const tag = "<span>[" + scanner.name + "]</span> ";
    showMessage(tag + "Iniciando...");
    try {
        // Request the scanning
        let response = await axios.post("http://" + scanner.address + ":8090/scan", { key: scanner.name, scanDir: scanner.scanDir, username: username });
        console.log(response.data);
        // If the scan was successfull
        if(response.data.scanFinished === true) {
            // If 1 or more items were scanned
            if(response.data.scannedItems > 0) {
                showMessage(tag + "Canhotos escaneados com sucesso");
                // Show confirmation dialog asking if the user wants to continue scanning or start the identification process
                $("#dialog").toggleClass("d-none");
                $(function() {
                    $("#dialog").dialog(
                        {
                            width: 600,
                            buttons: [
                                {
                                    text:"Escanear mais canhotos",
                                    click: function() {
                                        $(this).dialog( "close" );
                                        $("#dialog").toggleClass("d-none");
                                        scan(scanner);
                                    }
                                },
                                {
                                    text:"Iniciar identificação",
                                    click: function() {
                                        $(this).dialog( "close" );
                                        $("#dialog").toggleClass("d-none");
                                        identify(scanner);
                                    }
                                }
                            ]
                        }
                    );
                });
            } else {
                // If none were scanned
                showMessage(tag + "Não foi possível escanear, verifique se os canhotos foram colocados corretamente ou se estão presos no scanner");
            }
        } else {
            // If there was no answer from the scanner or if it was busy running another scan
            showMessage(tag + "Scanner não disponível ou desligado");
        }
    } catch(error) {
        console.error(error);
        showMessage(tag + "Falha na conexão com o scanner");
    }
    hideSpinner();
}

const openPopUp = () => {
    // Open the popup with the built data if there's any
    const popup = window.open("./cnt-files/cnt-modules/scanner-module/template/view/view-alert-tesseract-template.html", "tesseract confirmation", "width=1100 height=700");
    popup.onunload = async () => {
        disableIdentifyButton();
    }
}

/*IDENTIFICAR DE CANHOTOS ESCANEADOS*/
async function identify(scannerData = null) {
    // Disable the button while the process is running
    disableIdentifyButton(true);
    
    // The list of scanners to run the identify process
    const scannersToRun = [];
    if(scannerData) scannersToRun.push(scannerData);
    else {
        for(let scannerName of scanners) {
            scannersToRun.push({
                name: scannerName,
                scanDir: getScannerId(scannerName)
            });
        }
    }

    showMessage("Iniciando identificação...");
    try {
        showSpinner();
        for(let scanner of scannersToRun) {
            const dirData = {
                scanner: scanner.scanDir,
                user: username
            };
            const tag = "<span>[" + scanner.name + "]</span> ";
            let response;
            //CRIACAO DA LISTA ESCANEADOS
            dirData.swit = "get-file-list";
            response = await  axios.get("./cnt-files/cnt-modules/scanner-module/core/process-arquivo-escaneado-core.php?id=" + JSON.stringify(dirData));
            const scannedItems = response.data;
            //console.log(scannedItems);
            //SEPARAÇÂO DA LISTA ESCANEADOS
            if(scannedItems && scannedItems.length > 0) {
                dirData.swit = "get-file-data-process";
                showMessage(tag + "Identificando canhotos. Aguarde...");
                const tesseractData = [];
                for(const [index, item] of scannedItems.entries()) {
                    dirData.file = item;
                    showMessage(tag + "Identificando... " + (index + 1) + "/" + scannedItems.length);
                    const result = await axios.get("./cnt-files/cnt-modules/scanner-module/core/process-arquivo-escaneado-core.php?id=" + JSON.stringify(dirData));
                    const data = result.data;
                    console.log(data);
                    // Check for maximum execution time reached
                    const stringfiedData = data.toString();
                    if(stringfiedData.includes("Maximum execution time")) {
                        console.log("Maximum execution time reached!");
                    }
                    if(data.origin !== "ZBAR") {
                        data.username = username;
                        data.scannerId = scanner.scanDir;
                        data.image = item.split(".")[0] + ".jpeg";
                        tesseractData.push(data);
                        showMessage(tag + "Código de barras não identificado. Adicionando para confirmação de dados posterior.");
                    } else {
                        const dataToSave = {
                            nfe: data.nfe.toString(),
                            cnpj: data.cnpj.toString(),
                            username: username,
                            scanner: scanner.scanDir,
                            date: getDate(),
                            time: getTime()
                        }
                        console.log(dataToSave)
                        showMessage(tag + "Código de barras lido com sucesso!");
                        const logResult = await axios.post("./cnt-files/cnt-modules/scanner-module/core/add-to-log-file-core.php", dataToSave);
                    }
                }
                showMessage(tag + "Identificação terminada.");
                if(tesseractData.length > 0) {
                    localStorage.setItem("tesseract-data", JSON.stringify(tesseractData));
                    openPopUp();
                }
            } else {
                showMessage(tag + "Nenhum arquivo encontrado.");
            }
        }
        disableIdentifyButton();
    } catch (error) {
        console.log(error);
        showMessage("Ocorreu uma falha.");
        disableIdentifyButton();
    }
    hideSpinner();
}