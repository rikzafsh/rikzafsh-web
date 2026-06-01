<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HafidzQuiz - Bermain</title>
    <link rel="stylesheet" href="<?= base_url('css/HafidzQuiz.css'); ?>">
    <style>
        .hidden { display: none !important; }
        .ayat-box { font-family: "LPMQ IsepMisbah", serif; text-align: center; font-size: 24px; line-height: 2; dir: rtl; margin-bottom: 20px; }
        .option-btn { display: block; width: 100%; margin: 8px 0; padding: 12px; text-align: right; font-family: "LPMQ IsepMisbah", serif; font-size: 18px; cursor: pointer; }
        .word-blank { display: inline-block; border-bottom: 2px dashed #333; min-width: 60px; text-align: center; margin: 0 5px; color: #3d46e0; }
        #feedback { padding: 15px; margin-top: 15px; border-radius: 4px; font-weight: bold; text-align: center; }
        #feedback.correct { background-color: #d4edda; color: #155724; }
        #feedback.incorrect { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        
        <div id="quiz-view">
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
            <button onclick="window.location.href='<?= base_url('HafidzQuiz'); ?>'" class="btn-primary" style="margin-top: 30px; width: 100%;">Muroja'ah Lagi</button>
        </div>

    </div>

    <script>
        const BASE_URL = "<?= rtrim(base_url(), '/') ?>/";
    </script>
    
    <script src="<?= base_url('js/HafidzQuiz.js'); ?>"></script>
</body>
</html>