<?php

namespace App\Services;

use App\Models\Disease;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\UploadedFile;

class GeminiService
{
    public function generate($imageFormat = 'image/jpeg', $file, $prompt)
    {
        $template = "
            Jelaskan secara mendetail berdasarkan foto dan informasi berikut dan buatkan dalam format JSON:

            {$prompt}

            Berikan informasi dalam format JSON dengan struktur sebagai berikut, hanya json saja, tanpa tambahan apapun lagi, tanpa menambahkan tulisan json lagi di jawabanmu, hanya hasil json nya saja:

            {
            \"nama_penyakit\": \"<Nama penyakit<string>> {jika terdapat namanya tolong berikan nama lengkapnya}\",
            \"nama_hama\": \"<Nama hama<string>> {jika terdapat namanya tolong berikan nama lengkapnya}\",
            \"gejala\": \"<gejalan<string | array>> {ini opsional bisa array atau string, tergantung jawabanmu}\",
            \"penyebab\": \"<Penyebab<string | array>> {ini opsional bisa array atau string, tergantung jawabanmu}\",
            \"pengobatan\": \"<Pengobatan<string | array>> {ini opsional bisa array atau string, tergantung jawabanmu}\",
            \"pengendalian\": \"<pengendalian<string | array>> {ini opsional bisa array atau string, tergantung jawabanmu}\"
            }
        ";

        $geminiResponse = Gemini::generativeModel('gemini-1.5-pro')->generateContent([
            $template,
            new Blob(
                mimeType: MimeType::IMAGE_JPEG,
                data: base64_encode(
                    file_get_contents($file)
                )
            )
        ]);

        $response = json_decode($geminiResponse->text());

        $diseases = Disease::query()
            ->get();

        $simhash = new SimhashService();

        $diseaseName = $response?->nama_penyakit ?? '-';
        $pestName = $response?->nama_hama ?? '-';

        foreach($diseases as $disease) {
            if ($simhash->isSimilar($response->nama_penyakit, $disease->name)) {
                $diseaseName = $disease->name;
                $response->nama_penyakit = $disease->name;
                $response->pengendalian = $disease->control;
                $response->pengobatan = $disease->cure_name;
                break;
            }
        }

        return [$template, json_encode($response), $diseaseName, $pestName, $response];
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
