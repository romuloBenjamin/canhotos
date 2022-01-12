/*SET PAGINATIONS PATTERNS*/
function setPaginationsPatterns() {
    var patterns = {};
    patterns.module = "public";
    patterns.folder = "core";
    patterns.file = "paginations";
    patterns.extensions = "php";
    patterns.swit = false;
    patterns.paginations = setPaginations();
    patterns.path = createRequestPath(patterns);
    return patterns;
}
/*--------------------------------------------------->INIT PAGINATIONS<---------------------------------------------------*/
/*SET DEFAULT PAGINATIONS -> patterns*/
function setPaginations() {
    var pages = {ini:0, max:15};
    if(location?.search != "") {
        var data_get = location.search.replace("?", "");
        var xplode = data_get.split("&");
        var nMap = new Map();
        xplode.forEach(getdata => {
            const iSplit = getdata.split("=");
            nMap.set(iSplit[0], iSplit[1]);
            nMap.set("current", iSplit[1]);
            pages = {...pages, ...Object.fromEntries(nMap)};
        });
    }
    return pages;
}
/*SET PAGINATIONS TO LOUD IN PAGE*/
async function prepare_paginations(params) {
    var default_patterns = setPaginationsPatterns();
    default_patterns.paginations = params.paginations;
    default_patterns.swit = "listar-retronos-nfe-paginations";
    sendRequest(default_patterns);
}

/*SET UPDATE TO SEARCH DATA*/
async function update_paginations(params) {
    var patterns = setPaginationsPatterns();
    patterns.paginations = params.paginations;
    patterns.swit = "listar-retronos-nfe-paginations";
    sendRequest(patterns);
}
/*--------------------------------------------------->RECEIVE PAGINATIONS<---------------------------------------------------*/
async function receiveRequest_paginations(params, patterns) {
    /*ADD TOTAL DE PAGINAS*/
    var paginas = params.total_registros/params.max;
    params.total_paginas = Math.ceil(paginas);
    if(paginas%1!=0) params.total_paginas = Math.ceil(paginas);
    /*UPDATE PAGES*/
    if(location?.search != 0) {
        var set_paginations = setPaginations();
        params.current = parseInt(set_paginations.page);
        params.pages = parseInt(set_paginations.page)+1;
    }
    //console.log(params);
    /*ADD PLACERS*/
    var placers = document.querySelector("div.paginations-controls");
    placeSMalls(params, placers, "qtdRegistros");
    /*ACTIVE NEXT AND LAST*/
    activeNEXTLAST(params, placers);
    /*ACTIVE PREV AND FIRST*/
    activePREVFIRST(params, placers);
    /*ACTIVE MOD PAGES*/
    activeMODPAGES(params, placers);
}
/*PLACE SMALLS*/
function placeSMalls(params, placers, textContent) {
    var textRegistros = params.current+" de "+params.total_paginas+" páginas,<br>total de "+params.total_registros+" registros.";
    if(placers.querySelector("div#"+textContent).classList.contains("d-none") === true) {
        placers.querySelector("div#"+textContent).classList.remove("d-none");
        placers.querySelector("div#"+textContent).classList.add("d-flex");
    }
    placers.querySelector("div#"+textContent).innerHTML = textRegistros;
}
/*ACTIVE NEXT LAST PAGE*/
function activeNEXTLAST(params, placers) {
    var active = ["pageNXT", "pageLST"];
    active.forEach((dons, indice) => {
        const nPlacers = placers.querySelector("div.paginations-container > ul > li#"+dons);
        if(indice === 0) {
            if(params.pages === 1) params.pages = params.pages +1;
            nPlacers.querySelector("a").setAttribute("href", "?page="+params.pages);
        }
        if(indice === 1) {
            nPlacers.querySelector("a").setAttribute("href", "?page="+params.total_paginas);
        }
        if((params.pages+1) >= params.total_paginas) nPlacers.classList.add("d-none");
    });
}
/*ACTIVE PREV FIRST PAGE*/
function activePREVFIRST(params, placers) {
    var active = ["pageFST", "pagePRV"];
    active.forEach((dons, indice) => {
        const nPlacers = placers.querySelector("div.paginations-container > ul > li#"+dons);
        if(params.pages >= 3){
            if(indice === 1) {
                if(nPlacers.classList.contains("d-none")) nPlacers.classList.remove("d-none");
                nPlacers.querySelector("a").setAttribute("href", "?page="+(params.pages-2));
                nPlacers.style.borderLeftWidth = "1px";
                nPlacers.style.borderTopLeftRadius = ".25rem";
                nPlacers.style.borderBottomLeftRadius = ".25rem";
            }
        }
        if(params.pages >= 4){
            if(indice === 1) {
                if(nPlacers.classList.contains("d-none")) nPlacers.classList.remove("d-none");
                nPlacers.querySelector("a").setAttribute("href", "?page="+(params.pages-2));
                nPlacers.style.borderLeftWidth = "0";
                nPlacers.style.borderTopLeftRadius = "0";
                nPlacers.style.borderBottomLeftRadius = "0";
            }
            if(indice === 0){
                if(nPlacers.classList.contains("d-none")) nPlacers.classList.remove("d-none");
                nPlacers.querySelector("a").setAttribute("href", "?page=1");
            }
        }
    });
}
/*ACTIVE MOD PAGES*/
function activeMODPAGES(params, placers) {
    var nPlacers = placers.querySelectorAll("div.paginations-container > ul")[1];
    nPlacers.innerHTML = "";
    for (let index = 1; index <= 3; index++) {
        if(params?.current === undefined) params.current = params.pages-1;
        /*PLACE ELEMENTS*/
        nPlacers.appendChild(document.createElement("li")).id = "itens-0"+index;
        nPlacers.querySelector("li#itens-0"+index).classList.add("list-group-item");
        nPlacers.querySelector("li#itens-0"+index).appendChild(document.createElement("a"));
        if(index === 1) {
            nPlacers.querySelector("li#itens-0"+index).classList.add("current-page");
            nPlacers.querySelector("li#itens-0"+index+" > a").setAttribute("href", "?page="+params.current);
            nPlacers.querySelector("li#itens-0"+index+" > a").innerHTML = params.current;
        }
        if(index === 2) {
            nPlacers.querySelector("li#itens-0"+index+" > a").setAttribute("href", "?page="+params.pages);
            nPlacers.querySelector("li#itens-0"+index+" > a").innerHTML = params.pages;
        }
        if(index === 3) {
            nPlacers.querySelector("li#itens-0"+index+" > a").setAttribute("href", "?page="+(params.pages + 1));
            nPlacers.querySelector("li#itens-0"+index+" > a").innerHTML = (params.pages + 1);
        }
    }    
}