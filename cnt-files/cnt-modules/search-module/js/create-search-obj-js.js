/*------------------------------------------>PATTERNS<------------------------------------------*/
function setRetornosNFESearchPatterns() {
    var patterns = {};
    patterns.module = "search";
    patterns.folder = "core";
    patterns.file = false;
    patterns.extensions = "php";
    patterns.swit = false;
    patterns.paginations = setPaginations();
    patterns.path = createRequestPath(patterns);
    return patterns;
}
/*---------------------------------------->INIT SCRIPT<----------------------------------------*/
function setSearch() {    
    var select_inputs = ["select#forMotorista", "select#forEquipe", "select#forMotivos"];
    var input_inputs = ["input#search"];
    var button_inputs = ["button#forSearch"];
    
    /*SELECT INPUTS -> on CHANGE*/
    select_inputs.forEach(selects => {
        document.querySelector(selects).addEventListener("change", createSearchMap);
    });
    /*SELECT INPUTS -> on KEYUP*/
    input_inputs.forEach(inputs => {
        document.querySelector(inputs).addEventListener("keyup", createSearchMap);
    });
    /*SELECT INPUTS -> on CLICK*/
    button_inputs.forEach(buttons => {
        document.querySelector(buttons).addEventListener("click", createSearchMap);
    });
}
setSearch();
/*CRAETE OBJ*/
async function createSearchMap() {
    /*INIT PATTERNS*/
    var patterns_search = setRetornosNFESearchPatterns();
    /*VALORES IDENTIFICADOS*/
    var tag_name = this.tagName.toString().toLowerCase();
    var storage_positions = this.id.replace("for", "").toLowerCase();
    if(tag_name === "select"){
        var values = {id: this.value, name: this.selectedOptions[0].textContent};
        if(values.id === "*") removeStorage(storage_positions);
        if(values.id != "*") updateStorage(values, storage_positions);
    }
    if(tag_name != "select"){
        if(tag_name != "button") {
            var values = {nf: this.value, romaneio: this.value};
            if(this.value === "") {
                removeStorage("nf");
                removeStorage("romaneio");
            }
            if(this.value !=  "") updateStorage(values, false);
            
        }
        if(tag_name === "button") buscarRetornosSearch();
    }
}
/*UPDATE LOCALSTORAGE SEARCHDATA*/
function updateStorage(params, positions) {
    var search = localStorage?.getItem("searchdata");
    if(search === null){
        var search = {searchdata: {}};
        localStorage.setItem("searchdata", JSON.stringify(search));
        updateStorage(params, positions);
    }else{
        var open_storage = JSON.parse(search);
        if(positions != false) open_storage.searchdata[positions] = params;
        if(positions === false) open_storage.searchdata = {...open_storage.searchdata, ...params};
        return localStorage.setItem("searchdata", JSON.stringify(open_storage));
    }
}
/*REMOVE LOCALSTORAGE SEARCHDATA*/
function removeStorage(positions) {
    var open_storage = JSON.parse(localStorage?.getItem("searchdata"));
    delete open_storage.searchdata[positions];
    return localStorage.setItem("searchdata", JSON.stringify(open_storage));
}
/*---------------------------------------->SEND RESQUEST SEARCH<----------------------------------------*/
/*BUSCAR RETORNO NFE*/
function buscarRetornosSearch() {
    //console.log(localStorage.getItem("searchdata"));
    if(localStorage.getItem("searchdata") != null) {
        /*IF UNDEFINED OR STORAGE {}*/
        if(localStorage?.searchdata === undefined) location.reload();
        if(localStorage?.searchdata === "{\"searchdata\":{}}") location.reload();
        /*CALL PATTERNS PAGE*/
        var patterns_search = setRetornosNFESearchPatterns(); patterns_search.module = "logistica"; 
        patterns_search.file = "retornos-nfe"; patterns_search.paginations.search = JSON.parse(localStorage.getItem("searchdata"));
        patterns_search.swit = "listar-retornos-nfe-search"; patterns_search.path = createRequestPath(patterns_search);
        /*SEND REQUEST*/
        sendRequest(patterns_search);
        update_paginations(patterns_search);
    }
}