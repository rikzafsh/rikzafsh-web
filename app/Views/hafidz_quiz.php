<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HafidzQuiz</title>
    <link rel="stylesheet" href="<?= base_url('css/HafidzQuiz.css'); ?>">
    <style>
        /* Tambahan style helper untuk menyembunyikan view */
        .hidden { display: none !important; }
        .ayat-box { font-family: "LPMQ IsepMisbah", serif; text-align: center; }
        .option-btn { display: block; width: 100%; margin: 8px 0; padding: 12px; text-align: right; }
        /* Style drop-area sederhana jika memakai tipe lengkapi_ayat */
        .word-blank { display: inline-block; border-bottom: 2px dashed #333; min-width: 50px; text-align: center; margin: 0 5px; color: #3d46e0; }
    </style>
</head>
<body>
    <div class="container">
        
        <div id="setup-view">
            <h2>HafidzQuiz Setup</h2>
            <form id="quiz-config-form">
                <div class="form-group">
                    <label>Pilih Surah:</label>
                    <select id="surah-select" required>
                        <option value="" disabled selected>Memuat Surah...</option>
                    </select>
                </div>
                
                <div class="form-group" style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label>Ayat Awal:</label>
                        <select id="ayah-start" required><option value="">-</option></select>
                    </div>
                    <div style="flex: 1;">
                        <label>Ayat Akhir:</label>
                        <select id="ayah-end" required><option value="">-</option></select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Jumlah Soal:</label>
                    <input type="number" id="jumlah-soal" value="5" min="1" max="50" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; color: var(--primary);">Jenis Soal:</label><br>
                    <input type="checkbox" name="quiz-type" value="sambung" checked> Sambung Ayat<br>
                    <input type="checkbox" name="quiz-type" value="lengkapi"> Lengkapi Ayat<br>
                    <input type="checkbox" name="quiz-type" value="sebelum"> Tebak Ayat Sebelumnya<br>
                    <input type="checkbox" name="quiz-type" value="sesudah"> Tebak Ayat Sesudahnya<br>
                    <input type="checkbox" name="quiz-type" value="nomor"> Tebak Nomor Ayat / Lafaz<br>
                    <input type="checkbox" name="quiz-type" value="ikrab"> Ikrab (Harakat Akhir)<br>
                </div>

                <div class="form-group">
                    <label>Mode Soal:</label>
                    <input type="radio" id="mode-direct" name="mode" value="direct" checked> Tampilkan Benar/Salah Langsung
                </div>

                <button type="submit" class="btn-primary">Mulai Kuis</button>
            </form>
        </div>

        <div id="quiz-view" class="hidden">
            <div id="quiz-header">
                <h2 id="quiz-title">HafidzQuiz</h2>
                <p style="text-align:center">Soal <span id="current-index">1</span> dari <span id="total-count">0</span></p>
            </div>

            <div id="question-area" class="question-box">Memuat soal...</div>
            <div id="option-area" class="option-container"></div>
            <div id="feedback" class="hidden"></div>
            <button id="next-btn" class="btn-primary" style="margin-top:20px; display:none">Soal Berikutnya</button>
        </div>

        <div id="result-view" class="hidden">
            <h2 style="color:var(--purple); margin-bottom: 30px;">Hasil Akhir</h2>
            <div class="result-grid">
                <div class="result-card">
                    <div class="card-label">Jawaban Benar</div>
                    <div class="card-value"><span id="res-correct">0</span> / <span id="res-total">0</span></div>
                </div>
                <div class="result-card primary">
                    <div class="card-label">Skor Akhir</div>
                    <div class="card-value"><span id="res-score">0</span>%</div>
                </div>
                <div class="result-card">
                    <div class="card-label">Total Waktu</div>
                    <div class="card-value" id="res-time">0s</div>
                </div>
                <div class="result-card">
                    <div class="card-label">Rata-rata / Soal</div>
                    <div class="card-value"><span id="res-avg">0</span>s</div>
                </div>
            </div>
            <button onclick="location.reload()" class="btn-primary" style="margin-top: 30px; width: 100%;">Muroja'ah Lagi</button>
        </div>

    </div>

    <script src="<?= base_url('js/HafidzQuiz.js'); ?>"></script>
</body>
</html>