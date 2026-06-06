<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bermain - HafidzQuiz</title>
    <style>
        @font-face {
            font-family: 'LPMQ';
            src: url('<?= base_url("fonts/LPMQ IsepMisbah.ttf") ?>') format('truetype');
            font-weight: normal; font-style: normal;
        }

        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .quiz-container { background: white; width: 100%; max-width: 700px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; min-height: 600px; }
        
        .quiz-header { background: #34495e; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .progress-bar-bg { flex-grow: 1; background: rgba(255,255,255,0.2); height: 12px; border-radius: 6px; margin: 0 20px; overflow: hidden; }
        .progress-bar-fill { background: #2ecc71; height: 100%; width: 0%; transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .close-btn { color: white; text-decoration: none; font-weight: bold; font-size: 22px; }

        .quiz-body { padding: 40px 30px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; text-align: center; }
        .question-type-badge { display: inline-block; background: #e0e6ed; color: #34495e; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: bold; text-transform: uppercase; margin-bottom: 25px; align-self: center;}
        
        .question-instructions { font-size: 18px; color: #7f8c8d; margin-bottom: 15px; font-weight: bold; }
        .question-text { 
            font-size: 26px; color: #2c3e50; line-height: 2.2; margin-bottom: 40px; 
            font-family: 'LPMQ', serif; direction: rtl; 
        }
        
        .options-container { display: flex; flex-direction: column; gap: 15px; }
        .option-btn { background: #fff; border: 2px solid #bdc3c7; border-radius: 12px; padding: 18px; font-size: 22px; cursor: pointer; transition: all 0.2s; font-family: 'LPMQ', serif; direction: rtl; }
        .option-btn:hover { border-color: #3498db; background: #f0f8ff; transform: translateY(-2px); }
        .option-btn.correct { background: #2ecc71; color: white; border-color: #27ae60; }
        .option-btn.wrong { background: #e74c3c; color: white; border-color: #c0392b; }

        .quiz-footer { padding: 20px 30px; border-top: 1px solid #ecf0f1; text-align: right; background: #fafbfc;}
        .btn-next { background: #3498db; color: white; border: none; padding: 14px 30px; font-size: 18px; font-weight: bold; border-radius: 10px; cursor: pointer; display: none; transition: 0.2s;}
        .btn-next:hover { background: #2980b9; }

        .result-screen { display: none; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; }
        .result-screen h2 { color: #2c3e50; font-size: 32px; margin-bottom: 10px;}
        .result-screen p { color: #7f8c8d; font-size: 18px;}
        /* Mode Default Pilihan Ganda (Menumpuk Vertikal) */
        .options-container.mode-pilihan {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            margin-top: 20px;
        }

        /* Mode Word Bank (Gaya Grid / Menyamping ala Duolingo) */
        .options-container.mode-wordbank {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            width: 100%;
            margin-top: 25px;
            direction: rtl; /* Supaya urutan pilihan kata Arab mengalir dari kanan */
        }

        /* Gaya Kotak Kosong / Slot Rumpang di dalam Ayat */
        .blank-slot {
            display: inline-block;
            min-width: 80px;
            height: 35px;
            margin: 0 5px;
            border-bottom: 2px dashed #a0aec0;
            background-color: #f7fafc;
            border-radius: 4px;
            text-align: center;
            vertical-align: middle;
            line-height: 35px;
            color: #2b6cb0;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }

        /* Kotak Slot saat sudah terisi kata */
        .blank-slot.filled {
            border: 1px solid #cbd5e0;
            border-bottom: 3px solid #cbd5e0;
            background-color: #ffffff;
            padding: 0 10px;
        }

        /* Gaya Tombol Blok Kata di Word Bank bawah */
        .word-chip {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-bottom: 4px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 18px;
            font-family: 'LPMQ Isep Misbah', sans-serif; /* Sesuaikan font Arabmu */
            cursor: pointer;
            transition: all 0.1s ease;
        }

        .word-chip:active {
            border-bottom-width: 2px;
            transform: translateY(2px);
        }

        /* Aturan Teks Arab agar otomatis rapi dari kanan ke kiri */
        .question-text.arabic-render {
            direction: rtl;
            text-align: center;
            font-size: 24px;
            line-height: 2.2;
            font-family: 'LPMQ Isep Misbah', sans-serif;
        }
    </style>
</head>
<body>

<div class="quiz-container">
    <div class="quiz-header" id="quiz-header">
        <a href="<?= base_url('hafidzquiz/map') ?>" class="close-btn">✖</a>
        <div class="progress-bar-bg"><div class="progress-bar-fill" id="progress-fill"></div></div>
        <span id="progress-text" style="font-weight:bold;">0/0</span>
    </div>

    <div class="quiz-body" id="quiz-body">
        <h3 id="loading-text" style="color: #3498db; text-align: center; margin-top: 50px;">Mengunduh Ayat-Ayat...</h3>
        
        <div id="quiz-content" style="display: none; flex-direction: column;">
            <div class="question-type-badge" id="q-type-badge">Tipe Soal</div>
            <div class="question-instructions" id="q-instructions">Instruksi Soal</div>
            
            <div class="question-text" id="q-text">Teks Pertanyaan</div>
            
            <div class="options-container" id="options-container"></div>
        </div>
    </div>

    <div class="quiz-footer" id="quiz-footer" style="display: none;">
        <button class="btn-next" id="btn-next" onclick="nextQuestion()">CEK JAWABAN</button>
    </div>

    <div class="result-screen" id="result-screen">
        <svg viewBox="0 0 24 24" width="80" height="80" fill="#f1c40f"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
        <h2>Sesi Selesai!</h2>
        <p>Alhamdulillah, Thumn berhasil diselesaikan.</p>
        
        <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; margin: 20px auto 10px auto; text-align: left; font-size: 15px; width: 100%; max-width: 320px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px solid #dee2e6; padding-bottom: 4px;">
                <span style="color: #495057; font-weight: bold;">Jawaban Benar:</span>
                <span id="stat-score" style="color: #28a745; font-weight: bold;">0/0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px solid #dee2e6; padding-bottom: 4px;">
                <span style="color: #495057; font-weight: bold;">Waktu Pengerjaan:</span>
                <span id="stat-time" style="color: #007bff; font-weight: bold;">00:00</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #495057; font-weight: bold;">XP Didapat:</span>
                <span id="stat-xp" style="color: #fd7e14; font-weight: bold;">+0 XP</span>
            </div>
        </div>

        <button class="btn-next" id="btn-submit" style="display:inline-block; margin-top: 20px; width:100%;" onclick="submitProgress()">Simpan & Kembali</button>
    </div>
</div>

<script>
    const ASSET_URL = '<?= rtrim(base_url(), '/') ?>'; 
    const API_URL = '<?= rtrim(site_url(), '/') ?>';
    
    // Menerima konfigurasi dari localStorage
    const config = {
        hizb: localStorage.getItem('kuis_hizb') || 1,
        thumn: localStorage.getItem('kuis_thumn') || 1,
        node: localStorage.getItem('kuis_node') || 1,
        subnode: localStorage.getItem('kuis_subnode') || 1,
        bounds: JSON.parse(localStorage.getItem('kuis_bounds') || '[]'),
        rules: JSON.parse(localStorage.getItem('kuis_rules') || '["sambung"]')
    };

    let quranDataPool = [];
    let targetAyahs = [];
    let currentQuestions = [];
    let currentQuestionIndex = 0;
    
    // 1. Berhasil diubah menjadi 10 soal sesuai request!
    const TOTAL_QUESTIONS = 10; 

    let correctAnswersCount = 0;
    let quizStartTime = Date.now();
    let timeElapsedSeconds = 0; 
    let earnedXP = 0;

    document.addEventListener('DOMContentLoaded', () => {
        // Pastikan tampilan kuis disembunyikan dulu sewaktu memuat agar tidak muncul layar hantu 0/0
        document.getElementById('quiz-content').style.display = 'none';
        document.getElementById('quiz-footer').style.display = 'none';

        if (!config.bounds || config.bounds.length === 0) {
            showQuizError('Akses tidak valid atau data batas ayat kosong. Silakan kembali ke peta.');
            return;
        }
        fetchQuranData();
    });

    async function fetchQuranData() {
        try {
            const fetchPromises = config.bounds.map(bound => {
                if(!bound.sura || isNaN(bound.sura)) {
                    throw new Error("Format data batas ayat (bounds) di database tidak valid.");
                }
                return fetch(`${ASSET_URL}/data/${bound.sura}.json`).then(res => {
                    if (!res.ok) throw new Error(`Gagal memuat file Surah ${bound.sura}.json`);
                    return res.json();
                });
            });

            const surahsData = await Promise.all(fetchPromises);

            surahsData.forEach((suraJson, index) => {
                const bound = config.bounds[index];
                quranDataPool = quranDataPool.concat(suraJson);
                const targets = suraJson.filter(a => a.ayah >= bound.start && a.ayah <= bound.end);
                targetAyahs = targetAyahs.concat(targets);
            });
            
            generateQuizSet();
        } catch (error) {
            showQuizError(error.message);
        }
    }

    function generateQuizSet() {
        currentQuestions = [];
        const pool = targetAyahs.length > 0 ? targetAyahs : quranDataPool;
        
        // Memperbesar batas pencarian (attempts) jadi 200 agar aman meracik 10 soal unik dari rentang ayat pendek
        let attempts = 0; 

        while (currentQuestions.length < TOTAL_QUESTIONS && attempts < 200) {
            attempts++;
            const qType = config.rules[Math.floor(Math.random() * config.rules.length)];
            const targetAyah = pool[Math.floor(Math.random() * pool.length)];
            
            if (!targetAyah) continue;

            let question = { type: qType, instructions: '', options: [], correctIndex: 0 };

            if (qType === 'sambung' || qType === 'sesudah') {
                question.instructions = 'Apa ayat selanjutnya?';
                question.text = targetAyah.arabic; 
                
                let nextAyah = quranDataPool.find(a => a.surah_id === targetAyah.surah_id && a.ayah === targetAyah.ayah + 1);
                if (!nextAyah && targetAyah.surah && targetAyah.ayah === targetAyah.surah.num_ayah) {
                    nextAyah = quranDataPool.find(a => a.surah_id === targetAyah.surah_id + 1 && a.ayah === 1);
                }

                if (nextAyah) {
                    question.correctAnswer = nextAyah.arabic;
                    question.options = generateDistractors(nextAyah.arabic, targetAyah);
                } else { continue; }
            } 
            else if (qType === 'sebelum') {
                question.instructions = 'Ayat apa yang mendahului ayat ini?';
                question.text = targetAyah.arabic; 
                
                let prevAyah = quranDataPool.find(a => a.surah_id === targetAyah.surah_id && a.ayah === targetAyah.ayah - 1);

                if (prevAyah) {
                    question.correctAnswer = prevAyah.arabic;
                    question.options = generateDistractors(prevAyah.arabic, targetAyah);
                } else { continue; }
            }
            else if (qType === 'lengkapi' || qType === 'ikrab') {
                question.instructions = 'Lengkapi bagian akhir ayat berikut:';
                
                let wordList = [];
                if (Array.isArray(targetAyah.arabic_words)) {
                    wordList = targetAyah.arabic_words;
                } else if (typeof targetAyah.arabic === 'string') {
                    wordList = targetAyah.arabic.trim().split(/\s+/); 
                }

                const words = [...wordList]; 
                let hiddenWord = '';
                let skippedMarks = [];

                while (words.length > 0) {
                    let temp = words.pop();
                    let cleanStr = temp.replace(/[\u064B-\u065F\u0670\u06D6-\u06ED\u08F0-\u08FF]/g, '').trim();
                    const waqfList = ['ع', 'م', 'قل', 'صل', 'لا', 'ج', 'قلى', 'صلى', 'ۗ', 'ۖ', 'ۙ', 'ۚ', 'ۛ', 'ۜ', '۩'];

                    if (cleanStr.length <= 1 || waqfList.includes(cleanStr)) {
                        skippedMarks.unshift(temp); 
                    } else {
                        hiddenWord = temp; 
                        break;
                    }
                }
                
                if (hiddenWord) {
                    let marksStr = skippedMarks.length > 0 ? ' ' + skippedMarks.join(' ') : '';
                    question.text = `${words.join(' ')} _____${marksStr}`;
                    question.correctAnswer = hiddenWord;
                    question.options = generateWordDistractors(hiddenWord);
                } else { 
                    continue; 
                }
            }

            // Hindari duplikasi soal yang sama persis dalam 1 sesi kuis
            const isDuplicate = currentQuestions.some(q => q.text === question.text);
            if (isDuplicate) continue;

            question.options.push(question.correctAnswer);
            question.options = question.options.sort(() => Math.random() - 0.5);
            question.correctIndex = question.options.indexOf(question.correctAnswer);
            currentQuestions.push(question);
        }

        // Antisipasi darurat: jika ayat sangat sedikit & tidak sampai 10, pakai saja soal yang ada daripada error 0/0
        if (currentQuestions.length === 0) {
            showQuizError("Gagal meracik soal dari rentang ayat ini. Pastikan data JSON surah lengkap.");
            return;
        }

        quizStartTime = Date.now();
        document.getElementById('loading-text').style.display = 'none';
        document.getElementById('quiz-content').style.display = 'flex';
        document.getElementById('quiz-footer').style.display = 'block';
        displayQuestion();
    }

    function generateDistractors(correctAnswer, targetAyah) {
        let pool = targetAyahs.filter(a => a.arabic !== correctAnswer && a.arabic !== targetAyah.arabic);
        if (pool.length < 3) {
            let sameSurahPool = quranDataPool.filter(a => 
                a.surah_id === targetAyah.surah_id && 
                a.arabic !== correctAnswer && 
                a.arabic !== targetAyah.arabic &&
                !pool.includes(a)
            );
            pool = pool.concat(sameSurahPool);
        }
        pool = pool.sort(() => Math.random() - 0.5);
        let distractors = [];
        for (let i = 0; i < 3; i++) {
            if (pool[i]) distractors.push(pool[i].arabic);
            else distractors.push("ۗ"); 
        }
        return distractors;
    }

    function generateWordDistractors(correctWord) {
        let wordPool = [];
        let primarySource = targetAyahs.length > 0 ? targetAyahs : quranDataPool;
        
        primarySource.forEach(a => {
            let wList = [];
            if (Array.isArray(a.arabic_words)) {
                wList = a.arabic_words;
            } else if (typeof a.arabic === 'string') {
                wList = a.arabic.trim().split(/\s+/);
            }
            wordPool = wordPool.concat(wList);
        });

        wordPool = [...new Set(wordPool.filter(w => {
            if (w === correctWord) return false;
            let cleanW = w.replace(/[\u064B-\u065F\u0670\u06D6-\u06ED\u08F0-\u08FF]/g, '').trim();
            const waqfList = ['ع', 'م', 'قل', 'صل', 'لا', 'ج', 'قلى', 'صلى'];
            return cleanW.length > 1 && !waqfList.includes(cleanW);
        }))];
        
        wordPool = wordPool.sort(() => Math.random() - 0.5);
        let distractors = wordPool.slice(0, 3);
        
        while (distractors.length < 3) {
            distractors.push("‌");
        }
        return distractors;
    }

    function displayQuestion() {
        const q = currentQuestions[currentQuestionIndex];
        const totalSoalAktif = currentQuestions.length;
        
        document.getElementById('progress-text').innerText = `${currentQuestionIndex + 1}/${totalSoalAktif}`;
        document.getElementById('progress-fill').style.width = `${((currentQuestionIndex) / totalSoalAktif) * 100}%`;
        
        document.getElementById('q-type-badge').innerText = `Hizb ${config.hizb} Thumn ${config.thumn}`;
        document.getElementById('q-instructions').innerText = q.instructions;
        document.getElementById('q-text').innerText = q.text;
        
        const optsContainer = document.getElementById('options-container');
        optsContainer.innerHTML = '';
        
        q.options.forEach((opt, index) => {
            const btn = document.createElement('button');
            btn.className = 'option-btn';
            btn.innerText = opt;
            btn.onclick = () => checkAnswer(index, q.correctIndex, btn);
            optsContainer.appendChild(btn);
        });

        document.getElementById('btn-next').style.display = 'none';
    }

    function checkAnswer(selectedIndex, correctIndex, btnElement) {
        const buttons = document.querySelectorAll('.option-btn');
        buttons.forEach(b => b.disabled = true);

        if (selectedIndex === correctIndex) {
            btnElement.classList.add('correct');
            correctAnswersCount++;
        } else {
            btnElement.classList.add('wrong');
            buttons[correctIndex].classList.add('correct');
        }

        document.getElementById('btn-next').style.display = 'inline-block';
    }

    // Buat satu variabel global di bagian atas script untuk penanda status tombol
    let isCheckingAnswer = true; 

    function nextQuestion() {
        const btnNext = document.getElementById('btn-next');

        // TAHAP 1: User menekan "CEK JAWABAN"
        if (isCheckingAnswer) {
            
            // Panggil fungsi validasi jawaban kuis Anda di sini (misal: checkUserAnswer())
            let isCorrect = checkUserAnswer(); 
            
            // Ubah teks tombol menjadi LANJUTKAN setelah jawaban divalidasi
            btnNext.innerText = "LANJUTKAN";
            isCheckingAnswer = false; // Set status ke tahap berikutnya
            return; // Kunci agar tidak langsung pindah soal dulu
        }

        // TAHAP 2: User menekan "LANJUTKAN" (Pindah ke soal berikutnya)
        currentQuestionIndex++; // Naikkan indeks soal
        isCheckingAnswer = true; // Reset kembali status ke mode pengecekan
        btnNext.innerText = "CEK JAWABAN"; // Kembalikan teks tombol awal

        if (currentQuestionIndex >= totalQuestions) {
            showResultScreen(); // Jika soal habis, tampilkan skor akhir
        } else {
            renderCurrentQuestion(); // Tampilkan soal baru berikutnya
        }
    }

    function showFinishScreen() {
        timeElapsedSeconds = Math.floor((Date.now() - quizStartTime) / 1000);
        let m = Math.floor(timeElapsedSeconds / 60).toString().padStart(2, '0');
        let s = (timeElapsedSeconds % 60).toString().padStart(2, '0');
        
        let totalSoal = currentQuestions.length;
        let scorePercentage = (correctAnswersCount / totalSoal) * 100;

        let timeMinutes = timeElapsedSeconds / 60;
        let bobotWaktu = 0.5;
        if (timeMinutes < 0.5) bobotWaktu = 3;
        else if (timeMinutes <= 1) bobotWaktu = 2;
        else if (timeMinutes <= 2) bobotWaktu = 1;

        let jumlahBenar = Math.round((scorePercentage / 100) * 10);
        earnedXP = Math.ceil(jumlahBenar * bobotWaktu);

        document.getElementById('stat-score').innerText = correctAnswersCount + '/' + totalSoal;
        document.getElementById('stat-time').innerText = m + ':' + s;
        document.getElementById('stat-xp').innerText = '+' + earnedXP + ' XP';

        document.getElementById('quiz-body').style.display = 'none';
        document.getElementById('quiz-footer').style.display = 'none';
        document.getElementById('result-screen').style.display = 'flex';
    }

    async function submitProgress() {
        const btn = document.getElementById('btn-submit');
        if (btn) {
            btn.innerText = 'Menyimpan...';
            btn.disabled = true;
        }

        let totalSoal = currentQuestions.length;
        let finalScore = (correctAnswersCount / totalSoal) * 100;

        try {
            const response = await fetch(`${API_URL}/HafidzQuizGame/submitResult`, { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    // JALUR PENYELAMAT: Menyuntikkan Token Keamanan CSRF bawaan CodeIgniter 4
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>' 
                },
                body: JSON.stringify({
                    hizb_number: parseInt(config.hizb),
                    thumn_number: parseInt(config.thumn),
                    node_number: parseInt(config.node),
                    subnode_number: parseInt(config.subnode),
                    score: finalScore,
                    time_taken_seconds: timeElapsedSeconds
                })
            });

            // Kupas pesan error asli dari server jika HTTP status gagal (bukan 200)
            if (!response.ok) {
                const errText = await response.text();
                throw new Error(`Server bermasalah (Kode HTTP: ${response.status}).\nDetail: ${errText.slice(0, 100)}`);
            }
            
            const result = await response.json();
            
            if (result.status === 'success') {
                // Sukses! Kembalikan user ke halaman map
                window.location.href = `${API_URL}/hafidzquiz/map`;
            } else {
                throw new Error(result.message || 'Gagal menyimpan kemajuan.');
            }
            
        } catch (error) {
            console.error("Error Simpan:", error);
            // Menampilkan error yang JUJUR dan asli, bukan tulisan "tidak diketahui" lagi
            alert("Gagal menyimpan kemajuan kuis!\n\n" + error.message);
            if (btn) {
                btn.innerText = 'Simpan & Kembali';
                btn.disabled = false;
            }
        }
    }

    function showQuizError(msg) {
        document.getElementById('loading-text').innerHTML = `
            <div style="color:#e74c3c; font-weight:bold; padding:20px; text-align:center;">
                <p>⚠️ Error: ${msg}</p>
                <button onclick="window.location.href='${API_URL}/hafidzquiz/map'" style="padding:8px 16px; background:#3498db; color:#fff; border:none; border-radius:5px; cursor:pointer; margin-top:10px;">Kembali ke Peta</button>
            </div>
        `;
        document.getElementById('loading-text').style.display = 'block';
    }

    // Fungsi untuk menentukan pool/daftar tipe soal yang tersedia untuk node tertentu
    function getQuestionTypesForNode(nodeNumber) {
        if (nodeNumber >= 1 && nodeNumber <= 4) {
            return ['sambung_ayat', 'lengkapi_multi', 'ayat_sesudah'];
        } else if (nodeNumber >= 5 && nodeNumber <= 8) {
            return ['ayat_sebelum', 'tebak_nomor', 'tebak_sequence', 'ikrab'];
        } else { // Node 9 & 10 (Boss Level)
            return [
                'sambung_ayat', 'lengkapi_multi', 'ayat_sesudah', 
                'ayat_sebelum', 'tebak_nomor', 'tebak_sequence', 'ikrab'
            ];
        }
    }

    function generateLengkapiMulti(ayahText) {
        let words = ayahText.split(' ');
        // Tentukan berapa kata yang mau dihilangkan (misal 2 atau 3)
        let blanksCount = Math.min(3, Math.floor(words.length / 3)); 
        let hiddenIndices = [];
        
        // Pilih index acak untuk dikosongkan
        while(hiddenIndices.length < blanksCount) {
            let rand = Math.floor(Math.random() * words.length);
            if(!hiddenIndices.includes(rand)) hiddenIndices.push(rand);
        }

        let answers = [];
        hiddenIndices.forEach(idx => {
            answers.push(words[idx]); // Simpan jawaban asli
            words[idx] = '____';      // Ganti kata di ayat dengan garis bawah
        });

        // Ambil kata acak dari ayat lain/sekitar sebagai pengecoh (distractor)
        let distractors = ['اللَّهُ', 'عَذَابٌ', 'بَنِي']; // Contoh statis, baiknya ambil dari database
        let options = [...answers, ...distractor].sort(() => Math.random() - 0.5);

        return {
            type: 'lengkapi_multi',
            question: words.join(' '), // Ayat yang sudah rumpang
            options: options,
            correctAnswers: answers // Urutan jawaban yang benar
        };
    }

    function generateTebakNomor(ayahObj) {
        let correctNum = ayahObj.numberInSurah;
        // Buat pilihan: [Benar, Benar+1, Benar-1, Benar+2]
        let options = [correctNum, correctNum + 1, Math.max(1, correctNum - 1), correctNum + 2];
        
        return {
            type: 'tebak_nomor',
            question: `Ayat berikut adalah ayat ke berapa?\n\n${ayahObj.text}`,
            options: [...new Set(options)].sort(() => Math.random() - 0.5), // Acak
            correctAnswer: correctNum
        };
    }

    function generateTebakSequence(currentIndex, ayahsData) {
        let questionAyah = ayahsData[currentIndex].text;
        
        // Jawaban Benar (Gabungan 2 ayat sesudahnya)
        let correctSequence = ayahsData[currentIndex+1].text + " ۝ " + ayahsData[currentIndex+2].text;
        
        // Buat pengecoh (Gabungan ayat acak lain)
        let distractor1 = ayahsData[currentIndex+3].text + " ۝ " + ayahsData[currentIndex+4].text;
        
        return {
            type: 'tebak_sequence',
            question: `Lanjutkan urutan 2 ayat setelah ayat ini:\n\n${questionAyah}`,
            options: [correctSequence, distractor1].sort(() => Math.random() - 0.5),
            correctAnswer: correctSequence
        };
    }

    function generateIkrab(ayahText) {
        let words = ayahText.split(' ');
        let targetWord = words[words.length - 1]; // Ambil kata terakhir
        
        // Regex huruf Arab + buang harakat akhir (Fathatan, Dammatan, Kasratan, Fatha, Damma, Kasra)
        let baseWord = targetWord.replace(/[\u064B-\u0650]$/, ''); 
        
        // Buat pilihan berharakat beda (Fathah, Kasrah, Dammah)
        let options = [
            baseWord + '\u064E', // Fathah (ـَ)
            baseWord + '\u0650', // Kasrah (ـِ)
            baseWord + '\u064F', // Dammah (ـُ)
            baseWord + '\u064C'  // Dammatan (ـٌ) - Asumsi jawaban benar
        ];
        
        return {
            type: 'ikrab',
            question: `Pilih harakat akhir (I'rab) yang tepat untuk kata yang digarisbawahi:\n\n${words.slice(0, -1).join(' ')} _____`,
            options: options,
            correctAnswer: targetWord // Kata asli dari database
        };
    }

    // Logika JavaScript untuk interaksi isi kolom rumpang
    function selectWord(btnElement) {
        // 1. Cari slot titik-titik ('_____') pertama di dalam area pertanyaan yang belum terisi
        const emptySlots = Array.from(document.querySelectorAll('.blank-slot')).filter(slot => slot.innerText === '_____');
        
        if (emptySlots.length > 0) {
            let targetSlot = emptySlots[0];
            
            // 2. Pindahkan teks Arab dari tombol pilihan ke dalam slot kosong tersebut
            targetSlot.innerText = btnElement.innerText;
            targetSlot.classList.add('filled'); // Beri style kotak putih menandakan sudah terisi
            
            // Simpan ID/referensi tombol asal di data-attribute slot untuk fitur pembatalan
            targetSlot.dataset.sourceId = btnElement.id; 
            
            // 3. Pasang fitur pembatalan: Jika slot yang sudah terisi diklik lagi oleh user, kata dikembalikan ke bawah
            targetSlot.onclick = function() {
                this.innerText = '_____';
                this.classList.remove('filled');
                document.getElementById(this.dataset.sourceId).style.visibility = 'visible'; // Munculkan tombol bawah lagi
                this.onclick = null; // Hapus listener pembatalan
            };
            
            // 4. Sembunyikan kata di word-bank bawah agar tidak dipilih dua kali (gunakan visibility agar layout tidak bergeser)
            btnElement.style.visibility = 'hidden'; 
        }
    }
</script>
</body>
</html>