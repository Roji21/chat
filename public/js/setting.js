
document.addEventListener('click', function(e) {
    // Mencari elemen terdekat dengan class .setting-item
    const settingItem = e.target.closest('.setting-item');

    // Validasi agar kode hanya berjalan jika .setting-item berhasil ditemukan
    if (settingItem) {
        // Mengambil nilai dari atribut "page"
        const pageName = settingItem.getAttribute('page');
        muatPage(pageName);
    }else{
        // console.log("Elemen dengan class .setting-item tidak ditemukan.");
    }
});

function muatPage(page) {
    const dynamicSetting = document.getElementById('dynamic-content-setting');
    fetch("/setting/" + page)
        .then(response => response.text())
        .then(responHtml => {
            if (dynamicSetting) {
                dynamicSetting.innerHTML = responHtml;
            }
        })
        .catch(error => console.error('Gagal memuat halaman:', error));

}

document.onclick = function(e) {
    // Cari elemen secara dinamis saat klik terjadi
    var btnEditFoto = document.getElementById('btn-edit-foto');
    var uploadContainer = document.getElementById('upload-container');

    // Pastikan kedua elemen tersebut sudah ada di halaman
    if (!btnEditFoto || !uploadContainer) return;

    // Jika yang diklik adalah tombol "Edit" foto
    if (e.target === btnEditFoto) {
        e.stopPropagation();
        uploadContainer.classList.toggle('hidden');
        return;
    }

    // Jika mengklik di luar tombol edit dan kotak upload, sembunyikan kotak upload
    if (!uploadContainer.contains(e.target)) {
        uploadContainer.classList.add('hidden');
    }
};

document.onchange = function(e) {
    var inputFoto = document.getElementById('input-foto');
    var profileImg = document.getElementById('profile-img');
    var uploadContainer = document.getElementById('upload-container');

    if (e.target === inputFoto && inputFoto.files && inputFoto.files[0]) {
        var fileInput = inputFoto.files[0];

        // 1. Tampilkan preview di browser terlebih dahulu
        var reader = new FileReader();
        reader.onload = function(event) {
            if (profileImg) profileImg.src = event.target.result;
        };
        reader.readAsDataURL(fileInput);
        if (uploadContainer) uploadContainer.classList.add('hidden');

        // 2. Kirim file ke Laravel menggunakan Fetch API
        var formData = new FormData();
        formData.append('foto_profil', fileInput);

        // Ambil CSRF Token dari meta tag HTML Laravel
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/setting/upfoto', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken // Wajib di Laravel untuk keamanan
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Foto berhasil disimpan:', data.path);
            } else {
                alert('Gagal mengunggah foto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
};

function toggleEdit(inputId, button) {
    const inputField = document.getElementById(inputId);
    
    if (inputField.disabled) {
        inputField.disabled = false;
        inputField.focus();
        button.classList.add('editing');
    } else {
        inputField.disabled = true;
        button.classList.remove('editing');

        // AMBIL NILAI BARU DAN KIRIM KE LARAVEL
        var textValue = inputField.value;
        simpanTeksKeLaravel(inputId, textValue);
    }
}

var simpanTeksKeLaravel = function(type, textValue) {
    // Ambil token CSRF agar tidak diblokir oleh Laravel
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    var formData = new FormData();
    formData.append('tipe_input', type);    // Mengirim string 'input-name' atau 'input-about'
    formData.append('nilai_teks', textValue); // Mengirim teks baru yang diketik user

    fetch('/setting/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            console.log('Berhasil: ' + data.message);
        } else {
            alert('Gagal menyimpan: ' + data.message);
        }
    })
    .catch(function(error) {
        console.error('Terjadi kesalahan sistem:', error);
    });
};
