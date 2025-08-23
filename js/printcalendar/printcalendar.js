function dragstartHandler(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    console.log('Star ID '+ev.target.id);
}

function dragoverHandler(ev) {
    ev.preventDefault();
}

function dropHandler(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
    console.log(ev.target.id);
}