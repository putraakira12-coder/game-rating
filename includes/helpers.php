<?php
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function rupiah(int|float $amount): string
{
    if ((float)$amount <= 0) {
        return 'Gratis';
    }
    return 'Rp' . number_format((float)$amount, 0, ',', '.');
}

function angka(int|float $amount): string
{
    return number_format((float)$amount, 0, ',', '.');
}

function tanggal(string $value): string
{
    $timestamp = strtotime($value);
    return $timestamp ? date('d M Y', $timestamp) : $value;
}

function potong(string $text, int $length = 110): string
{
    $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length - 2) . '…' : $text;
}

function badge_kelas(float $rating): string
{
    if ($rating >= 9) return 'emas';
    if ($rating >= 8) return 'ungu';
    if ($rating >= 7) return 'biru';
    return 'abu';
}

function teks_umur(string $umur): string
{
    return match ($umur) {
        'SU' => 'Semua Umur',
        '7+' => '7 Tahun+',
        '10+' => '10 Tahun+',
        '12+' => '12 Tahun+',
        '13+' => '13 Tahun+',
        '16+' => '16 Tahun+',
        '17+' => '17 Tahun+',
        '18+' => '18 Tahun+',
        default => $umur,
    };
}

function icon_platform(string $slug): string
{
    return match ($slug) {
        'steam' => 'bi-microsoft',
        'playstation' => 'bi-playstation',
        'nintendo' => 'bi-nintendo-switch',
        'playstore' => 'bi-google-play',
        'appstore' => 'bi-apple',
        'xbox' => 'bi-xbox',
        default => 'bi-controller',
    };
}

function url_mulai(string $url): string
{
    return preg_match('~^https?://~i', $url) ? $url : 'https://' . $url;
}

function slug_foto(string $nama): string
{
    $nama = strtolower(pathinfo($nama, PATHINFO_FILENAME));
    $nama = preg_replace('/[^a-z0-9]+/', '-', $nama) ?? 'foto';
    return trim($nama, '-') ?: 'foto';
}
