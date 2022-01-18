// The scan directory currently being used
let scannerId = null;
let username = null;

const spinner = document.querySelector("#spinner");
const messageBox = document.querySelector("#messageBox");
const extraMessageBox = document.querySelector("#extraMessageBox");

// Read the json with the scanners' data and add the buttons
const addScannerButtons = async () => {
    let json = await axios.get("./cnt-files/cnt-modules/scanner-module/jsons/lista-scanners-json.json");
    const buttonContainer = document.getElementById("buttonContainer");
    for(let scanner of json.data.scanners) {
        const button = document.createElement("BUTTON");
        // Remove special and normalize characters from the scanner name to use as ID and scan directory
        button.id = scanner.name.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().split(" ").join("-");
        (scanner.address != "")? button.classList.add("btn", "btn-secondary") : button.classList.add("btn", "btn-warning")
        button.innerHTML = scanner.name;
        if(scanner.name === "Identificar novamente") {
            button.addEventListener("click", (e) => { 
                get_disabled_button();
                identify();
            });
        } else {
            scanner.scanDir = button.id;
            button.addEventListener("click", () => { scan(scanner); }, false);
        }
        buttonContainer.appendChild(button);
    }
}
addScannerButtons();

/*ADD PROPERTY DISABLED*/
async function get_disabled_button(type = true) {
    var disab = document.querySelector("#identificar-novamente");
    (type === true)? disab.setAttribute("disabled", true): disab.removeAttribute("disabled");
    return;
}

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

// Get the scanner data and calls the scan process on the chosen server if available
const scan = async (scanner) => {
    scannerId = scanner.scanDir;
    showSpinner();
    showMessage("Iniciando...");
    try {
        // Request the scanning
        username = await getUsername();
        if(!username) return;
        let response = await axios.post("http://" + scanner.address + ":8090/scan", { key: scanner.name, scanDir: scanner.scanDir, username: username });
        console.log(response.data);
        // If the scan was successfull
        if(response.data.scanFinished === true) {
            // If 1 or more items were scanned
            if(response.data.scannedItems > 0) {
                showMessage("Canhotos escaneados com sucesso");
                identify();
            } else {
                // If none were scanned
                showMessage("Não foi possível escanear, verifique se os canhotos foram colocados corretamente ou se estão presos no scanner");
            }
        } else {
            // If there was no answer from the scanner or if it was busy running another scan
            showMessage("Scanner não disponível ou desligado");
        }
    } catch(error) {
        console.error(error);
        showMessage("Falha na conexão com o scanner");
    }
    hideSpinner();
}

// Get the json which contains the identify processes running at the moment
async function getIdentifyProcessesRunningJson() {
    try {
        const response = await axios.get("./cnt-files/cnt-modules/scanner-module/jsons/lista-identify-processes-running-json.json");
        return response.data;
    } catch(error) {
        console.log(error);
    }
    return {};
}

// Update the json with the identify process currently running
async function updateIdentifyProcessesRunningJson(userInfo) {
    try {
        const response = await axios.post("./cnt-files/cnt-modules/scanner-module/core/lista-identify-processes-running-core.php", userInfo);
    } catch(error) {
        console.log(error);
    }
}

const openPopUp = () => {
    // Open the popup with the built data if there's any
    const popup = window.open("./cnt-files/cnt-modules/scanner-module/template/view/view-alert-tesseract-template.html", "Confirmação de Canhotos", "width=1000 height=700");
}

// Show the loading spinner
const showSpinner = () => {
    spinner.classList.remove("d-none");
}

// Hide the loading spinner
const hideSpinner = () => {
    spinner.classList.add("d-none");
}

// Shows the passed message in the box
const showMessage = (message) => {
    messageBox.parentNode.parentNode.classList.remove("d-none");
    messageBox.parentNode.parentNode.classList.add("d-flex");
    messageBox.innerHTML = message;
    console.log(message);
}

// Show the passed message as an extra message below the message box' text
const showExtraMessage = (message) => {
    extraMessageBox.innerHTML = "\n" + message;
}

/*IDENTIFICAR DE CANHOTOS ESCANEADOS*/
async function identify() {
    let json;
    let userInfo;
    // If the user didn't press a button
    if(!scannerId) {
        // Retrieve the username
        username = await getUsername();
        // User is not logged
        if(!username) {
            showMessage("Usuário não identificado. Realize o login.");
            return;
        } else {
            // Get json if it exists
            json = await getIdentifyProcessesRunningJson();
            userInfo = json[username];
            if(userInfo) {
                // Loop through scanner names and check if the identify process was running previously for one of them
                const keys = Object.keys(userInfo);
                keys.forEach(key => {
                    if(userInfo[key]) scannerId = key;
                });
                if(scannerId) showMessage("Processo prévio de identificação encontrado.");
            }
        }
    }

    if(!scannerId) {
        get_disabled_button(false);
        showMessage("Nenhum processo prévio de identificação encontrado.");
        return;
    }

    // Get json if it exists
    json = await getIdentifyProcessesRunningJson();
    userInfo = json[username];

    // Add the new process running for the chosen scanner in the current data
    if(userInfo) {
        userInfo[scannerId] = true;
    } else {
        // Or add it as new data
        userInfo = {
            [scannerId]: true
        }
    }

    // Update the json
    await updateIdentifyProcessesRunningJson({
        [username]: userInfo
    });
    console.log({[username]: userInfo})
    
    showMessage("Iniciando identificação...");

    // GET RESULTSET
    try {
        const dirData = {
            scanner: scannerId,
            user: username
        };
        const response = await axios.get("./cnt-files/cnt-modules/scanner-module/core/listar-arquivos-escaneados-core.php?id=" + JSON.stringify(dirData));
        const scannedItems = response.data;
        console.log(scannedItems);
        if(!scannedItems) {
            showMessage("Nenhum arquivo encontrado. Realize o processo escaneamento novamente.");
            return;
        }
        showSpinner();
        showMessage("Identificando canhotos. Aguarde...");
        const tesseractData = [];
        for(const [index, item] of scannedItems.entries()) {
            dirData.file = item;
            const result = await axios.get("./cnt-files/cnt-modules/scanner-module/core/process-arquivo-escaneado-core.php?id=" + JSON.stringify(dirData));
            const data = result.data;
            // Check for maximum execution time reached
            const stringfiedData = data.toString();
            if(stringfiedData.includes("Maximum execution time")) {
                console.log("Maximum execution time reached!");
            }
            showMessage("Identificando... " + (index + 1) + "/" + scannedItems.length);
            if(data.identify.origin !== "ZBAR") {
                tesseractData.push(data);
                showExtraMessage("Código de barras não identificado. Adicionando para confirmação de dados posterior.");
            } else {
                const dataToSave = {
                    nfe: data.identify.nfe.toString(),
                    cnpj: data.identify.cnpj.toString(),
                    username: data.who,
                    scanner: data.scannerID,
                    date: getDate(),
                    time: getTime()
                }
                showExtraMessage("Código de barras lido com sucesso!");
                const logResult = await axios.post("./cnt-files/cnt-modules/scanner-module/core/add-to-log-file-core.php", dataToSave);
            }
        }
        showMessage("Identificação terminada.");
        showExtraMessage("");
        if(tesseractData.length > 0) {
            localStorage.setItem("tesseract-data", JSON.stringify(tesseractData));
            openPopUp();
        }

        // Get the current json
        json = await getIdentifyProcessesRunningJson();
    
        // Remove the current scanner from the userinfo
        userInfo = json[username];
        if(userInfo) delete userInfo[scannerId];

        await updateIdentifyProcessesRunningJson({
            [username]: userInfo
        });
        get_disabled_button(false);
    } catch (error) {
        console.log(error);
        showMessage("Ocorreu uma falha.");
    }

    hideSpinner();
}