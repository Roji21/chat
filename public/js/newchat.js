newlist();
function newlist() {
    const listElement = document.getElementById("newlist");

    fetch( "/newchat/list")
        .then(response => response.text())
        .then(responHtml => {
            listElement.innerHTML = responHtml;
        })
        .catch(error => console.error('Gagal memuat list:', error));
}