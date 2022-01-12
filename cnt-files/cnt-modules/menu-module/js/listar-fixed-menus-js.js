/*PUT PLACE TITLE*/
function placeTitleMenu(title) {
    var titulo = document.getElementById(title).textContent;
    document.getElementById("fixedMenuTitle").innerHTML = titulo.toString();
    document.addEventListener('scroll', getViewPort);
}
placeTitleMenu("title-page");

/*GET VIEW PORT*/
function getViewPort() {
    let scroll_position = window.scrollY;
    console.log(scroll_position);
    if(scroll_position > 210) show_fixed_menu();
    if(scroll_position < 210) hide_fixed_menu();
}

var fixedMenu = document.querySelector("div.container-fixed-menu");
/*HIDE FIXED MENU*/
function hide_fixed_menu() {
    if(fixedMenu.classList.contains("d-fixed") === true){
        fixedMenu.classList.remove("d-fixed");
        fixedMenu.classList.add("d-none");
    }
}
/*SHOW FIXED MENU*/
function show_fixed_menu() {
    if(fixedMenu.classList.contains("d-none") === true){
        fixedMenu.classList.remove("d-none");
        fixedMenu.classList.add("d-fixed");
    }
}