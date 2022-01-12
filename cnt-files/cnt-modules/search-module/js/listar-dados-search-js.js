/*-------------------------------------------->PATTERNS<--------------------------------------------*/
/*PATTERNS*/
var patterns_dados_search = patternsDadosSearch();
function patternsDadosSearch() {
    var patterns = {};
    patterns.module = "search";
    patterns.folder = "core";
    patterns.file = false;
    patterns.extensions = "php";
    patterns.paginations = {};
    patterns.path = createRequestPath(patterns);
    return patterns;
}
