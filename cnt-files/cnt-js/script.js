// Go to page
function goToPage(page) {
    location.href = page;
}

// Go back to previous page
function goBack() {
    var nArray = [];
    var location_path = location.pathname.split("/");
    for (let index = 0; index < (location_path.length - 1); index++) {
        const element = location_path[index];
        nArray.push(element);
    }
    location.href = location.origin + nArray.join("/");
}