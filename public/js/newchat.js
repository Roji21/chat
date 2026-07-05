
if (typeof config === 'undefined') {
    var config = window.chatConfig;
}
var sedangMengirim = false;

// Event Delegation - Pindah Lawan Bicara
document.addEventListener('click', function(e) {
    const chatItem = e.target.closest('.contact-item');
    
    if (chatItem) {
        if (sedangMengirim) return;
        // Ambil data dari atribut elemen yang diklik
        var idPengirim = chatItem.getAttribute('data-lawan');
        var nama = chatItem.getAttribute('data-nama');
        var lawanfoto = chatItem.getAttribute('data-foto');
        sedangMengirim = true;
        setTimeout(() => {
            // Setelah selesai, kembalikan status menjadi false agar bisa diklik lagi
            sedangMengirim = false; 
        }, 500);
        // console.log(idPengirim,lawanfoto);

        window.chatConfig.lawanBicara = idPengirim;
        window.chatConfig.lawanfoto = lawanfoto;
        window.chatConfig.lawannama = nama;
        if (typeof renderHalaman === 'function') {
            renderHalaman('chat');
            
        }
    }
});

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

function searchnew() {
    var input = document.getElementById("search_new");
    var filter = input.value.toLowerCase();
    if (filter.trim() !== "") {
    hentikanIntervalList();
    fetch("/newchat/filter?user_aktif=" + encodeURIComponent(iduser) + "&filter=" + encodeURIComponent(filter))
        .then(response => response.text())
        .then(responHtml => {
            document.getElementById("newlist").innerHTML = responHtml;
        })
        .catch(error => console.error('Gagal memuat list:', error));
    } else {
        newlist();
    }
}