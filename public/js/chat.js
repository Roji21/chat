// const config = window.chatConfig;
// Cek apakah variabel 'config' sudah ada di windows/scope global
if (typeof config === 'undefined') {
    var config = window.chatConfig;
}

var iduser = config.iduser;
var userSekarang = config.userSekarang;
var lawanBicara = config.lawanBicara;
var lawanfoto = config.lawanfoto;

// CSRF Token tetap diambil dari meta tag HTML
var csrfMeta = document.querySelector('meta[name="csrf-token"]');
var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

// --- State Global Pagination Chat ---
var halamanChat = 1;
var sedangMemuat = false;
var adaChatLagi = true;
var pertamaKaliMuat = true;

function muatChat(isScrollToBottom = false) {
    if (iduser == 0 || sedangMemuat) return;
    
    // Jangan paksa update halaman atas jika interval otomatis 5 detik sedang berjalan
    if (!isScrollToBottom && halamanChat > 1 && !adaChatLagi) return;

    sedangMemuat = true;
    const boxPesan = document.getElementById("box_pesan");
    if (!boxPesan) {
        sedangMemuat = false;
        return;
    }

    const tinggiSebelumnya = boxPesan.scrollHeight;
    let pageTarget = isScrollToBottom ? 1 : halamanChat;

    fetch("/chat/ambil?user_aktif=" + encodeURIComponent(iduser) + "&lawan_bicara=" + encodeURIComponent(lawanBicara) + "&page=" + pageTarget)
        .then(response => response.text())
        .then(responHtml => {
            if (responHtml.trim() === "" || responHtml.includes('Belum a .')) {
                if (pageTarget > 1) {
                    adaChatLagi = false; 
                } else {
                    boxPesan.innerHTML = responHtml;
                }
                sedangMemuat = false;
                return;
            }

            if (pageTarget === 1) {
                // Untuk Halaman 1, Kirim Pesan, atau Sinkronisasi Otomatis 5 Detik
                const isUserDiBawah = (boxPesan.scrollHeight - boxPesan.scrollTop - boxPesan.clientHeight) < 150;
                
                if (pertamaKaliMuat || isScrollToBottom || isUserDiBawah) {
                    boxPesan.innerHTML = responHtml;
                    boxPesan.scrollTop = boxPesan.scrollHeight;
                    pertamaKaliMuat = false;
                }
            } else {
                // Untuk Infinite Scroll ke atas (Pagination)
                boxPesan.insertAdjacentHTML('afterbegin', responHtml);
                boxPesan.scrollTop = boxPesan.scrollHeight - tinggiSebelumnya; // Kunci posisi scroll
                halamanChat++;
            }

            sedangMemuat = false;
        })
        .catch(error => {
            console.error('Gagal memuat chat:', error);
            sedangMemuat = false;
        });
}

function muatlist() {
    // Mengambil URL dari data attribute elemen #list
    const listElement = document.getElementById("list");
    // console.log('Error:');

    fetch( "/chat/list")
        .then(response => response.text())
        .then(responHtml => {
            listElement.innerHTML = responHtml;
        })
        .catch(error => console.error('Gagal memuat list:', error));
}

function kirimPesan() {
    var inputPesan = document.getElementById("isi_pesan");
    var pesan = inputPesan.value;
    if (pesan.trim() === "") return;

    var formData = new FormData();
    formData.append('isi_pesan', pesan);
    formData.append('dari_siapa', iduser);
    formData.append('untuk_siapa', lawanBicara);

    fetch("/chat/kirim", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text) });
        }
        inputPesan.value = "";
        
        // Reset state ke halaman awal & paksa scroll ke paling bawah
        halamanChat = 1;
        adaChatLagi = true;
        muatChat(true); 
    })
    .catch(error => {
        console.error('Detail Error Server:', error.message);
    });
}

window.intervalList = window.intervalList || null;
gas();
function gas() {
    halamanChat = 1;
    adaChatLagi = true;
    pertamaKaliMuat = true;
    
    muatChat(true); 
    muatlist();
    
    // Interval 5 detik untuk cek pesan baru halaman pertama 
    if (window.chatInterval) {
        clearInterval(window.chatInterval);
    }
    window.chatInterval = setInterval(function() {
        muatChat(false);
    }, 5000);
    mulaiIntervalList();

    // Event Listener Scroll Elemen Box Chat
    const boxPesan = document.getElementById("box_pesan");
    if (boxPesan) {
        boxPesan.addEventListener("scroll", function() {
            // Ketika menyentuh batas paling atas (0)
            if (this.scrollTop === 0 && adaChatLagi && !sedangMemuat) {
                if (halamanChat === 1) halamanChat = 2; 
                muatChat(false);
            }
        });
    }

    document.getElementById("btn_kirim").addEventListener("click", kirimPesan);
    document.getElementById("isi_pesan").addEventListener("keypress", function(e) {
        if (e.key === 'Enter') { kirimPesan(); }
    });
};

function toggleMenu(event) {
    event.stopPropagation();
    var menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
function home(event) {
    event.preventDefault();
    var yakin = confirm("Apakah Anda yakin ingin keluar dari aplikasi chat?");
    if (yakin) {
        document.getElementById('logout-form').submit();
    } else {
        document.getElementById("dropdownMenu").style.display = "none";
    }
}
function chat(event) {
    event.stopPropagation();
    var menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
function sett(event) {
    event.stopPropagation();
    var menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
function konfirmasiLogout(event) {
    event.preventDefault();
    var yakin = confirm("Apakah Anda yakin ingin keluar dari aplikasi chat?");
    if (yakin) {
        document.getElementById('logout-form').submit();
    } else {
        document.getElementById("dropdownMenu").style.display = "none";
    }
}

window.onclick = function() {
    var menu = document.getElementById("dropdownMenu");
    if (menu && menu.style.display === "block") {
        menu.style.display = "none";
    }
}

function mulaiIntervalList() {
    if (!window.intervalList) { 
        window.intervalList = setInterval(muatlist, 5000);
    }
}

// Fungsi untuk menghentikan interval muatlist
function hentikanIntervalList() {
    if (window.intervalList) {
        clearInterval(window.intervalList);
        window.intervalList = null; // Reset variabel global menjadi null
    }
}

window.hentikanIntervalList = hentikanIntervalList;
window.mulaiIntervalList = mulaiIntervalList;

function filterUser() {
    var input = document.getElementById("search_user");
    var filter = input.value.toLowerCase();
    if (filter.trim() !== "") {
    hentikanIntervalList();
    fetch("/chat/filter?user_aktif=" + encodeURIComponent(iduser) + "&filter=" + encodeURIComponent(filter))
        .then(response => response.text())
        .then(responHtml => {
            document.getElementById("list").innerHTML = responHtml;
        })
        .catch(error => console.error('Gagal memuat list:', error));
    } else {
        muatlist(); 
        mulaiIntervalList();
    }
}

// Event Delegation - Pindah Lawan Bicara
document.addEventListener('click', function(e) {
    const chatItem = e.target.closest('.chat-click');
    
    if (chatItem) {
        const idPengirim = chatItem.getAttribute('data-pengirim');
        const nama = chatItem.getAttribute('data-nama');
        const lawanfoto = chatItem.getAttribute('data-foto');
        const storageUrl = "/storage/img/";
        lawanBicara = idPengirim;
        
        const elNamaLawan = document.getElementById('nama-lawan-bicara');
        if (elNamaLawan) {
            elNamaLawan.innerText = nama;
        }
        document.querySelector('#ava-id img').src = storageUrl + lawanfoto;
        
        // RESET TOTAL STATE AGAR KEMBALI KE HALAMAN 1
        halamanChat = 1;
        adaChatLagi = true;
        sedangMemuat = false;
        pertamaKaliMuat = true;
        
        muatChat(true); 
    }
});
