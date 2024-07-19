<?php

namespace App\Services;

use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Http\UploadedFile;

class GeminiService
{
    public function generate($imageFormat = 'image/jpeg', $file, $prompt)
    {
        $template = "
            Jelaskan secara mendetail berdasarkan informasi berikut dan buatkan dalam format JSON:

            {$prompt}

            Berikan informasi dalam format JSON dengan struktur sebagai berikut:

            {
            \"nama_penyakit\": \"<Nama penyakit>\",
            \"nama_hama\": \"<Nama hama>\",
            \"gejala\": \"<gejalan>\",
            \"penyebab\": \"<Penyebab>\",
            \"pengendalian\": \"<Pengendalian>\"
            }
        ";

        $geminiResponse = Gemini::generateTextUsingImageFile(
            $imageFormat,
            $file,
            $template
        );

        $response = json_decode($geminiResponse);

        $diseaseName = $response->nama_penyakit;
        $pestName = $response->nama_hama;

        return [$geminiResponse, $diseaseName, $pestName];
    }

    public function convertResponseToHTML($text)
    {
        $html = '';

        // Nama Penyakit
        if (preg_match('/\*\*Nama penyakit:\*\*\s*(.*)/', $text, $matches)) {
            $html .= '<h2 class="gemini-header">Nama Penyakit: </h2>';
            $html .= '<p>' . trim($matches[1]) . '</p>';
        }

        // Nama Hama
        if (preg_match('/\*\*Nama Hama:\*\*\s*(.*)/', $text, $matches)) {
            $html .= '<h3 class="gemini-subhead">Nama Hama: </h3>';
            $html .= '<p>' . trim($matches[1]) . '</p>';
        }

        // Gejala
        if (preg_match('/\*\*Gejala:\*\*\s*(.*?)(?=\*\*|\z)/s', $text, $matches)) {
            $gejala = explode("\n", trim($matches[1]));
            $html .= '<h3 class="gemini-subhead">Gejala:</h3><ul class="gemini-list">';
            foreach ($gejala as $gejalaItem) {
                if (!empty(trim($gejalaItem))) {
                    $html .= '<li class="gemini-list-item">' . trim($gejalaItem, '- ') . '</li>';
                }
            }
            $html .= '</ul>';
        }

        // Penyebab
        if (preg_match('/\*\*Penyebab:\*\*\s*(.*?)(?=\*\*|\z)/s', $text, $matches)) {
            $html .= '<h3 class="gemini-subhead">Penyebab:</h3>';
            $html .= '<p>' . trim($matches[1]) . '</p>';
        }

        // Pengendalian
        if (preg_match('/\*\*Pengendalian:\*\*\s*(.*?)(?=\*\*|\z)/s', $text, $matches)) {
            $pengendalian = explode("\n", trim($matches[1]));
            $html .= '<h3 class="gemini-subhead">Pengendalian:</h3><ul class="gemini-list">';
            foreach ($pengendalian as $pengendalianItem) {
                if (!empty(trim($pengendalianItem))) {
                    $html .= '<li class="gemini-list-item">' . trim($pengendalianItem, '- ') . '</li>';
                }
            }
            $html .= '</ul>';
        }

        return $html;
    }
}
