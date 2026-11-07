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
        
        .options-container { display: flex; flex-direction: column; gap: 15px; padding: 20px; border-radius: 10px; transition: background-color 0.2s;}
        .option-btn { background: #fff; border: 2px solid #bdc3c7; border-radius: 12px; padding: 18px; font-size: 22px; cursor: pointer; transition: all 0.2s; font-family: 'LPMQ', serif; direction: rtl; text-align: center; width: 100%; max-width: 550px; box-sizing: border-box; }
        .option-btn:hover { border-color: #3498db; background: #f0f8ff; transform: translateY(-2px); }
        .option-btn.correct { background: #2ecc71; color: white; border-color: #27ae60; }
        .option-btn.wrong { background: #e74c3c; color: white; border-color: #c0392b; }

        .quiz-footer { padding: 20px 30px; border-top: 1px solid #ecf0f1; text-align: right; background: #fafbfc;}
        .btn-next { background: #3498db; color: white; border: none; padding: 14px 30px; font-size: 18px; font-weight: bold; border-radius: 10px; cursor: pointer; display: none; transition: 0.2s;}
        .btn-next:hover { background: #2980b9; }

        .result-screen { display: none; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; }
        .result-screen h2 { color: #2c3e50; font-size: 32px; margin-bottom: 10px;}
        .result-screen p { color: #7f8c8d; font-size: 18px;}

        /* Mode Pilihan Ganda (Sudah Diperbaiki Agar Rata Tengah Sempurna) */
        .options-container.mode-pilihan {
            display: flex;
            flex-direction: column;
            align-items: center;    /* Memastikan tombol pilihan rata tengah horizontal */
            justify-content: center;
            gap: 12px;
            width: 100%;
            margin-top: 20px;
        }

        /* Mode Word Bank */
        .options-container.mode-wordbank {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            width: 100%;
            margin-top: 25px;
            direction: rtl; 
            min-height: 80px;
        }
        .options-container.mode-wordbank.drag-over {
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e0;
        }

        /* Gaya Kotak Kosong / Slot Rumpang */
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

        .blank-slot.filled {
            border: 1px solid #cbd5e0;
            border-bottom: 3px solid #cbd5e0;
            background-color: #ffffff;
            padding: 0 10px;
        }

        .blank-slot.drag-over {
            background-color: #e2e8f0;
            border-bottom: 3px solid #3182ce;
        }

        .blank-slot.dragging-slot {
            opacity: 0.5;
            border-style: dashed;
        }

        /* Gaya Tombol Blok Kata */
        .word-chip {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-bottom: 4px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 18px;
            font-family: 'LPMQ', sans-serif;
            cursor: pointer;
            transition: all 0.1s ease;
            user-select: none;
        }

        .word-chip:active {
            border-bottom-width: 2px;
            transform: translateY(2px);
        }

        .word-chip.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }

        /* Gaya saat kata sudah dimasukkan ke garis (redup) */
        .word-chip.used {
            opacity: 0.4;
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
            pointer-events: none;
            box-shadow: none;
            transform: none;
            border-bottom-width: 2px;
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
    
    const config = {
        hizb: localStorage.getItem('kuis_hizb') || 1,
        thumn: localStorage.getItem('kuis_thumn') || 1,
        node: parseInt(localStorage.getItem('kuis_node') || 1),
        subnode: localStorage.getItem('kuis_subnode') || 1,
        bounds: JSON.parse(localStorage.getItem('kuis_bounds') || '[]')
    };

    let quranDataPool = [];
    let targetAyahs = [];
    let currentQuestions = [];
    let currentQuestionIndex = 0;
    const TOTAL_QUESTIONS = 10; 

    let correctAnswersCount = 0;
    let quizStartTime = Date.now();
    let timeElapsedSeconds = 0; 
    let earnedXP = 0;

    const WAQF_MARKS = ['ۗ', 'ۘ', 'ۙ', 'ۚ', 'ۛ', 'ۜ', '۝', '۞', '۩', 'ع', 'م', 'قل', 'صل', 'لا', 'ج', 'قلى', 'صلى', '۫', '۬', 'ۭ'];

    function isWaqf(word) {
        if(!word) return true;
        let clean = word.replace(/[\u064B-\u065F\u0670\u06D6-\u06ED\u08F0-\u08FF]/g, '').trim();
        if (clean.length < 2 && !['و', 'ف', 'ب', 'ك', 'ل', 'أ'].includes(clean)) return true;
        return clean === '' || WAQF_MARKS.includes(clean) || WAQF_MARKS.includes(word.trim());
    }

    // --- SHORTCUT KEYBOARD SHORTCUT (ANGKA 1-4 & ENTER) ---
    document.addEventListener('keydown', function(event) {
        // Jauhkan logika jika pengguna sedang berada di layar hasil kuis
        if (document.getElementById('result-screen').style.display === 'flex') return;

        // 1. Logika menekan angka 1-4 untuk memilih opsi jawaban (Hanya berlaku untuk tipe pilihan ganda)
        if (event.key >= '1' && event.key <= '4') {
            const currentQuestion = currentQuestions[currentQuestionIndex];
            // Pastikan soal saat ini bertipe pilihan ganda (bukan word bank/lengkapi_multi)
            if (currentQuestion && currentQuestion.type === 'pilihan_ganda') {
                const buttons = document.querySelectorAll('.option-btn');
                const targetIndex = parseInt(event.key) - 1;

                // Cek jika tombol pilihan tersedia dan belum dinonaktifkan (belum dijawab)
                if (buttons[targetIndex] && !buttons[targetIndex].disabled) {
                    buttons[targetIndex].click();
                }
            }
        }

        // 2. Logika menekan tombol "Enter" untuk memicu klik pada tombol CEK JAWABAN / LANJUTKAN
        if (event.key === 'Enter') {
            const btnNext = document.getElementById('btn-next');
            // Pastikan tombol aksi bawah sedang tampil/visible di layar sebelum diklik otomatis
            if (btnNext && btnNext.style.display !== 'none') {
                event.preventDefault(); // Mencegah form submitting tidak sengaja
                btnNext.click();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
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

    function getQuestionTypesForNode(nodeNumber) {
        if (nodeNumber >= 1 && nodeNumber <= 4) {
            return ['sambung_ayat', 'lengkapi_multi', 'ayat_sesudah'];
        } else if (nodeNumber >= 5 && nodeNumber <= 8) {
            return ['ayat_sebelum', 'tebak_nomor', 'tebak_sequence', 'ikrab'];
        } else { 
            return ['sambung_ayat', 'lengkapi_multi', 'ayat_sesudah', 'ayat_sebelum', 'tebak_nomor', 'tebak_sequence', 'ikrab'];
        }
    }

    function generateQuizSet() {
        currentQuestions = [];
        const pool = targetAyahs.length > 0 ? targetAyahs : quranDataPool;
        let attempts = 0; 
        const allowedTypes = getQuestionTypesForNode(config.node);

        while (currentQuestions.length < TOTAL_QUESTIONS && attempts < 300) {
            attempts++;
            const qType = allowedTypes[Math.floor(Math.random() * allowedTypes.length)];
            const targetAyah = pool[Math.floor(Math.random() * pool.length)];
            
            if (!targetAyah) continue;
            let question = null;

            if (qType === 'sambung_ayat' || qType === 'ayat_sesudah') {
                let nextAyah = quranDataPool.find(a => a.surah_id === targetAyah.surah_id && a.ayah === targetAyah.ayah + 1);
                if (nextAyah) {
                    let opts = generateDistractors(nextAyah.arabic, targetAyah);
                    opts.push(nextAyah.arabic);
                    opts = opts.sort(() => Math.random() - 0.5);
                    question = { type: 'pilihan_ganda', instructions: 'Apa ayat selanjutnya?', text: targetAyah.arabic, options: opts, correctIndex: opts.indexOf(nextAyah.arabic) };
                }
            } else if (qType === 'ayat_sebelum') {
                let prevAyah = quranDataPool.find(a => a.surah_id === targetAyah.surah_id && a.ayah === targetAyah.ayah - 1);
                if (prevAyah) {
                    let opts = generateDistractors(prevAyah.arabic, targetAyah);
                    opts.push(prevAyah.arabic);
                    opts = opts.sort(() => Math.random() - 0.5);
                    question = { type: 'pilihan_ganda', instructions: 'Ayat apa yang mendahului ayat ini?', text: targetAyah.arabic, options: opts, correctIndex: opts.indexOf(prevAyah.arabic) };
                }
            } else if (qType === 'lengkapi_multi') {
                question = generateLengkapiMulti(targetAyah.arabic);
            } else if (qType === 'tebak_nomor') {
                question = generateTebakNomor(targetAyah);
            } else if (qType === 'tebak_sequence') {
                question = generateTebakSequence(targetAyah);
            } else if (qType === 'ikrab') {
                question = generateIkrab(targetAyah.arabic);
            }

            if (!question) continue;

            const isDuplicate = currentQuestions.some(q => q.text === question.text);
            if (isDuplicate) continue;

            currentQuestions.push(question);
        }

        if (currentQuestions.length === 0) {
            showQuizError("Gagal meracik soal dari rentang ayat ini.");
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
            let backupPool = quranDataPool.filter(a => a.arabic !== correctAnswer && a.arabic !== targetAyah.arabic && !pool.includes(a));
            pool = pool.concat(backupPool);
        }
        pool = pool.sort(() => Math.random() - 0.5);
        return [pool[0]?.arabic || "ۗ", pool[1]?.arabic || "ۗ", pool[2]?.arabic || "ۗ"];
    }

    function generateWordDistractors(correctWord) {
        let words = [];
        targetAyahs.forEach(a => words = words.concat(a.arabic.split(' ')));
        if (words.length < 15) quranDataPool.slice(0, 50).forEach(a => words = words.concat(a.arabic.split(' ')));

        words = [...new Set(words)].filter(w => {
            if (w === correctWord) return false;
            return !isWaqf(w);
        });
        words = words.sort(() => Math.random() - 0.5);
        return [words[0] || "اللَّهُ", words[1] || "عَلِيمٌ", words[2] || "فِي"];
    }

    function generateLengkapiMulti(ayahText) {
        let words = ayahText.trim().split(/\s+/);
        if (words.length < 4) return null;
        
        let validIndices = [];
        for(let i = words.length - 1; i >= 0; i--) {
            if(!isWaqf(words[i])) validIndices.push(i);
            if(validIndices.length === 2) break; 
        }
        if(validIndices.length < 2) return null; 

        let hiddenIndices = validIndices.reverse(); 
        let answers = [];
        let displayWords = [...words];
        
        hiddenIndices.forEach((idx, order) => {
            answers.push(words[idx]);
            displayWords[idx] = `<span class="blank-slot" data-index="${order}">_____</span>`;
        });

        let distractors = generateWordDistractors(answers[0]);
        let options = [...answers, ...distractors].sort(() => Math.random() - 0.5);

        return { type: 'lengkapi_multi', instructions: 'Lengkapi ayat (Klik atau seret kata ke garis kosong):', text: displayWords.join(' '), options: options, correctAnswers: answers };
    }

    function generateTebakNomor(ayahObj) {
        let correctNum = ayahObj.ayah;
        let opts = [correctNum, correctNum + 1, Math.max(1, correctNum - 1), correctNum + 2];
        opts = [...new Set(opts)].sort(() => Math.random() - 0.5);
        return { type: 'pilihan_ganda', instructions: `Ayat berikut merupakan ayat ke berapa di dalam surah?`, text: ayahObj.arabic, options: opts.map(n => `Ayat ke-${n}`), correctIndex: opts.indexOf(`Ayat ke-${correctNum}`) };
    }

    function generateTebakSequence(targetAyah) {
        let next1 = quranDataPool.find(a => a.surah_id === targetAyah.surah_id && a.ayah === targetAyah.ayah + 1);
        let next2 = quranDataPool.find(a => a.surah_id === targetAyah.surah_id && a.ayah === targetAyah.ayah + 2);
        if (!next1 || !next2) return null;
        let correctSequence = next1.arabic + " ۝ " + next2.arabic;
        let randomAyah = quranDataPool[Math.floor(Math.random() * quranDataPool.length)];
        let distractor = randomAyah.arabic + " ۝ " + (quranDataPool.find(a => a.surah_id === randomAyah.surah_id && a.ayah === randomAyah.ayah + 1)?.arabic || "");
        let opts = [correctSequence, distractor].sort(() => Math.random() - 0.5);
        return { type: 'pilihan_ganda', instructions: 'Lanjutkan urutan (1-2) ayat setelah ayat ini:', text: targetAyah.arabic, options: opts, correctIndex: opts.indexOf(correctSequence) };
    }

    function generateIkrab(ayahText) {
        let words = ayahText.trim().split(/\s+/);
        let targetWord = words[words.length - 1];
        if (isWaqf(targetWord)) targetWord = words[words.length - 2];
        
        let baseWord = targetWord.replace(/[\u064B-\u0652]$/, ''); 
        let opts = [baseWord + '\u064E', baseWord + '\u0650', baseWord + '\u064F', targetWord];
        opts = [...new Set(opts)].sort(() => Math.random() - 0.5);
        let finalAyah = ayahText.replace(targetWord, " <u>" + baseWord + "___</u>");
        return { type: 'pilihan_ganda', instructions: "Pilih I'rab (harakat akhir) yang tepat:", text: finalAyah, options: opts, correctIndex: opts.indexOf(targetWord) };
    }
    
    function emptySlot(slot) {
        if (!slot.classList.contains('filled')) return;
        const chipId = slot.dataset.sourceId;
        const chip = document.getElementById(chipId);
        
        if (chip) chip.classList.remove('used'); 

        slot.innerText = '_____';
        slot.classList.remove('filled');
        delete slot.dataset.sourceId;
        slot.draggable = false;
        slot.ondragstart = null;
        slot.ondragend = null;
        slot.onclick = null;
    }

    function fillSlot(slot, chipId) {
        const chip = document.getElementById(chipId);
        if (!chip) return;

        const existingSlot = document.querySelector(`.blank-slot[data-source-id="${chipId}"]`);
        if (existingSlot && existingSlot !== slot) {
            emptySlot(existingSlot);
        }

        slot.innerText = chip.innerText;
        slot.classList.add('filled');
        slot.dataset.sourceId = chip.id;
        slot.draggable = true; 
        
        slot.ondragstart = (ev) => {
            ev.dataTransfer.setData('drag-type', 'slot_chip');
            ev.dataTransfer.setData('chip-id', chip.id);
            setTimeout(() => slot.classList.add('dragging-slot'), 0);
        };
        slot.ondragend = () => slot.classList.remove('dragging-slot');
        slot.onclick = function() { emptySlot(this); };
        
        chip.classList.add('used'); 
    }

    function selectWord(btnElement) {
        if (btnElement.classList.contains('used')) return; 

        const emptySlots = Array.from(document.querySelectorAll('.blank-slot')).filter(slot => !slot.classList.contains('filled'));
        if (emptySlots.length > 0) {
            fillSlot(emptySlots[0], btnElement.id);
        }
    }

    function displayQuestion() {
        const q = currentQuestions[currentQuestionIndex];
        const totalSoalAktif = currentQuestions.length;
        
        document.getElementById('progress-text').innerText = `${currentQuestionIndex + 1}/${totalSoalAktif}`;
        document.getElementById('progress-fill').style.width = `${((currentQuestionIndex) / totalSoalAktif) * 100}%`;
        document.getElementById('q-type-badge').innerText = `Hizb ${config.hizb} Thumn ${config.thumn} • Node ${config.node}`;
        document.getElementById('q-instructions').innerText = q.instructions;
        
        const qTextElem = document.getElementById('q-text');
        const optsContainer = document.getElementById('options-container');
        optsContainer.innerHTML = '';
        
        const btnNext = document.getElementById('btn-next');

        if (q.type === 'lengkapi_multi') {
            qTextElem.innerHTML = q.text; 
            optsContainer.className = 'options-container mode-wordbank';
            
            q.options.forEach((opt, index) => {
                const btn = document.createElement('div');
                btn.className = 'word-chip';
                btn.draggable = true; 
                btn.id = `word-chip-${index}`;
                btn.innerText = opt;
                btn.onclick = () => selectWord(btn);
                btn.addEventListener('dragstart', (e) => {
                    if(btn.classList.contains('used')) {
                        e.preventDefault();
                        return;
                    }
                    e.dataTransfer.setData('drag-type', 'bank_chip');
                    e.dataTransfer.setData('chip-id', btn.id);
                    setTimeout(() => btn.classList.add('dragging'), 0);
                });
                btn.addEventListener('dragend', () => btn.classList.remove('dragging'));
                optsContainer.appendChild(btn);
            });

            document.querySelectorAll('.blank-slot').forEach(slot => {
                slot.addEventListener('dragover', (e) => {
                    e.preventDefault(); 
                    if (!slot.classList.contains('filled')) slot.classList.add('drag-over');
                });
                slot.addEventListener('dragleave', () => slot.classList.remove('drag-over'));
                
                slot.addEventListener('drop', (e) => {
                    e.preventDefault();
                    slot.classList.remove('drag-over');

                    const chipId = e.dataTransfer.getData('chip-id');
                    if (!chipId) return; 

                    if (slot.classList.contains('filled')) {
                        emptySlot(slot);
                    }

                    fillSlot(slot, chipId);
                });
            });

            optsContainer.addEventListener('dragover', (e) => {
                e.preventDefault();
                optsContainer.classList.add('drag-over');
            });
            optsContainer.addEventListener('dragleave', () => optsContainer.classList.remove('drag-over'));
            optsContainer.addEventListener('drop', (e) => {
                e.preventDefault();
                optsContainer.classList.remove('drag-over');
                const chipId = e.dataTransfer.getData('chip-id');
                if (chipId) {
                    const sourceSlot = document.querySelector(`.blank-slot[data-source-id="${chipId}"]`);
                    if (sourceSlot) emptySlot(sourceSlot);
                }
            });

            btnNext.innerText = "CEK JAWABAN";
            btnNext.style.display = 'inline-block'; 
        } else {
            qTextElem.innerHTML = q.text;
            optsContainer.className = 'options-container mode-pilihan';
            
            q.options.forEach((opt, index) => {
                const btn = document.createElement('button');
                btn.className = 'option-btn';
                btn.innerText = opt;
                btn.onclick = () => checkAnswer(index, q.correctIndex, btn);
                optsContainer.appendChild(btn);
            });
            btnNext.style.display = 'none'; 
        }
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

        const btnNext = document.getElementById('btn-next');
        btnNext.innerText = "LANJUTKAN";
        btnNext.style.display = 'inline-block'; 
    }

    function checkLengkapiMultiAnswer() {
        const q = currentQuestions[currentQuestionIndex];
        const slots = document.querySelectorAll('.blank-slot');
        let isAllCorrect = true;

        slots.forEach((slot, idx) => {
            let userAns = slot.innerText.trim();
            let correctAns = q.correctAnswers[idx]?.trim();
            slot.onclick = null; 
            slot.draggable = false;

            if (userAns === correctAns) {
                slot.style.backgroundColor = '#2ecc71';
                slot.style.color = '#fff';
                slot.style.borderColor = '#27ae60';
            } else {
                slot.style.backgroundColor = '#e74c3c';
                slot.style.color = '#fff';
                slot.style.borderColor = '#c0392b';
                isAllCorrect = false;
            }
        });

        document.querySelectorAll('.word-chip').forEach(b => {
            b.draggable = false;
            b.onclick = null;
        });

        document.getElementById('options-container').ondragover = null;
        document.getElementById('options-container').ondrop = null;

        if (isAllCorrect) correctAnswersCount++;
    }

    function nextQuestion() {
        const q = currentQuestions[currentQuestionIndex];
        const btnNext = document.getElementById('btn-next');

        if (q.type === 'lengkapi_multi' && btnNext.innerText === "CEK JAWABAN") {
            checkLengkapiMultiAnswer();
            btnNext.innerText = "LANJUTKAN";
            return; 
        }

        currentQuestionIndex++; 

        if (currentQuestionIndex >= currentQuestions.length) {
            showFinishScreen(); 
        } else {
            displayQuestion(); 
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

            if (!response.ok) {
                const errText = await response.text();
                throw new Error(`Server bermasalah (Kode HTTP: ${response.status}).\nDetail: ${errText.slice(0, 100)}`);
            }
            
            const result = await response.json();
            if (result.status === 'success') {
                window.location.href = `${API_URL}/hafidzquiz/map`;
            } else {
                throw new Error(result.message || 'Gagal menyimpan kemajuan.');
            }
        } catch (error) {
            console.error("Error Simpan:", error);
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
            </div>`;
    }
</script>
</body>
</html>