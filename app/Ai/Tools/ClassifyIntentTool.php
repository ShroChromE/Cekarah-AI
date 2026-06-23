<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ClassifyIntentTool implements Tool
{
    public function description(): string
    {
        return 'Klasifikasikan intent pesan user: apakah mencari navigasi bantuan, memverifikasi klaim/informasi, atau tidak jelas. Panggil ini pertama kali sebelum tool lain.';
    }

    public function handle(Request $request): string
    {
        $message = strtolower($request['message']);

        $navigationKeywords = [
            'bantuan', 'daftar', 'cara', 'bagaimana', 'prosedur', 'langkah',
            'evakuasi', 'mengungsi', 'posko', 'logistik', 'pkh', 'bpnt',
            'bansos', 'kemensos', 'bnpb', 'basarnas', 'pmi', 'hilang', 'darurat',
        ];

        $verificationKeywords = [
            'benarkah', 'benar', 'hoaks', 'hoax', 'palsu', 'viral', 'beredar',
            'katanya', 'konon', 'isu', 'kabar', 'rumor', 'cek', 'verifikasi',
            'tsunami', 'gempa susulan', 'rekening donasi', 'sebarkan',
        ];

        $navScore = 0;
        $verScore = 0;

        foreach ($navigationKeywords as $kw) {
            if (str_contains($message, $kw)) {
                $navScore++;
            }
        }

        foreach ($verificationKeywords as $kw) {
            if (str_contains($message, $kw)) {
                $verScore++;
            }
        }

        if ($navScore === 0 && $verScore === 0) {
            $intent = 'unclear';
            $strength = 'low';
            $suggestion = 'Minta user menjelaskan lebih spesifik: apakah mencari bantuan atau ingin memverifikasi informasi?';
        } elseif ($navScore >= $verScore) {
            $intent = 'navigasi';
            $strength = $navScore >= 2 ? 'high' : 'medium';
            $suggestion = 'Cari prosedur dan kontak bantuan yang relevan dari knowledge base.';
        } else {
            $intent = 'verifikasi';
            $strength = $verScore >= 2 ? 'high' : 'medium';
            $suggestion = 'Cari informasi verifikasi dari sumber resmi. Jangan vonis hoaks tanpa rujukan.';
        }

        return json_encode([
            'intent' => $intent,
            'signal_strength' => $strength,
            'suggestion' => $suggestion,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'message' => $schema->string()->required(),
        ];
    }
}
