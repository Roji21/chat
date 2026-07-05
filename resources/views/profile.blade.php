<head>
    
    <style>
        /* --- RESET UTAMA --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- KARTU PROFIL --- */
        .card {
            background: #ffffff;
            padding: 30px 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow:auto;
        }

        /* --- FOTO & TOMBOL UPLOAD --- */
        .avatar-zone {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }

        .avatar-frame {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid #111111;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #eaeaea;
        }

        .avatar-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .edit-trigger {
            background: none;
            border: none;
            font-size: 14px;
            color: #4f46e5;
            font-weight: 600;
            margin-top: 10px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .edit-trigger:hover {
            color: #3730a3;
        }

        /* Tombol Kamera Pop-up */
        .upload-btn {
            position: absolute;
            bottom: 32px;
            right: -4px;
            background-color: #4f46e5;
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .upload-btn:hover {
            background-color: #3730a3;
            transform: scale(1.1);
        }

        /* Kelas Utility Sembunyi */
        .hidden {
            display: none !important;
        }

        /* --- FORM INPUT --- */
        .input-group {
            width: 100%;
            margin-top: 22px;
        }

        .input-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #8e8e93;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }

        .field-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .custom-input {
            width: 100%;
            background-color: #f5f5f7;
            border: 1px solid transparent;
            color: #1c1c1e;
            padding: 12px 42px 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            outline: none;
        }

        /* Input saat dikunci (disabled) */
        .custom-input:disabled {
            color: #2c2c2e;
            background-color: #f5f5f7;
            border-color: transparent;
        }

        /* Input saat aktif di-edit */
        .custom-input:not(:disabled) {
            background-color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        /* Tombol Ikon Pensil */
        .action-icon {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #a1a1aa;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .action-icon:hover {
            color: #4f46e5;
        }

        /* Warna pensil berubah saat mode edit menyala */
        .action-icon.editing {
            color: #4f46e5;
        }

        /* Aturan pewarnaan SVG */
        .action-icon svg {
            stroke: currentColor;
        }
    </style>
</head>
<body>

    <div class="card">
        
        <!-- FOTO PROFIL -->
        <div class="avatar-zone">
            <div class="avatar-frame">
                <img id="profile-img" src="{{ auth()->user()->photo ? asset('img/' . auth()->user()->photo) : asset('img/default.jpeg') }}" alt="Profile">
            </div>
            
            <button id="btn-edit-foto" class="edit-trigger">Edit</button>

            <!-- Tombol Kamera (SVG Murni) -->
            <label id="upload-container" class="upload-btn hidden">
                <svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                    <circle cx="12" cy="13" r="3"/>
                </svg>
                <input type="file" id="input-foto" accept="image/*" class="hidden">
            </label>
        </div>

        <!-- INPUT NAMA -->
        <div class="input-group">
            <label>Name</label>
            <div class="field-container">
                <input type="text" id="input-name" value="{{ auth()->user()->name }}" disabled class="custom-input">
                <button onclick="toggleEdit('input-name', this)" class="action-icon">
                    <svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- INPUT ABOUT -->
        <div class="input-group">
            <label>About</label>
            <div class="field-container">
                <input type="text" id="input-about" value="{{ auth()->user()->about }}" disabled class="custom-input">
                <button onclick="toggleEdit('input-about', this)" class="action-icon">
                    <svg xmlns="http://w3.org" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                    </svg>
                </button>
            </div>
        </div>

    </div>

</body>

