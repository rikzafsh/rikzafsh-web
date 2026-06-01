<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Perjalanan - HafidzQuiz</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar Styling */
        .sidebar { width: 320px; background: white; border-right: 1px solid #e0e6ed; display: flex; flex-direction: column; overflow-y: auto; flex-shrink: 0;}
        .sidebar-header { background: #1abc9c; color: white; padding: 20px; text-align: center; font-weight: bold; font-size: 18px; position: sticky; top: 0; z-index: 10; }
        .nav-table { width: 100%; border-collapse: collapse; }
        .nav-table td { padding: 10px; border-bottom: 1px solid #f0f0f0; }
        .nav-table .juz-label { font-weight: bold; color: #7f8c8d; font-size: 14px; text-align: center; width: 60px; background: #f9fbfc;}
        .hizb-btn { width: 100%; padding: 8px; border: 1px solid #bdc3c7; background: #fff; border-radius: 6px; cursor: pointer; font-weight: bold; color: #2c3e50; transition: 0.2s; }
        .hizb-btn:hover { background: #ecf0f1; border-color: #95a5a6; }
        .hizb-btn.active { background: #3498db; color: white; border-color: #2980b9; box-shadow: 0 2px 5px rgba(52,152,219,0.3); }

        /* Main Content */
        .main-content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; scroll-behavior: smooth; }
        .top-bar { padding: 20px; text-align: center; border-bottom: 2px solid #e0e6ed; background: #fff; position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 10px rgba(0,0,0,0.05);}
        .total-xp { display: inline-flex; align-items: center; background: #fff; border-radius: 12px; padding: 10px 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); font-weight: bold; color: #f39c12; border: 1px solid #f1c40f;}
        
        /* Map Area (Thumn Sections) */
        .map-area { padding: 30px 20px; display: flex; flex-direction: column; align-items: center; gap: 50px; }
        
        .thumn-section { width: 100%; max-width: 600px; display: flex; flex-direction: column; align-items: center; }
        .thumn-header { background: white; padding: 15px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; margin-bottom: 30px; width: 100%; box-sizing: border-box;}
        .thumn-header h3 { margin: 0 0 5px 0; color: #2c3e50; font-size: 20px; text-transform: uppercase; }
        .thumn-header p { margin: 0; color: #7f8c8d; font-size: 15px; font-family: monospace;}

        /* Nodes Path */
        .nodes-container { position: relative; display: flex; flex-direction: column; align-items: center; gap: 30px; width: 100%; padding: 20px 0; }
        .path-line { position: absolute; width: 6px; background: #e0e6ed; top: 0; bottom: 0; left: 50%; transform: translateX(-50%); z-index: 1; border-radius: 3px; }
        
        .node-wrapper { position: relative; z-index: 2; display: flex; justify-content: center; width: 100%; }
        
        /* Kotak Melengkung (Round-Cornered Square) */
        .node-box { 
            width: 70px; height: 70px; border-radius: 20px; 
            display: flex; justify-content: center; align-items: center; cursor: pointer;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15); transition: transform 0.2s;
            border: 4px solid #fff; position: relative;
        }
        .node-box:hover { transform: scale(1.1); }
        .node-box svg { width: 32px; height: 32px; fill: white; }
        
        .session-badge { position: absolute; bottom: -10px; right: -10px; background: white; color: #2c3e50; font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border: 1px solid #ecf0f1;}
        .node-locked { background-color: #e0e6ed !important; cursor: not-allowed; box-shadow: none; }
        .node-locked svg { fill: #95a5a6; }

        .loading-overlay { position: absolute; inset: 0; background: white; z-index: 100; display: flex; flex-direction: column; justify-content: center; align-items: center; font-size: 20px; font-weight: bold; color: #3498db; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">HafidzQuiz</div>
    <table class="nav-table" id="nav-table"></table>
</div>

<div class="main-content">
    <div id="loading" class="loading-overlay">Memuat Data Quran...</div>
    
    <div class="top-bar">
        <div class="total-xp">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#f1c40f" style="margin-right:8px;"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
            Total XP: <span id="text-total-xp" style="margin-left: 5px;">Memuat...</span>
        </div>
    </div>

    <div class="map-area" id="map-area">
        </div>
</div>

<script>
    const ASSET_URL = '<?= rtrim(base_url(), '/') ?>'; 
    const API_URL = '<?= rtrim(site_url(), '/') ?>';

    // 8 Warna sesuai permintaan
    const thumnColors = ['#e9162d', '#f28200', '#ffdb28', '#1fb819', '#00e1da', '#007bd8', '#8f2be7', '#fb4fd9'];
    
    // Ikon murni SVG
    const iconPlay = `<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`;
    const iconStar = `<svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;
    const iconLock = `<svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>`;

    let quranMeta = { surahs: [], thumns: [] };
    
    // [PERBAIKAN 2] Ambil Hizb terakhir dari localStorage, bukan reset ke 1 terus
    let currentHizb = parseInt(localStorage.getItem('kuis_hizb')) || 1;
    let userProgress = []; // Variabel global untuk menyimpan data dari database

    document.addEventListener('DOMContentLoaded', async () => {
        await fetchMetaData();
        await fetchMapData(); // Panggil fetch progress dari database
        generateSidebar();
        renderMap(currentHizb);
    });

    async function fetchMetaData() {
        try {
            const [surahRes, thumnRes] = await Promise.all([
                fetch(`${ASSET_URL}/data/surah-list.json`),
                fetch(`${ASSET_URL}/data/thumn.json`)
            ]);
            quranMeta.surahs = await surahRes.json();
            const rawThumns = await thumnRes.json();
            
            quranMeta.thumns = calculateThumnBounds(rawThumns, quranMeta.surahs);
            document.getElementById('loading').style.display = 'none';
        } catch (error) {
            document.getElementById('loading').innerHTML = `<span style="color:red">Error: Gagal memuat data aset. Pastikan surah-list.json & thumn.json ada.</span>`;
        }
    }

    function calculateThumnBounds(thumns, surahs) {
        for (let i = 0; i < thumns.length; i++) {
            let curr = thumns[i];
            let next = thumns[i + 1];

            let startSura = parseInt(curr.sura_number || curr.sura_id || curr.sura || curr.surah || 1);
            let startAya = parseInt(curr.starting_aya || curr.aya || curr.ayah || 1);

            let endSura, endAya;

            // [PERBAIKAN 3] Logika Next Thumn yang Jauh Lebih Tangguh untuk Hizb Akhir
            if (next) { 
                let nextSura = parseInt(next.sura_number || next.sura_id || next.sura || next.surah || 1);
                let nextAya = parseInt(next.starting_aya || next.aya || next.ayah || 1);

                if (nextAya === 1) {
                    endSura = nextSura - 1;
                    let prevSuraInfo = surahs.find(s => parseInt(s.id || s.number || s.surah_id) === endSura);
                    endAya = prevSuraInfo ? parseInt(prevSuraInfo.num_ayah || prevSuraInfo.number_of_ayahs || 1) : 1;
                } else {
                    endSura = nextSura;
                    endAya = nextAya - 1;
                }
            } else {
                endSura = 114; endAya = 6; // Pasti Surah An-Nas ayat 6
            }

            let bounds = [];
            for (let s = startSura; s <= endSura; s++) {
                let sInfo = surahs.find(x => parseInt(x.id || x.number || x.surah_id) === s);
                if (!sInfo) continue;

                let sNumAyah = parseInt(sInfo.num_ayah || sInfo.number_of_ayahs || 0);
                let segStart = (s === startSura) ? startAya : 1;
                let segEnd = (s === endSura) ? endAya : sNumAyah;
                let sName = (sInfo.latin || sInfo.englishName || sInfo.name || `Surah ${s}`).trim();

                bounds.push({ sura: s, name: sName, start: segStart, end: segEnd });
            }
            curr.bounds = bounds;

            if (bounds.length > 0) {
                let f = bounds[0]; let l = bounds[bounds.length - 1];
                if (f.sura === l.sura) curr.displayStr = `${f.name} (${f.sura}): ${f.start} - ${l.end}`;
                else curr.displayStr = `${f.name} (${f.sura}):${f.start} -- ${l.name} (${l.sura}):${l.end}`;
            } else {
                curr.displayStr = `Gagal menghitung batas ayat`;
            }
        }
        return thumns;
    }

    function generateSidebar() {
        const table = document.getElementById('nav-table');
        let html = '';
        for (let juz = 1; juz <= 30; juz++) {
            let hizbA = (juz * 2) - 1;
            let hizbB = juz * 2;
            html += `
                <tr>
                    <td class="juz-label">JUZ ${juz}</td>
                    <td><button class="hizb-btn ${hizbA === currentHizb ? 'active' : ''}" onclick="selectHizb(${hizbA})">HIZB ${hizbA}</button></td>
                    <td><button class="hizb-btn ${hizbB === currentHizb ? 'active' : ''}" onclick="selectHizb(${hizbB})">HIZB ${hizbB}</button></td>
                </tr>
            `;
        }
        table.innerHTML = html;
        // Scroll sidebar otomatis ke hizb yang aktif
        setTimeout(() => {
            const activeBtn = document.querySelector('.hizb-btn.active');
            if(activeBtn) activeBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    function selectHizb(hizbNum) {
        currentHizb = hizbNum;
        localStorage.setItem('kuis_hizb', hizbNum); // Simpan pilihan terbaru
        generateSidebar(); 
        renderMap(hizbNum);
        document.querySelector('.main-content').scrollTo(0, 0);
    }

    function renderMap(hizbNum) {
        const mapArea = document.getElementById('map-area');
        mapArea.innerHTML = ''; 

        const hizbThumns = quranMeta.thumns.filter(t => parseInt(t.hizb_number || t.hizb) === hizbNum);

        hizbThumns.forEach((thumnObj, tIndex) => {
            const color = thumnColors[tIndex % 8]; 
            const thumnNum = thumnObj.thumn || (tIndex + 1);

            const section = document.createElement('div');
            section.className = 'thumn-section';
            
            let html = `
                <div class="thumn-header" style="border-bottom: 4px solid ${color};">
                    <h3>HIZB ${hizbNum} - THUMN ${thumnNum}</h3>
                    <p>${thumnObj.displayStr}</p>
                </div>
                <div class="nodes-container">
                    <div class="path-line"></div>
            `;

            for (let n = 1; n <= 10; n++) {
                // [PERBAIKAN 1] Dinamis membaca dari Database!
                // Filter data untuk Node ini
                let nodeHistory = userProgress.filter(p => 
                    parseInt(p.hizb_number) === hizbNum && 
                    parseInt(p.thumn_number) === thumnNum && 
                    parseInt(p.node_number) === n
                );
                
                // Cari subnode tertinggi yang sudah diselesaikan (maks 3)
                let subnodesDone = nodeHistory.length > 0 ? Math.max(...nodeHistory.map(x => parseInt(x.subnode_number))) : 0;
                
                // Cek progress Node sebelumnya untuk membongkar gembok
                let prevNodeHistory = userProgress.filter(p => 
                    parseInt(p.hizb_number) === hizbNum && 
                    parseInt(p.thumn_number) === thumnNum && 
                    parseInt(p.node_number) === (n - 1)
                );
                let prevSubnodesDone = prevNodeHistory.length > 0 ? Math.max(...prevNodeHistory.map(x => parseInt(x.subnode_number))) : 0;

                const isCompleted = subnodesDone >= 3;
                
                // Node terbuka jika: Ini node pertama, ATAU node sebelumnya sudah subnode 3, ATAU node ini sedang setengah jalan.
                const isCurrent = !isCompleted && (n === 1 || prevSubnodesDone >= 3 || subnodesDone > 0);
                const isLocked = !isCompleted && !isCurrent;
                
                let icon = iconLock;
                let boxClass = 'node-box node-locked';
                let style = '';
                let badge = '';

                if (isCompleted) {
                    icon = iconStar;
                    boxClass = 'node-box';
                    style = `background-color: ${color};`;
                } else if (isCurrent) {
                    icon = iconPlay;
                    boxClass = 'node-box';
                    style = `background-color: ${color};`;
                    badge = `<div class="session-badge">${subnodesDone}/3</div>`; // Dinamis!
                }

                // Bersihkan tanda kutip tunggal agar aman dimasukkan ke atribut HTML
                let thumnSafeJson = JSON.stringify(thumnObj).replace(/'/g, "&#39;");

                html += `
                    <div class="node-wrapper">
                        <div class="${boxClass}" style="${style}" ${!isLocked ? `onclick='startQuiz(${thumnSafeJson}, ${n}, ${subnodesDone})'` : ''}>
                            ${icon}
                            ${badge}
                        </div>
                    </div>
                `;
            }
            html += `</div>`;
            section.innerHTML = html;
            mapArea.appendChild(section);
        });
    }

    function startQuiz(thumnObj, nodeIndex, subnodesDone) {
        localStorage.setItem('kuis_bounds', JSON.stringify(thumnObj.bounds));
        localStorage.setItem('kuis_hizb', currentHizb);
        localStorage.setItem('kuis_thumn', thumnObj.thumn || 1);
        localStorage.setItem('kuis_node', nodeIndex);
        
        // Mempersiapkan subnode selanjutnya yang harus dimainkan (1, 2, atau 3)
        let nextSubnode = subnodesDone >= 3 ? 1 : (subnodesDone + 1); 
        localStorage.setItem('kuis_subnode', nextSubnode);
        
        localStorage.setItem('kuis_rules', JSON.stringify(['sambung', 'sebelum', 'lengkapi']));
        
        window.location.href = `${API_URL}/hafidzquiz/play`;
    }

    async function fetchMapData() {
        try {
            const response = await fetch(`${API_URL}/HafidzQuizGame/getMapData`);
            if (response.ok) {
                const data = await response.json();
                
                // Ambil Total XP
                document.getElementById('text-total-xp').innerText = data.total_xp || 0;
                
                // Ambil seluruh progress pengguna dan masukkan ke variabel global
                userProgress = data.progress || []; 
                
                // Render ulang peta jika data berhasil ditangkap
                renderMap(currentHizb);
            } else {
                document.getElementById('text-total-xp').innerText = "0";
            }
        } catch (error) {
            console.error("Gagal mengambil data peta:", error);
            document.getElementById('text-total-xp').innerText = "Error";
        }
    }
</script>
</body>
</html>