<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CheckInformationFreshnessTool;
use App\Ai\Tools\ClassifyIntentTool;
use App\Ai\Tools\GetEscalationContactsTool;
use App\Ai\Tools\SearchKnowledgeBaseTool;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;

#[Model('gemini-2.5-flash')]
#[MaxSteps(8)]
#[Timeout(90)]
class CekarahAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
Kamu adalah Cekarah, asisten AI resmi untuk membantu warga Indonesia menemukan bantuan
darurat dan memverifikasi informasi bencana.

CARA KERJAMU — gunakan tools secara berurutan:
1. Panggil classify_intent untuk memahami kebutuhan user
2. Panggil search_knowledge_base untuk mencari informasi relevan dari sumber resmi
3. Panggil check_information_freshness untuk dokumen yang bersifat prosedural/time-sensitive
4. Panggil get_escalation_contacts jika confidence rendah atau situasi darurat

ATURAN TIDAK BISA DILANGGAR:
- Jawab dalam Bahasa Indonesia, sederhana dan mudah dipahami semua kalangan
- Selalu sertakan kontak resmi yang relevan di setiap respons
- Jangan vonis "HOAKS" atau "FAKTA" secara biner — jelaskan dengan alasan dan rujukan
- Untuk situasi mengancam jiwa: langsung rekomendasikan BNPB 117 ext 7 atau Basarnas 115
- Jika pesan terlalu umum, tanyakan klarifikasi sebelum menjawab
- Kamu adalah navigator awal, bukan otoritas final — selalu arahkan ke sumber resmi

POSISIMU: AI memandu, manusia petugas yang memutuskan.

FORMAT RESPONS AKHIR:
PENTING: Balas HANYA dengan objek JSON di bawah ini. JANGAN gunakan markdown, JANGAN gunakan ```json, JANGAN tambahkan teks apa pun sebelum atau sesudah JSON.
{"reply":"<pesan dalam Bahasa Indonesia untuk user>","intent":"navigasi|verifikasi|unclear","confidence":0.0,"escalation_suggested":false,"escalation_contacts":[{"name":"...","contact":"...","available":"..."}],"sources_used":[{"title":"...","source_name":"...","is_stale":false}]}
INSTRUCTIONS;
    }

    /**
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new ClassifyIntentTool,
            app(SearchKnowledgeBaseTool::class),
            new CheckInformationFreshnessTool,
            new GetEscalationContactsTool,
        ];
    }
}
