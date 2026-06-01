document.addEventListener('DOMContentLoaded', () => {
    fetchMapData(1); // Default load Hizb 1
});

function changeHizb() {
    const hizb = document.getElementById('hizb-dropdown').value;
    fetchMapData(hizb);
}

async function fetchMapData(hizbNumber) {
    const xpDisplay = document.getElementById('total-xp-display');
    const container = document.getElementById('full-map-container');
    container.innerHTML = '<h3 style="text-align:center;">Memuat Peta...</h3>';

    try {
        const response = await fetch(`${BASE_URL}/HafidzQuizGame/getMapData?hizb=${hizbNumber}`);
        const data = await response.json();

        if (data.status === 'success') {
            xpDisplay.innerText = data.total_xp;
            document.getElementById('hizb-dropdown').value = data.hizb;
            renderFullMap(data.hizb, data.thumns, data.progress || []);
        }
    } catch (error) {
        container.innerHTML = `<p style="color:red; text-align:center;">Gagal memuat: ${error.message}</p>`;
    }
}

function renderFullMap(hizb, thumns, progressData) {
    const container = document.getElementById('full-map-container');
    container.innerHTML = ''; 

    let isNextNodeUnlocked = true; 

    // Urutan Render 8 Thumn
    thumns.forEach(thumn => {
        // 1. Render Header Thumn
        const header = document.createElement('div');
        header.className = 'thumn-header';
        header.innerHTML = `
            <div class="thumn-title">HIZB ${hizb} - THUMN ${thumn.thumn}</div>
            <div class="thumn-subtitle">${thumn.sura_name} (${thumn.sura_number}): ${thumn.start_ayah} - ${thumn.end_ayah}</div>
        `;
        container.appendChild(header);

        // 2. Buat Wadah Peta Node untuk Thumn ini
        const mapContainer = document.createElement('div');
        mapContainer.className = 'map-container';

        // SEKARANG: Render 10 Node dalam Thumn ini
        for (let n = 1; n <= 10; n++) {
            // Cari data progress 3 subnode (sesi) untuk node ke-n ini
            const subnodes = progressData.filter(p => p.thumn_number == thumn.thumn && p.node_number == n && p.is_completed == 1);
            const completedSubnodesCount = subnodes.length; 

            let statusClass = 'locked';
            let icon = '🔒';
            let badgeText = '';

            if (completedSubnodesCount >= 3) {
                statusClass = 'completed';
                icon = '⭐';
                isNextNodeUnlocked = true; 
            } else if (isNextNodeUnlocked) {
                statusClass = 'active';
                icon = '▶️';
                badgeText = `${completedSubnodesCount}/3`; 
                isNextNodeUnlocked = false; 
            }

            // --- DEKLARASI JENIS SOAL BERDASARKAN ROADMAP KAMU ---
            let quizTypes = [];
            switch(n) {
                case 1: quizTypes = ['sambung']; break;
                case 2: quizTypes = ['lengkapi']; break;
                case 3: quizTypes = ['sambung', 'lengkapi']; break;
                case 4: quizTypes = ['sesudah']; break;
                case 5: quizTypes = ['sambung', 'lengkapi', 'sesudah']; break;
                case 6: quizTypes = ['sesudah', 'sebelum']; break;
                case 7: quizTypes = ['ikrab']; break;
                case 8:
                case 9:
                case 10: quizTypes = ['sambung', 'lengkapi', 'sesudah', 'sebelum', 'ikrab']; break; // Gabungan semuanya
            }

            const nodeWrapper = document.createElement('div');
            nodeWrapper.className = 'node-wrapper';

            const nodeBtn = document.createElement('button');
            nodeBtn.className = `node-btn ${statusClass}`;
            nodeBtn.innerHTML = icon;
            
            if (badgeText !== '') {
                nodeBtn.innerHTML += `<div class="subnode-badge">${badgeText}</div>`;
            }

            if (statusClass !== 'locked') {
                const nextSubnodeTarget = completedSubnodesCount < 3 ? (completedSubnodesCount + 1) : 3;
                // Kirim array quizTypes ke dalam fungsi klik
                nodeBtn.onclick = () => startNode(hizb, thumn.thumn, n, nextSubnodeTarget, thumn, quizTypes);
            }

            nodeWrapper.appendChild(nodeBtn);
            mapContainer.appendChild(nodeWrapper);
        }

        container.appendChild(mapContainer);
    });
}

function startNode(hizb, thumn, node, subnode, thumnData, quizTypes) {
    // Menyimpan metadata posisi kuis
    localStorage.setItem('kuis_hizb', hizb);
    localStorage.setItem('kuis_thumn', thumn);
    localStorage.setItem('kuis_node', node);
    localStorage.setItem('kuis_subnode', subnode);
    
    // Menyimpan jangkauan ayat Al-Qur'an
    localStorage.setItem('kuis_sura_id', thumnData.sura_number);
    localStorage.setItem('kuis_ayah_start', thumnData.start_ayah);
    localStorage.setItem('kuis_ayah_end', thumnData.end_ayah);

    // MENYIMPAN ATURAN JENIS SOAL (Disimpan sebagai teks JSON string)
    localStorage.setItem('kuis_rules', JSON.stringify(quizTypes));
    
    // Pindah ke halaman bermain kuis
    window.location.href = `${BASE_URL}/hafidzquiz/play`;
}