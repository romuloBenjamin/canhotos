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

// Return the date formatted as dd/MM/yy
const getDate = () => {
    const date = new Date(Date.now());
    const day = date.getDate().toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });
    const month = (date.getMonth() + 1).toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });
    const year = date.getFullYear().toString().substring(2);
    const formattedDate = day + "/" + month + "/" + year;
    return formattedDate;
}

// Return the current time as HHhMM
const getTime = () => {
    const date = new Date(Date.now());
    const hours = date.getHours().toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });;
    const minutes = date.getMinutes().toLocaleString('en-US', {
        minimumIntegerDigits: 2,
        useGrouping: false
    });
    const time = hours + "h" + minutes;
    console.log(time);
    return time;
}