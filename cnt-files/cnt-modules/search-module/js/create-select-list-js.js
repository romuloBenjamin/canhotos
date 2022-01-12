/*CREATE SENTDATA*/
function setPatternsCreateList() {
    var sentdata = {};
    sentdata.module = "usuarios";
    sentdata.folder = "jsons";
    sentdata.extensions = "json";
    return sentdata;
}
/*SET CHILDREN -> FILIAL*/
var selectFilial = document.querySelector("select#forSite");
if(selectFilial != null) {
selectFilial.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList(); sentdata.file = "filiais";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "filial");
        handleChildren(nView, sentdata, this);
    }
});
}

// Set Children -> Filiais
function onEmpresaSelectClick() {
    console.log("on click")
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList();
        sentdata.module = "public";
        sentdata.file = "empresas";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "empresa");
        handleChildren(nView, sentdata, this, true);
    }
}
// Set it on the available selects
function setOnEmpresaSelects() {
    const onEmpresaSelects = document.querySelectorAll("#forEmpresa");
    for(let select of onEmpresaSelects) select.addEventListener("click", onEmpresaSelectClick, false);
}
setOnEmpresaSelects();

/*SET CHILDREN -> DEPARTAMENTOS*/
var selectDepto = document.querySelector("select#forDepto");
if(selectDepto != null) {
selectDepto.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList(); sentdata.file = "departamentos";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "departamento");
        handleChildren(nView, sentdata, this);
    }
});
}
/*SET CHILDREN -> CARGOS*/
var selectCargos = document.querySelector("select#forCargo");
if (selectCargos != null) {
selectCargos.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList(); sentdata.file = "cargos";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "cargo");
        handleChildren(nView, sentdata, this);
    }
});
}
/*SET CHILDREN -> STATUS*/
var selectStatus = document.querySelector("select#forStatus");
if(selectStatus != null) {
selectStatus.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList(); sentdata.file = "status";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "status");
        handleChildren(nView, sentdata, this);
    }
});
}
/*SET CHILDREN -> EQUIPES*/
var selectEquipes = document.querySelector("select#forEquipe");
if(selectEquipes != null) {
selectEquipes.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList(); sentdata.file = "equipes";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "equipes");
        handleChildren(nView, sentdata, this);
    }
});
}
/*SET CHILDREN -> GENEROS*/
var selectGeneros = document.querySelector("select#forGenero");
if(selectGeneros != null) {
selectGeneros.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList(); sentdata.file = "generos";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "generos");
        handleChildren(nView, sentdata, this);
    }
});
}
/*SET CHILDREN -> PERIODOS*/
var selectPeriodos = document.querySelector("select#forPeriodo");
if(selectPeriodos != null) {
selectPeriodos.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList(); sentdata.file = "periodos";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "periodos");
        handleChildren(nView, sentdata, this);
    }
});
}
/*SET CHILDREN -> MOTORISTAS*/
var selectMotoristas = document.querySelector("select#forMotorista");
if(selectMotoristas != null) {
selectMotoristas.addEventListener('click', function(){
    if(this.children.length <= 1){
        var sentdata = setPatternsCreateList();
        sentdata.file = "transportadores"; sentdata.module = "logistica";
        sentdata.path = createRequestPath(sentdata);
        var nView = new Map();
        nView.set("ids", "ids");
        nView.set("names", "transportador");
        handleChildren(nView, sentdata, this);
    }
});
}
/*SET CHILDREN -> MOTIVOS*/
var selectMotivos = document.querySelectorAll("select#forMotivos");
selectMotivos.forEach(select => {
    if(select != null && select.onclick === null) {
        select.addEventListener('click', function() {
            if(this.children.length <= 1) {
                var sentdata = setPatternsCreateList();
                sentdata.file = "motivos-retorno-nfe"; sentdata.module = "logistica";
                sentdata.path = createRequestPath(sentdata);
                var nView = new Map();
                nView.set("ids", "ids");
                nView.set("names", "motivo");
                handleChildren(nView, sentdata, this);
            }
        });
    }
});

/*HANDLE CHILDREN ELEMENTS*/
function handleChildren(e, data, placer, prefixId = false) {
    /*CREATE MAPS*/
    var gets = new Map(e);
    var loud_request = loudRequest(data);
    fetch(loud_request)
        .then(responser => responser.json())
        .then(data => {
            const loops = data.dataset[0]?.data;
            let leftIndex = 1;
            Object.values(loops).forEach(looneys => {
                /*PLACE VALUES*/
                placer.appendChild(document.createElement("option")).setAttribute("value", looneys[gets.get("ids")]);
                /*PLACE NAMES*/
                if(looneys[gets.get("names")].split("").length > 55) {
                    var motivoNome = looneys[gets.get("names")].charAt(0).toUpperCase() + looneys[gets.get("names")].slice(1);
                    placer.querySelectorAll("option")[leftIndex].textContent = motivoNome.slice(0, 54)+"...";
                }else{
                    let textContent = "";
                    /* Prefixes "[id] - " to the text content */
                    if(prefixId === true) {
                        textContent = looneys[gets.get("ids")] + " - ";
                    }
                    textContent += looneys[gets.get("names")].charAt(0).toUpperCase() + looneys[gets.get("names")].slice(1);
                    placer.querySelectorAll("option")[leftIndex].textContent = textContent;
                }
                if(gets?.get("current") != undefined){
                    if(looneys[gets.get("names")] == gets.get("current")) 
                        placer.querySelectorAll("option")[leftIndex].setAttribute("selected", true);
                }
                leftIndex++;
            });
        })
        .catch(()=>{console.log("Falha em create Select List -> "+data.file);})
}