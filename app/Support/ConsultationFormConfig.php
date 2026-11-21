<?php

namespace App\Support;

use App\Models\Consultation;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Konfigurasi pertanyaan & opsi Formulir Konsultasi BK yang dapat
 * ditambah, diubah, diurutkan, maupun dihapus langsung oleh Guru BK.
 */
class ConsultationFormConfig
{
    public const SETTING_KEY = 'consultation_form_config';

    public const QUESTION_TYPES = [
        'radio' => 'Pilihan Ganda',
        'scale' => 'Skala 1–4',
        'text' => 'Jawaban Singkat',
        'textarea' => 'Paragraf / Jawaban Panjang',
    ];

    public static function defaults(): array
    {
        return [
            'intro' => 'Halo, Teman-Teman! Halaman ini disediakan khusus bagi kamu yang ingin berdiskusi, bertanya, atau meminta bantuan seputar manajemen waktu belajar, mengatasi prokrastinasi (penundaan tugas), atau hambatan belajar lainnya bersama Guru BK. Catatan dan datamu dijamin kerahasiaannya. Silakan isi formulir di bawah ini dengan jujur ya!',
            'questions' => [
                [
                    'id' => 'difficulty',
                    'label' => 'Bagian mana dari pengelolaan waktu belajar yang saat ini paling membuatmu kesulitan?',
                    'type' => 'radio',
                    'required' => true,
                    'options' => Consultation::DIFFICULTIES,
                    'allow_other' => true,
                    'min_label' => null,
                    'max_label' => null,
                    'placeholder' => null,
                ],
                [
                    'id' => 'focus_level',
                    'label' => 'Bagaimana tingkat fokus atau kendali kamu terhadap waktu belajarmu minggu ini?',
                    'type' => 'scale',
                    'required' => true,
                    'options' => [],
                    'allow_other' => false,
                    'min_label' => 'Sangat sulit dikendalikan / Berantakan',
                    'max_label' => 'Sangat terkendali / Lancar',
                    'placeholder' => null,
                ],
                [
                    'id' => 'topic',
                    'label' => 'Topik utama apa yang ingin kamu diskusikan dengan Guru BK?',
                    'type' => 'radio',
                    'required' => true,
                    'options' => Consultation::TOPICS,
                    'allow_other' => false,
                    'min_label' => null,
                    'max_label' => null,
                    'placeholder' => null,
                ],
                [
                    'id' => 'bring_journal',
                    'label' => 'Apakah kamu melampirkan atau membawa catatan/ringkasan dari Jurnal Belajar Mandiri-mu?',
                    'type' => 'radio',
                    'required' => true,
                    'options' => Consultation::JOURNAL_OPTIONS,
                    'allow_other' => false,
                    'min_label' => null,
                    'max_label' => null,
                    'placeholder' => null,
                ],
                [
                    'id' => 'mode',
                    'label' => 'Kapan waktu luang yang kamu preferensikan untuk melakukan sesi konsultasi?',
                    'type' => 'radio',
                    'required' => true,
                    'options' => Consultation::PREFERRED_TIMES,
                    'allow_other' => false,
                    'min_label' => null,
                    'max_label' => null,
                    'placeholder' => null,
                ],
                [
                    'id' => 'consultation_method',
                    'label' => 'Cara pendampingan atau bentuk pertemuan yang paling nyaman untukmu:',
                    'type' => 'radio',
                    'required' => true,
                    'options' => Consultation::METHODS,
                    'allow_other' => false,
                    'min_label' => null,
                    'max_label' => null,
                    'placeholder' => null,
                ],
                [
                    'id' => 'message',
                    'label' => 'Cerita atau catatan tambahan',
                    'type' => 'textarea',
                    'required' => false,
                    'options' => [],
                    'allow_other' => false,
                    'min_label' => null,
                    'max_label' => null,
                    'placeholder' => 'Kalau ada detail tambahan yang ingin kamu ceritakan lebih awal kepada Guru BK, tulis di sini...',
                ],
            ],
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

            // Format baru: daftar pertanyaan dinamis.
            if (isset($decoded['questions']) && is_array($decoded['questions'])) {
                $questions = self::normalizeQuestions($decoded['questions']);

                return [
                    'intro' => self::cleanText($decoded['intro'] ?? null, $defaults['intro']),
                    'questions' => count($questions) > 0 ? $questions : $defaults['questions'],
                ];
            }

            // Kompatibilitas mundur: format lama (q1..q6 + note).
            return self::migrateLegacyConfig($decoded, $defaults);
        } catch (\Throwable $e) {
            return $defaults;
        }
    }

    public static function save(array $config): void
    {
        $defaults = self::defaults();
        $questions = self::normalizeQuestions($config['questions'] ?? []);

        $normalized = [
            'intro' => self::cleanText($config['intro'] ?? null, $defaults['intro']),
            'questions' => count($questions) > 0 ? $questions : $defaults['questions'],
        ];

        Setting::put(self::SETTING_KEY, json_encode($normalized, JSON_UNESCAPED_UNICODE));
    }

    public static function reset(): void
    {
        Setting::query()->where('key', self::SETTING_KEY)->delete();
    }

    /**
     * Normalisasi daftar pertanyaan dari input atau penyimpanan JSON.
     *
     * @return list<array<string, mixed>>
     */
    public static function normalizeQuestions(mixed $rawQuestions): array
    {
        if (! is_array($rawQuestions)) {
            return [];
        }

        $normalized = [];
        $usedIds = [];

        foreach (array_values($rawQuestions) as $idx => $item) {
            if (! is_array($item)) {
                continue;
            }

            $label = is_string($item['label'] ?? null) ? trim($item['label']) : '';
            if ($label === '') {
                continue;
            }

            $type = is_string($item['type'] ?? null) && array_key_exists($item['type'], self::QUESTION_TYPES)
                ? $item['type']
                : 'text';

            $rawId = is_string($item['id'] ?? null) ? trim($item['id']) : '';
            $id = preg_replace('/[^a-zA-Z0-9_]/', '', $rawId) ?: 'q_'.($idx + 1).'_'.Str::lower(Str::random(5));
            while (in_array($id, $usedIds, true)) {
                $id = 'q_'.Str::lower(Str::random(6));
            }
            $usedIds[] = $id;

            $options = [];
            if ($type === 'radio') {
                $options = self::cleanOptions($item['options'] ?? []);
                if (count($options) === 0) {
                    continue;
                }
            }

            $normalized[] = [
                'id' => $id,
                'label' => Str::limit($label, 250, ''),
                'type' => $type,
                'required' => (bool) ($item['required'] ?? false),
                'options' => $options,
                'allow_other' => $type === 'radio' ? (bool) ($item['allow_other'] ?? false) : false,
                'min_label' => $type === 'scale'
                    ? self::cleanText($item['min_label'] ?? null, 'Sangat kurang')
                    : null,
                'max_label' => $type === 'scale'
                    ? self::cleanText($item['max_label'] ?? null, 'Sangat baik')
                    : null,
                'placeholder' => in_array($type, ['text', 'textarea'], true)
                    ? (is_string($item['placeholder'] ?? null) && trim($item['placeholder']) !== ''
                        ? Str::limit(trim($item['placeholder']), 250, '')
                        : 'Tuliskan jawabanmu di sini...')
                    : null,
            ];
        }

        return $normalized;
    }

    private static function migrateLegacyConfig(array $decoded, array $defaults): array
    {
        $byId = [];
        foreach ($defaults['questions'] as $q) {
            $byId[$q['id']] = $q;
        }

        $byId['difficulty']['label'] = self::cleanText($decoded['q1_label'] ?? null, $byId['difficulty']['label']);
        $byId['difficulty']['options'] = self::cleanOptions($decoded['q1_options'] ?? null, $byId['difficulty']['options']);
        $byId['difficulty']['allow_other'] = array_key_exists('q1_allow_other', $decoded)
            ? (bool) $decoded['q1_allow_other']
            : $byId['difficulty']['allow_other'];

        $byId['focus_level']['label'] = self::cleanText($decoded['q2_label'] ?? null, $byId['focus_level']['label']);
        $byId['focus_level']['min_label'] = self::cleanText($decoded['q2_min_label'] ?? null, $byId['focus_level']['min_label']);
        $byId['focus_level']['max_label'] = self::cleanText($decoded['q2_max_label'] ?? null, $byId['focus_level']['max_label']);

        $byId['topic']['label'] = self::cleanText($decoded['q3_label'] ?? null, $byId['topic']['label']);
        $byId['topic']['options'] = self::cleanOptions($decoded['q3_options'] ?? null, $byId['topic']['options']);

        $byId['bring_journal']['label'] = self::cleanText($decoded['q4_label'] ?? null, $byId['bring_journal']['label']);
        $byId['bring_journal']['options'] = self::cleanOptions($decoded['q4_options'] ?? null, $byId['bring_journal']['options']);

        $byId['mode']['label'] = self::cleanText($decoded['q5_label'] ?? null, $byId['mode']['label']);
        $byId['mode']['options'] = self::cleanOptions($decoded['q5_options'] ?? null, $byId['mode']['options']);

        $byId['consultation_method']['label'] = self::cleanText($decoded['q6_label'] ?? null, $byId['consultation_method']['label']);
        $byId['consultation_method']['options'] = self::cleanOptions($decoded['q6_options'] ?? null, $byId['consultation_method']['options']);

        $byId['message']['label'] = self::cleanText($decoded['note_label'] ?? null, $byId['message']['label']);
        $byId['message']['placeholder'] = self::cleanText($decoded['note_placeholder'] ?? null, $byId['message']['placeholder']);

        return [
            'intro' => self::cleanText($decoded['intro'] ?? null, $defaults['intro']),
            'questions' => array_values($byId),
        ];
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
    private static function cleanOptions(mixed $options, array $fallback = []): array
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
                $cleaned[] = Str::limit($text, 200, '');
            }
        }

        return count($cleaned) > 0 ? $cleaned : $fallback;
    }
}
