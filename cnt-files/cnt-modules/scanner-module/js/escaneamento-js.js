// The scan directory currently being used
let scanDir = null;
let username = null;

const spinner = document.querySelector("#spinner");
const messageBox = document.querySelector("#result");

// Read the json with the scanners' data and add the buttons
const addScannerButtons = async () => {
    let json = await axios.get("./cnt-files/cnt-modules/scanner-module/jsons/lista-scanners-json.json");
    console.log(json.data.scanners);
    const buttonContainer = document.getElementById("buttonContainer");
    for(let scanner of json.data.scanners) {
        const button = document.createElement("BUTTON");
        // Remove special and normalize characters from the scanner name to use as ID and scan directory
        button.id = scanner.name.normalize('NFD').replace(/[\u0300-\u036f]/g, "").toLowerCase().split(" ").join("-");
        scanner.scanDir = button.id;
        button.classList.add("btn", "btn-secondary");
        button.innerHTML = scanner.name;
        button.addEventListener("click", () => {
            scan(scanner);
        }, false);
        buttonContainer.appendChild(button);
    }
}

addScannerButtons();

// Get the current user name
const getUsername = async () => {
    try {
        const response = await axios.get("./cnt-files/cnt-modules/scanner-module/core/get-username-core.php");
        //console.log(response);
        return response.data;
    } catch(error) {
        console.log(error);
    }
    return null;
}

// Get the scanner data and calls the scan process on the chosen server if available
const scan = async (scanner) => {
    scanDir = scanner.scanDir;
    console.log("NOME DO SCANNER: "+scanner.name);
    showSpinner();
    showMessage("Iniciando...");
    try {
        // Request the scanning
        username = await getUsername();
        if(!username) return;
        let response = await axios.post("http://" + scanner.address + ":8090/scan", { key: scanner.name, scanDir: scanner.scanDir, username: username });
        //console.log(response.data);
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

/*REFAZER IDENTIFICAÇÃO DE CANHOTOS JA ESCANEADOS*/
async function retry() {
    identify();
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
        //console.log(response.data);
    } catch(error) {
        console.log(error);
    }
}

const openPopUp = () => {
    // Open the popup with the built data if there's any
    const popup = window.open("./cnt-files/cnt-modules/scanner-module/template/view/view-alert-tesseract-template.html", "Confirmação de Canhotos", "width=800 height=500");
    //console.log(popup);
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
    messageBox.parentNode.classList.remove("d-none");
    messageBox.parentNode.classList.add("d-flex");
    messageBox.innerHTML = message;
}

/*IDENTIFICAR DE CANHOTOS ESCANEADOS*/
async function identify() {
    let json;
    let userInfo;
    // If the user didn't press a button
    if(!scanDir) {
        // Retrieve the username
        username = await getUsername();
        // User is not logged
        if(!username) {
            showMessage("Usuário não identificado. Realize o login.");
            return;
        } else {
            // Get json if it exists
            json = await getIdentifyProcessesRunningJson();
            console.log("Current: ", json);
            userInfo = json[username];
            // Loop through scanner names and check if the identify process was running previously for one of them
            const keys = Object.keys(userInfo);
            keys.forEach(key => {
                if(userInfo[key]) scanDir = key;
            });
        }
    } else {
        // Get json if it exists
        json = await getIdentifyProcessesRunningJson();
        //console.log("Current: ", json);
        userInfo = json[username];

        // Add the new process running for the chosen scanner in the current data
        if(userInfo) {
            userInfo[scanDir] = true;
        } else {
            // Or add it as new data
            userInfo = {
                [scanDir]: true
            }
        }

        //console.log("Updated: ", username, userInfo);

        // Update the json
        await updateIdentifyProcessesRunningJson({
            [username]: userInfo
        });
    }

    if(!scanDir) {
        showMessage("Nenhum processo prévio de identificação encontrado.");
        return;
    }
    
    showMessage("Iniciando identificação...");
    //console.log("Identificação iniciada em " + scanDir);

    // GET RESULTSET
    try {
        showSpinner();
        showMessage("Identificando canhotos. Aguarde...");
        const dirData = {
            scanner: scanDir,
            user: username
        };
        const response = await axios.get("./cnt-files/cnt-modules/scanner-module/core/listar-arquivos-escaneados-core.php?id=" + JSON.stringify(dirData));
        const data = response.data;
        console.log(data);        
        
        for(let item of data) {
            dirData.file = item;
            const result = await axios.get("./cnt-files/cnt-modules/scanner-module/core/process-arquivo-escaneado-core.php?id=" + JSON.stringify(dirData));
            console.log(result.data);
        }
        showMessage("Identificação terminada.");
        if(data.length > 0) {
            localStorage.setItem("tesseract-data", JSON.stringify(data));
            openPopUp();
        }
        //console.log("Removing " + scanDir);

        // Get the current json
        json = await getIdentifyProcessesRunningJson();
        //console.log("Current: ", json);
    
        // Remove the current scanner from the userinfo
        userInfo = json[username];
        if(userInfo) delete userInfo[scanDir];
    
        await updateIdentifyProcessesRunningJson({
            [username]: userInfo
        });
        //console.log("Updated: ", username, userInfo);
    
        //console.log("Identificação terminada em " + scanDir);
    } catch (error) {
        //console.log(error);
        showMessage("Ocorreu uma falha.");
    }

    hideSpinner();
}