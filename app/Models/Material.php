<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta',
        'summary',
        'source_type',
        'file_path',
        'file_name',
        'external_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * URL yang digunakan di dalam iframe pembaca materi pada halaman siswa.
     */
    public function previewUrl(): string
    {
        if ($this->source_type === 'file') {
            return route('materi.file', $this);
        }

        $url = trim((string) $this->external_url);

        // Google Docs -> /preview agar langsung tampil bersih di iframe
        if (preg_match('#https?://docs\.google\.com/document/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
            return "https://docs.google.com/document/d/{$m[1]}/preview";
        }

        // Google Slides / Sheets -> /preview
        if (preg_match('#https?://docs\.google\.com/(presentation|spreadsheets)/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
            return "https://docs.google.com/{$m[1]}/d/{$m[2]}/preview";
        }

        // Google Drive file -> /preview
        if (preg_match('#https?://drive\.google\.com/file/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
            return "https://drive.google.com/file/d/{$m[1]}/preview";
        }

        return $url;
    }

    /**
     * URL cadangan untuk membuka materi di tab baru.
     */
    public function openUrl(): string
    {
        return $this->source_type === 'file'
            ? route('materi.file', $this)
            : (string) $this->external_url;
    }

    public function sourceLabel(): string
    {
        if ($this->source_type === 'file') {
            return 'File PDF';
        }

        $url = strtolower((string) $this->external_url);
        if (str_contains($url, 'docs.google.com') || str_contains($url, 'drive.google.com')) {
            return 'Google Docs / Drive';
        }

        return 'Tautan Web';
    }
}
