<?php

namespace App\Libraries;

class QuranJsonParser {
    public $data = [];

    public function __construct($json_filename) {
        // Gunakan FCPATH agar langsung menunjuk ke folder public/data/
        $json_path = FCPATH . 'data/' . $json_filename;
        
        if (file_exists($json_path)) {
            $json_string = file_get_contents($json_path);
            $this->data = json_decode($json_string, true);
        }
    }

    public function getWord($ayahData, $wordIndex) {
        if (isset($ayahData['arabic_words']) && is_array($ayahData['arabic_words'])) {
            $words = $ayahData['arabic_words'];
        } else {
            $words = explode(' ', $ayahData['arabic']);
        }
        return isset($words[$wordIndex - 1]) ? $words[$wordIndex - 1] : null;
    }

    public function splitAyahForQuestion($ayahData) {
        $fullText = $ayahData['arabic'];
        if (isset($ayahData['arabic_words']) && is_array($ayahData['arabic_words'])) {
            $words = $ayahData['arabic_words'];
        } else {
            $words = explode(' ', $fullText);
        }
        
        $pattern = '/([ۖۗۘۙۚۛۜۮࣖ])/u';
        $parts = preg_split($pattern, $fullText, 2, PREG_SPLIT_DELIM_CAPTURE);
        
        if (count($parts) > 2 && trim($parts[2]) !== '') {
            $soal = trim($parts[0] . $parts[1]); 
            $jawaban = trim($parts[2]);          
        } else {
            $totalWords = count($words);
            if ($totalWords === 0) return ['soal' => $fullText, 'jawaban' => ''];
            $middle = floor($totalWords / 2);
            $soal = implode(' ', array_slice($words, 0, $middle));
            $jawaban = implode(' ', array_slice($words, $middle));
        }

        return ['soal' => $soal, 'jawaban' => $jawaban];
    }
}