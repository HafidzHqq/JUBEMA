<?php

namespace App\Support;

use App\Models\Consultation;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

/**
 * Konfigurasi pertanyaan & opsi Formulir Konsultasi BK yang dapat
 * diubah langsung oleh Guru BK dari Ruang Guru BK.
 */
class ConsultationFormConfig
{
    public const SETTING_KEY = 'consultation_form_config';

    public static function defaults(): array
    {
        return [
            'intro' => 'Halo, Teman-Teman! Halaman ini disediakan khusus bagi kamu yang ingin berdiskusi, bertanya, atau meminta bantuan seputar manajemen waktu belajar, mengatasi prokrastinasi (penundaan tugas), atau hambatan belajar lainnya bersama Guru BK. Catatan dan datamu dijamin kerahasiaannya. Silakan isi formulir di bawah ini dengan jujur ya!',
            'q1_label' => 'Bagian mana dari pengelolaan waktu belajar yang saat ini paling membuatmu kesulitan?',
            'q1_options' => Consultation::DIFFICULTIES,
            'q1_allow_other' => true,
            'q2_label' => 'Bagaimana tingkat fokus atau kendali kamu terhadap waktu belajarmu minggu ini?',
            'q2_min_label' => 'Sangat sulit dikendalikan / Berantakan',
            'q2_max_label' => 'Sangat terkendali / Lancar',
            'q3_label' => 'Topik utama apa yang ingin kamu diskusikan dengan Guru BK?',
            'q3_options' => Consultation::TOPICS,
            'q4_label' => 'Apakah kamu melampirkan atau membawa catatan/ringkasan dari Jurnal Belajar Mandiri-mu?',
            'q4_options' => Consultation::JOURNAL_OPTIONS,
            'q5_label' => 'Kapan waktu luang yang kamu preferensikan untuk melakukan sesi konsultasi?',
            'q5_options' => Consultation::PREFERRED_TIMES,
            'q6_label' => 'Cara pendampingan atau bentuk pertemuan yang paling nyaman untukmu:',
            'q6_options' => Consultation::METHODS,
            'note_label' => 'Cerita atau catatan tambahan',
            'note_placeholder' => 'Kalau ada detail tambahan yang ingin kamu ceritakan lebih awal kepada Guru BK, tulis di sini...',
        ];
    }

    public static function get(): array
    {
        $defaults = self::defaults();

        try {
            if (! Schema::hasTable('settings')) {
                return $defaults;
            }

            $raw = Setting::get(self::SETTING_KEY);
            if (! is_string($raw) || trim($raw) === '') {
                return $defaults;
            }

            $decoded = json_decode($raw, true);
            if (! is_array($decoded)) {
                return $defaults;
            }

            return [
                'intro' => self::cleanText($decoded['intro'] ?? null, $defaults['intro']),
                'q1_label' => self::cleanText($decoded['q1_label'] ?? null, $defaults['q1_label']),
                'q1_options' => self::cleanOptions($decoded['q1_options'] ?? null, $defaults['q1_options']),
                'q1_allow_other' => array_key_exists('q1_allow_other', $decoded) ? (bool) $decoded['q1_allow_other'] : $defaults['q1_allow_other'],
                'q2_label' => self::cleanText($decoded['q2_label'] ?? null, $defaults['q2_label']),
                'q2_min_label' => self::cleanText($decoded['q2_min_label'] ?? null, $defaults['q2_min_label']),
                'q2_max_label' => self::cleanText($decoded['q2_max_label'] ?? null, $defaults['q2_max_label']),
                'q3_label' => self::cleanText($decoded['q3_label'] ?? null, $defaults['q3_label']),
                'q3_options' => self::cleanOptions($decoded['q3_options'] ?? null, $defaults['q3_options']),
                'q4_label' => self::cleanText($decoded['q4_label'] ?? null, $defaults['q4_label']),
                'q4_options' => self::cleanOptions($decoded['q4_options'] ?? null, $defaults['q4_options']),
                'q5_label' => self::cleanText($decoded['q5_label'] ?? null, $defaults['q5_label']),
                'q5_options' => self::cleanOptions($decoded['q5_options'] ?? null, $defaults['q5_options']),
                'q6_label' => self::cleanText($decoded['q6_label'] ?? null, $defaults['q6_label']),
                'q6_options' => self::cleanOptions($decoded['q6_options'] ?? null, $defaults['q6_options']),
                'note_label' => self::cleanText($decoded['note_label'] ?? null, $defaults['note_label']),
                'note_placeholder' => self::cleanText($decoded['note_placeholder'] ?? null, $defaults['note_placeholder']),
            ];
        } catch (\Throwable $e) {
            return $defaults;
        }
    }

    public static function save(array $config): void
    {
        $defaults = self::defaults();

        $normalized = [
            'intro' => self::cleanText($config['intro'] ?? null, $defaults['intro']),
            'q1_label' => self::cleanText($config['q1_label'] ?? null, $defaults['q1_label']),
            'q1_options' => self::cleanOptions($config['q1_options'] ?? null, $defaults['q1_options']),
            'q1_allow_other' => (bool) ($config['q1_allow_other'] ?? false),
            'q2_label' => self::cleanText($config['q2_label'] ?? null, $defaults['q2_label']),
            'q2_min_label' => self::cleanText($config['q2_min_label'] ?? null, $defaults['q2_min_label']),
            'q2_max_label' => self::cleanText($config['q2_max_label'] ?? null, $defaults['q2_max_label']),
            'q3_label' => self::cleanText($config['q3_label'] ?? null, $defaults['q3_label']),
            'q3_options' => self::cleanOptions($config['q3_options'] ?? null, $defaults['q3_options']),
            'q4_label' => self::cleanText($config['q4_label'] ?? null, $defaults['q4_label']),
            'q4_options' => self::cleanOptions($config['q4_options'] ?? null, $defaults['q4_options']),
            'q5_label' => self::cleanText($config['q5_label'] ?? null, $defaults['q5_label']),
            'q5_options' => self::cleanOptions($config['q5_options'] ?? null, $defaults['q5_options']),
            'q6_label' => self::cleanText($config['q6_label'] ?? null, $defaults['q6_label']),
            'q6_options' => self::cleanOptions($config['q6_options'] ?? null, $defaults['q6_options']),
            'note_label' => self::cleanText($config['note_label'] ?? null, $defaults['note_label']),
            'note_placeholder' => self::cleanText($config['note_placeholder'] ?? null, $defaults['note_placeholder']),
        ];

        Setting::put(self::SETTING_KEY, json_encode($normalized, JSON_UNESCAPED_UNICODE));
    }

    public static function reset(): void
    {
        Setting::query()->where('key', self::SETTING_KEY)->delete();
    }

    private static function cleanText(mixed $value, string $fallback): string
    {
        if (! is_string($value)) {
            return $fallback;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : $fallback;
    }

    /**
     * @return list<string>
     */
    private static function cleanOptions(mixed $options, array $fallback): array
    {
        if (! is_array($options)) {
            return $fallback;
        }

        $cleaned = [];
        foreach ($options as $opt) {
            if (! is_string($opt)) {
                continue;
            }
            $text = trim($opt);
            if ($text !== '' && ! in_array($text, $cleaned, true)) {
                $cleaned[] = $text;
            }
        }

        return count($cleaned) > 0 ? $cleaned : $fallback;
    }
}
