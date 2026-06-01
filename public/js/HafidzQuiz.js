// ==========================================
// DATA & UTILITIES
// ==========================================
const surahList = [
    "Al-Fatihah", "Al-Baqarah", "Ali 'Imran", "An-Nisa'", "Al-Ma'idah", "Al-An'am", "Al-A'raf", "Al-Anfal", "At-Taubah", "Yunus", "Hud", "Yusuf", "Ar-Ra'd", "Ibrahim", "Al-Hijr", "An-Nahl", "Al-Isra'", "Al-Kahf", "Maryam", "Taha", "Al-Anbiya'", "Al-Hajj", "Al-Mu'minun", "An-Nur", "Al-Furqan", "Asy-Syu'ara'", "An-Naml", "Al-Qasas", "Al-'Ankabut", "Ar-Rum", "Luqman", "As-Sajdah", "Al-Ahzab", "Saba'", "Fatir", "Yasin", "As-Saffat", "Sad", "Az-Zumar", "Ghafir", "Fussilat", "Asy-Syura", "Az-Zukhruf", "Ad-Dukhan", "Al-Jasiyah", "Al-Ahqaf", "Muhammad", "Al-Fath", "Al-Hujurat", "Qaf", "Az-Zariyat", "At-Tur", "An-Najm", "Al-Qamar", "Ar-Rahman", "Al-Waqi'ah", "Al-Hadid", "Al-Mujadalah", "Al-Hasyr", "Al-Mumtahanah", "As-Saff", "Al-Jumu'ah", "Al-Munafiqun", "At-Tagabun", "At-Talaq", "At-Tahrim", "Al-Mulk", "Al-Qalam", "Al-Haqqah", "Al-Ma'arij", "Nuh", "Al-Jinn", "Al-Muzzammil", "Al-Muddassir", "Al-Qiyamah", "Al-Insan", "Al-Mursalat", "An-Naba'", "An-Nazi'at", "'Abasa", "At-Takwir", "Al-Infitar", "Al-Mutaffifin", "Al-Insyiqaq", "Al-Buruj", "At-Tariq", "Al-A'la", "Al-Gasyiyah", "Al-Fajr", "Al-Balad", "Asy-Syams", "Al-Lail", "Ad-Duha", "Asy-Syarh", "At-Tin", "Al-'Alaq", "Al-Qadr", "Al-Bayyinah", "Az-Zalzalah", "Al-'Adiyat", "Al-Qari'ah", "At-Takasur", "Al-'Asr", "Al-Humazah", "Al-Fil", "Quraisy", "Al-Ma'un", "Al-Kausar", "Al-Kafirun", "An-Nasr", "Al-Lahab", "Al-Ikhlas", "Al-Falaq", "An-Nas"
];

function toArabicNumber(number) {
    const arabicDigits = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
    return String(number).replace(/[0-9]/g, d => arabicDigits[d]);
}

function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

// Fungsi pemotong ayat berdasarkan tanda waqaf atau nilai tengah kalimat
function splitAyahForQuestion(ayahData) {
    const fullText = ayahData.arabic;
    const words = fullText.split(' ').filter(w => w.trim() !== '');
    const pattern = /([ۖۗۘۙۚۛۜۮࣖ])/u;

    const match = fullText.match(pattern);
    if (match) {
        const idx = fullText.indexOf(match[0]);
        const soal = fullText.substring(0, idx + match[0].length).trim();
        const jawaban = fullText.substring(idx + match[0].length).trim();
        if (jawaban !== "") {
            return { soal, jawaban };
        }
    }

    if (words.length === 0) return { soal: fullText, jawaban: '' };
    const middle = Math.floor(words.length / 2);
    return {
        soal: words.slice(0, middle).join(' '),
        jawaban: words.slice(middle).join(' ')
    };
}

// ==========================================
// STATE ENGINE KUIS
// ==========================================
let quizQuestions = [];
let currentQuestionIndex = 0;
let correctAnswersCount = 0;
let startTime, questionStartTime;
const alm = "\u061C";

// DOM Elements
const surahSelect = document.getElementById('surah-select');
const ayahStartSelect = document.getElementById('ayah-start');
const ayahEndSelect = document.getElementById('ayah-end');
const jumlahInput = document.getElementById('jumlah-soal');
const setupView = document.getElementById('setup-view');
const quizView = document.getElementById('quiz-view');
const resultView = document.getElementById('result-view');

// ==========================================
// INITIALIZATION & DROPDOWNS
// ==========================================
if (surahSelect) {
    surahSelect.innerHTML = '<option value="" disabled selected>Pilih Surah</option>';
    surahList.forEach((surah, index) => {
        let option = document.createElement('option');
        option.value = index + 1;
        option.text = `${index + 1}. ${surah}`;
        surahSelect.appendChild(option);
    });
}

async function loadAyahDropdowns(surahId) {
    if (!ayahStartSelect || !ayahEndSelect) return [];
    ayahStartSelect.innerHTML = '<option value="">Memuat...</option>';
    ayahEndSelect.innerHTML = '<option value="">Memuat...</option>';
    try {
        const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
        const response = await fetch(`${baseUrl}data/${surahId}.json`);
        if (!response.ok) throw new Error();
        const data = await response.json();
        
        ayahStartSelect.innerHTML = '';
        ayahEndSelect.innerHTML = '';
        data.forEach((_, i) => {
            const num = i + 1;
            ayahStartSelect.innerHTML += `<option value="${num}">${num}</option>`;
            ayahEndSelect.innerHTML += `<option value="${num}">${num}</option>`;
        });
        ayahEndSelect.value = data.length;
        return data;
    } catch (e) {
        console.error(`Gagal memuat data Surah ${surahId}.`);
        return [];
    }
}

if (surahSelect) {
    surahSelect.addEventListener('change', function() { loadAyahDropdowns(this.value); });
}
if (ayahStartSelect && ayahEndSelect) {
    ayahStartSelect.addEventListener('change', function() {
        if (parseInt(this.value) > parseInt(ayahEndSelect.value)) ayahEndSelect.value = this.value;
    });
    ayahEndSelect.addEventListener('change', function() {
        if (parseInt(this.value) < parseInt(ayahStartSelect.value)) ayahStartSelect.value = this.value;
    });
}

// Restore Form Data dari LocalStorage
async function restoreFormData() {
    if (!surahSelect || !jumlahInput) return;
    const savedSurah = localStorage.getItem('tahfiz_surah_id');
    const savedJumlah = localStorage.getItem('tahfiz_jumlah');
    const savedTypesStr = localStorage.getItem('tahfiz_types');

    if (savedJumlah) jumlahInput.value = savedJumlah;
    if (savedTypesStr) {
        const savedTypes = JSON.parse(savedTypesStr);
        document.querySelectorAll('input[name="type[]"]').forEach(cb => {
            cb.checked = savedTypes.includes(cb.value);
        });
    }
    if (savedSurah) {
        surahSelect.value = savedSurah;
        await loadAyahDropdowns(savedSurah);
        if (localStorage.getItem('tahfiz_ayah_start')) ayahStartSelect.value = localStorage.getItem('tahfiz_ayah_start');
        if (localStorage.getItem('tahfiz_ayah_end')) ayahEndSelect.value = localStorage.getItem('tahfiz_ayah_end');
    }
}
restoreFormData();

// ==========================================
// CORE LOGIC: CLIENT SIDE CORE GENERATOR
// ==========================================
const configForm = document.getElementById('quiz-config-form');
if (configForm) {
    configForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const surahId = parseInt(surahSelect.value);
        const ayahStart = parseInt(ayahStartSelect.value);
        const ayahEnd = parseInt(ayahEndSelect.value);
        const jumlahDiminta = parseInt(jumlahInput.value);
        const selectedTypes = Array.from(document.querySelectorAll('input[name="type[]"]:checked')).map(cb => cb.value);

        if (selectedTypes.length === 0) {
            alert("Pilih minimal satu jenis soal!");
            return;
        }

        // Simpan preferensi form kuis
        localStorage.setItem('tahfiz_surah_id', surahId);
        localStorage.setItem('tahfiz_ayah_start', ayahStart);
        localStorage.setItem('tahfiz_ayah_end', ayahEnd);
        localStorage.setItem('tahfiz_jumlah', jumlahDiminta);
        localStorage.setItem('tahfiz_types', JSON.stringify(selectedTypes));

        // Ambil data master ayat
        const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
        const response = await fetch(`${baseUrl}data/${surahId}.json`);
        const semuaAyat = await response.json();

        // Filter Rentang Ayat Pilihan
        let dataRentang = semuaAyat.filter(a => a.ayah >= ayahStart && a.ayah <= ayahEnd);
        if (dataRentang.length === 0) {
            alert("Tidak ada ayat dalam rentang terpilih.");
            return;
        }

        let jumlahSoal = Math.min(jumlahDiminta, dataRentang.length);
        quizQuestions = [];
        let ayatUntukSoal = [...dataRentang];

        for (let i = 0; i < jumlahSoal; i++) {
            const randomIndex = Math.floor(Math.random() * ayatUntukSoal.length);
            const ayatTerpilih = ayatUntukSoal.splice(randomIndex, 1)[0];

            let tipeSoal = selectedTypes[Math.floor(Math.random() * selectedTypes.length)];
            const ayatLengkapDenganNomor = `${ayatTerpilih.arabic} ${toArabicNumber(ayatTerpilih.ayah)}${alm}`;
            let words = ayatTerpilih.arabic.split(' ').filter(w => w.trim() !== '');

            // Filter indeks kata yang memiliki huruf dasar arab (menghindari tanda baca kosong)
            let validIndices = [];
            words.forEach((w, idx) => {
                const baseLetters = (w.match(/[\u0621-\u064A\u0671]/gu) || []).length;
                if (baseLetters >= 2) validIndices.push(idx);
            });

            // ------------------------------------------
            // LOGIKA 1: LENGKAPI AYAT (1 KATA RUMPANG)
            // ------------------------------------------
            if (tipeSoal === 'lengkapi' && validIndices.length >= 1) {
                let randomValidIdx = validIndices[Math.floor(Math.random() * validIndices.length)];
                let jawabanBenar = words[randomValidIdx];

                let teksSoalWords = [...words];
                teksSoalWords[randomValidIdx] = '<span class="word-blank">(......)</span>';

                // Cari Pengecoh Kata
                let distractors = [];
                let limit = 0;
                while (distractors.length < 3 && limit < 100) {
                    limit++;
                    let randAyat = semuaAyat[Math.floor(Math.random() * semuaAyat.length)];
                    let randWordsArray = randAyat.arabic.split(' ').filter(w => w.trim() !== '');
                    if (randWordsArray.length > 0) {
                        let randWord = randWordsArray[Math.floor(Math.random() * randWordsArray.length)];
                        const baseLettersDist = (randWord.match(/[\u0621-\u064A\u0671]/gu) || []).length;
                        if (baseLettersDist >= 2 && randWord !== jawabanBenar && !distractors.includes(randWord)) {
                            distractors.push(randWord);
                        }
                    }
                }

                let pilihan = [jawabanBenar, ...distractors];
                while (pilihan.length < 4) { pilihan.push("—"); }
                shuffleArray(pilihan);

                quizQuestions.push({
                    tipe: 'lengkapi_ayat',
                    teks_soal: `<div class='ayat-box' dir='rtl'>${teksSoalWords.join(' ')}</div>`,
                    jawaban_benar: jawabanBenar,
                    pilihan: pilihan,
                    ayat_lengkap: ayatLengkapDenganNomor
                });
                continue;
            }

            // ------------------------------------------
            // LOGIKA 2: MENEBAK AYAT SEBELUMNYA
            // ------------------------------------------
            if (tipeSoal === 'sebelum') {
                let ayatIndex = semuaAyat.findIndex(a => a.ayah === ayatTerpilih.ayah);
                if (ayatIndex > 0) {
                    let correctAyah = semuaAyat[ayatIndex - 1];
                    let teksSoal = `<div style='text-align:center; margin-bottom:10px; color:#555;'>Perhatikan ayat berikut:</div>
                                    <div class='ayat-box' dir='rtl'>${ayatTerpilih.arabic}</div>
                                    <div style='text-align:center; margin-top:15px; font-weight:bold; color:var(--primary);'>Manakah ayat sebelumnya?</div>`;
                    
                    let jawabanBenar = correctAyah.arabic;
                    let distractors = [];
                    let limit = 0;
                    while (distractors.length < 3 && limit < 100) {
                        limit++;
                        let rand = semuaAyat[Math.floor(Math.random() * semuaAyat.length)];
                        if (rand.ayah !== correctAyah.ayah && rand.ayah !== ayatTerpilih.ayah && !distractors.includes(rand.arabic)) {
                            distractors.push(rand.arabic);
                        }
                    }
                    let pilihan = [jawabanBenar, ...distractors];
                    while (pilihan.length < 4) {
                        let rand = semuaAyat[Math.floor(Math.random() * semuaAyat.length)];
                        if (!pilihan.includes(rand.arabic)) pilihan.push(rand.arabic);
                    }
                    shuffleArray(pilihan);

                    quizQuestions.push({
                        tipe: 'tebak_ayat',
                        teks_soal: teksSoal,
                        jawaban_benar: jawabanBenar,
                        pilihan: pilihan,
                        ayat_lengkap: `${jawabanBenar} ${toArabicNumber(correctAyah.ayah)}`
                    });
                    continue;
                } else {
                    tipeSoal = 'sambung'; // Fallback ke sambung ayat jika ini ayat pertama surah
                }
            }

            // ------------------------------------------
            // LOGIKA 3: MENEBAK AYAT SESUDAHNYA
            // ------------------------------------------
            if (tipeSoal === 'sesudah') {
                let ayatIndex = semuaAyat.findIndex(a => a.ayah === ayatTerpilih.ayah);
                if (ayatIndex < semuaAyat.length - 1) {
                    let correctAyah = semuaAyat[ayatIndex + 1];
                    let teksSoal = `<div style='text-align:center; margin-bottom:10px; color:#555;'>Perhatikan ayat berikut:</div>
                                    <div class='ayat-box' dir='rtl'>${ayatTerpilih.arabic}</div>
                                    <div style='text-align:center; margin-top:15px; font-weight:bold; color:var(--primary);'>Manakah ayat sesudahnya?</div>`;
                    
                    let jawabanBenar = correctAyah.arabic;
                    let distractors = [];
                    let limit = 0;
                    while (distractors.length < 3 && limit < 100) {
                        limit++;
                        let rand = semuaAyat[Math.floor(Math.random() * semuaAyat.length)];
                        if (rand.ayah !== correctAyah.ayah && rand.ayah !== ayatTerpilih.ayah && !distractors.includes(rand.arabic)) {
                            distractors.push(rand.arabic);
                        }
                    }
                    let pilihan = [jawabanBenar, ...distractors];
                    while (pilihan.length < 4) {
                        let rand = semuaAyat[Math.floor(Math.random() * semuaAyat.length)];
                        if (!pilihan.includes(rand.arabic)) pilihan.push(rand.arabic);
                    }
                    shuffleArray(pilihan);

                    quizQuestions.push({
                        tipe: 'tebak_ayat',
                        teks_soal: teksSoal,
                        jawaban_benar: jawabanBenar,
                        pilihan: pilihan,
                        ayat_lengkap: `${jawabanBenar} ${toArabicNumber(correctAyah.ayah)}`
                    });
                    continue;
                } else {
                    tipeSoal = 'sambung'; // Fallback jika ini ayat terakhir surah
                }
            }

            // ------------------------------------------
            // LOGIKA 4: TEBAK NOMOR AYAT / LAFAZ
            // ------------------------------------------
            if (tipeSoal === 'nomor') {
                let subTipe = Math.floor(Math.random() * 2);
                let teksSoal = '', jawabanBenar = '', pilihan = [];
                let tempRentang = [...dataRentang];
                shuffleArray(tempRentang);

                if (subTipe === 0) {
                    teksSoal = `<div style='text-align:center; margin-bottom:10px; color:#555;'>Perhatikan ayat berikut:</div>
                                <div class='ayat-box' dir='rtl'>${ayatTerpilih.arabic}</div>
                                <div style='text-align:center; margin-top:15px; font-weight:bold; color:var(--primary);'>Ayat nomor berapakah ini?</div>`;
                    jawabanBenar = String(ayatTerpilih.ayah);
                    pilihan.push(jawabanBenar);

                    for (let p of tempRentang) {
                        if (pilihan.length >= 4) break;
                        let num = String(p.ayah);
                        if (!pilihan.includes(num)) pilihan.push(num);
                    }
                    let extraNum = 1;
                    while (pilihan.length < 4) {
                        let numStr = String(extraNum++);
                        if (!pilihan.includes(numStr)) pilihan.push(numStr);
                    }
                } else {
                    teksSoal = `<div style='text-align:center; margin-bottom:10px; color:#555;'>Surah ${surahList[surahId - 1]}</div>
                                <div style='text-align:center; margin-top:10px; font-weight:bold; color:var(--primary); font-size:20px;'>Manakah bunyi ayat ke-${ayatTerpilih.ayah}?</div>`;
                    jawabanBenar = ayatTerpilih.arabic;
                    pilihan.push(jawabanBenar);

                    for (let p of tempRentang) {
                        if (pilihan.length >= 4) break;
                        if (!pilihan.includes(p.arabic)) pilihan.push(p.arabic);
                    }
                    while (pilihan.length < 4) {
                        let rand = semuaAyat[Math.floor(Math.random() * semuaAyat.length)];
                        if (!pilihan.includes(rand.arabic)) pilihan.push(rand.arabic);
                    }
                }
                shuffleArray(pilihan);
                quizQuestions.push({
                    tipe: 'tebak_ayat',
                    teks_soal: teksSoal,
                    jawaban_benar: jawabanBenar,
                    pilihan: pilihan,
                    ayat_lengkap: ayatLengkapDenganNomor
                });
                continue;
            }

            // ------------------------------------------
            // LOGIKA 5: IKRAB (TEBAK HARAKAT AKHIR)
            // ------------------------------------------
            if (tipeSoal === 'ikrab') {
                let fullText = ayatTerpilih.arabic;
                let wordsIkrab = fullText.split(' ').filter(w => w.trim() !== '');
                let waqafPattern = /[ۖۗۘۙۚۛۜۮࣖ]/u;
                let posisiValid = [];

                wordsIkrab.forEach((w, index) => {
                    if (/([ًٌٍَُِّْ])[ۖۗۘۙۚۛۜۮࣖ]*$/u.test(w)) {
                        posisiValid.push(index);
                    }
                });

                let selectedIndex = wordsIkrab.length - 1;
                if (posisiValid.length > 0) {
                    let posisiAkhir = posisiValid.filter(idx => waqafPattern.test(wordsIkrab[idx]) || idx === wordsIkrab.length - 1);
                    let posisiTengah = posisiValid.filter(idx => !waqafPattern.test(wordsIkrab[idx]) && idx !== wordsIkrab.length - 1);

                    let roll = Math.floor(Math.random() * 3) + 1;
                    if (roll === 1 && posisiTengah.length > 0) {
                        selectedIndex = posisiTengah[Math.floor(Math.random() * posisiTengah.length)];
                    } else {
                        selectedIndex = posisiAkhir.length > 0 ? posisiAkhir[Math.floor(Math.random() * posisiAkhir.length)] : posisiValid[Math.floor(Math.random() * posisiValid.length)];
                    }
                }

                let wordToProcess = wordsIkrab[selectedIndex];
                let jawabanBenar = 'َ';
                let wordDisplay = wordToProcess;

                let matches = wordToProcess.match(/([ًٌٍَُِّْ])([ۖۗۘۙۚۛۜۮࣖ]*)$/u);
                if (matches) {
                    jawabanBenar = matches[1];
                    let waqafAfter = matches[2];
                    wordDisplay = wordToProcess.replace(/[ًٌٍَُِّْ]([ۖۗۘۙۚۛۜۮࣖ]*)$/u, '◌' + waqafAfter);
                }

                let displayWords = [...wordsIkrab];
                displayWords[selectedIndex] = `<span style='color:#3d46e0; font-weight:bold;'>${wordDisplay}</span>`;
                let teksSoalIkrab = displayWords.join(' ');

                let pilihan = [jawabanBenar];
                let tempHarakat = ['َ', 'ُ', 'ِ', 'ً', 'ٌ', 'ٍ', 'ْ', 'ّ'];
                shuffleArray(tempHarakat);
                for (let h of tempHarakat) {
                    if (pilihan.length >= 4) break;
                    if (h !== jawabanBenar) pilihan.push(h);
                }
                shuffleArray(pilihan);

                quizQuestions.push({
                    tipe: 'ikrab',
                    teks_soal: `<div style='text-align:center; font-size:14px; color:#666; margin-bottom:15px;'>Pilih harakat akhir yang tepat untuk kata yang berwarna biru:</div>
                                <div class='ayat-box' dir='rtl' style='font-size:28px; line-height:2.5;'>${teksSoalIkrab}</div>`,
                    jawaban_benar: jawabanBenar,
                    pilihan: pilihan,
                    ayat_lengkap: ayatLengkapDenganNomor
                });
                continue;
            }

            // ------------------------------------------
            // LOGIKA DEFAULT: SAMBUNG AYAT
            // ------------------------------------------
            let potongan = splitAyahForQuestion(ayatTerpilih);
            let jawabanBenarSambung = potongan.jawaban;
            let pilihanSambung = [jawabanBenarSambung];
            let teksSoalFix = potongan.soal + alm;

            let targetLength = jawabanBenarSambung.length;
            let minLen = targetLength * 0.4;
            let maxLen = targetLength * 1.8;

            let limitLoop = 0;
            let tempRentangSambung = [...dataRentang];
            shuffleArray(tempRentangSambung);

            for (let ayatSalah of tempRentangSambung) {
                if (pilihanSambung.length >= 4 || limitLoop > 150) break;
                limitLoop++;
                if (ayatSalah.ayah !== ayatTerpilih.ayah) {
                    let potSalah = splitAyahForQuestion(ayatSalah);
                    let jawSalah = potSalah.jawaban;
                    if (!pilihanSambung.includes(jawSalah) && jawSalah.trim() !== "" && jawSalah.length >= minLen && jawSalah.length <= maxLen) {
                        pilihanSambung.push(jawSalah);
                    }
                }
            }

            if (pilihanSambung.length < 4) {
                for (let ayatSalah of semuaAyat) {
                    if (pilihanSambung.length >= 4) break;
                    if (ayatSalah.ayah === ayatTerpilih.ayah) continue;
                    let potSalah = splitAyahForQuestion(ayatSalah);
                    if (!pilihanSambung.includes(potSalah.jawaban) && potSalah.jawaban.trim() !== "") {
                        pilihanSambung.push(potSalah.jawaban);
                    }
                }
            }

            while (pilihanSambung.length < 4) { pilihanSambung.push("—"); }
            shuffleArray(pilihanSambung);

            quizQuestions.push({
                tipe: 'sambung_ayat',
                teks_soal: `<div class='ayat-box' dir='rtl'>${teksSoalFix}</div>`,
                pilihan: pilihanSambung,
                jawaban_benar: jawabanBenarSambung,
                ayat_lengkap: ayatLengkapDenganNomor
            });
        }

        // Tampilkan Arena Permainan
        if (setupView && quizView) {
            setupView.classList.add('hidden');
            quizView.classList.remove('hidden');
        }
        
        currentQuestionIndex = 0;
        correctAnswersCount = 0;
        startTime = new Date();
        renderQuestion();
    });
}

// ==========================================
// GAMEPLAY ENGINE
// ==========================================
function renderQuestion() {
    const questionArea = document.getElementById('question-area');
    const optionArea = document.getElementById('option-area');
    const feedback = document.getElementById('feedback');
    const nextBtn = document.getElementById('next-btn');

    if (!questionArea || !optionArea) return;

    if (feedback) feedback.classList.add('hidden');
    if (feedback) feedback.innerHTML = '';
    if (nextBtn) nextBtn.style.display = 'none';
    optionArea.innerHTML = '';

    document.getElementById('current-index').innerText = currentQuestionIndex + 1;
    document.getElementById('total-count').innerText = quizQuestions.length;

    let q = quizQuestions[currentQuestionIndex];
    questionArea.innerHTML = q.teks_soal;

    questionStartTime = new Date();

    // Merender Tombol Pilihan Jawaban
    q.pilihan.forEach(opt => {
        let btn = document.createElement('button');
        btn.className = 'option-btn';
        
        if (q.tipe === 'ikrab') {
            btn.style.fontSize = '26px';
            btn.style.textAlign = 'center';
        } else if (/[آ-ی]/u.test(opt)) {
            btn.setAttribute('dir', 'rtl');
            btn.style.fontSize = '20px';
            btn.style.fontFamily = "'LPMQ IsepMisbah', serif";
        } else {
            btn.style.textAlign = 'left';
        }
        btn.textContent = opt; 
        btn.onclick = () => checkAnswer(opt, btn);
        optionArea.appendChild(btn);
    });
}

function checkAnswer(selectedOption, clickedButton) {
    let q = quizQuestions[currentQuestionIndex];
    const optionButtons = document.querySelectorAll('.option-btn');
    const feedback = document.getElementById('feedback');
    const nextBtn = document.getElementById('next-btn');

    // Kunci semua opsi agar tidak dapat diklik ulang
    optionButtons.forEach(btn => btn.disabled = true);

    let isCorrect = (selectedOption === q.jawaban_benar);

    if (isCorrect) {
        clickedButton.style.backgroundColor = '#2ec4b6'; // Hijau benar
        clickedButton.style.color = '#fff';
        correctAnswersCount++;
    } else {
        clickedButton.style.backgroundColor = '#e71d36'; // Merah salah
        clickedButton.style.color = '#fff';
        
        // Highlight otomatis jawaban yang benar
        optionButtons.forEach(btn => {
            if (btn.textContent === q.jawaban_benar) {
                btn.style.backgroundColor = '#2ec4b6';
                btn.style.color = '#fff';
            }
        });
    }

    // Tampilkan Kunci Ayat Utuh
    if (feedback) {
        feedback.innerHTML = `<div style="margin-top:10px; padding:12px; border-radius:5px; background:#f8f9fa; border:1px solid #ddd;">
                                <strong>Kunci Ayat Lengkap:</strong><br>
                                <div dir="rtl" style="font-size:22px; margin-top:5px; font-family:'LPMQ IsepMisbah', serif; line-height:1.8;">${q.ayat_lengkap}</div>
                              </div>`;
        feedback.classList.remove('hidden');
    }
    if (nextBtn) nextBtn.style.display = 'block';
}

const nextButtonElement = document.getElementById('next-btn');
if (nextButtonElement) {
    nextButtonElement.addEventListener('click', function() {
        currentQuestionIndex++;
        if (currentQuestionIndex < quizQuestions.length) {
            renderQuestion();
        } else {
            showFinalResults();
        }
    });
}

function showFinalResults() {
    if (quizView) quizView.classList.add('hidden');
    if (resultView) resultView.classList.remove('hidden');

    let totalTimeSec = Math.floor((new Date() - startTime) / 1000);
    let score = Math.round((correctAnswersCount / quizQuestions.length) * 100);
    let avgTime = quizQuestions.length > 0 ? Math.round(totalTimeSec / quizQuestions.length) : 0;

    document.getElementById('res-correct').innerText = correctAnswersCount;
    document.getElementById('res-total').innerText = quizQuestions.length;
    document.getElementById('res-score').innerText = score;
    document.getElementById('res-time').innerText = totalTimeSec + 's';
    document.getElementById('res-avg').innerText = avgTime;
}