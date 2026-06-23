# PROMPT UNTUK CLAUDE CODE — CEKARAH AI (v5 — FINAL)
## Platform Asisten Krisis & Verifikasi Informasi
### LKS Dikmen Tingkat Nasional 2026 — Ekshibisi Kecerdasan Artifisial

---

> **Cara pakai:** Paste seluruh isi ini sebagai pesan pertama ke Claude Code (Sonnet 4.6).
> Jalankan dari root direktori project Laravel 13 yang sudah ada.
> **Kerjakan satu fase dalam satu sesi. Jangan lanjut ke fase berikutnya sebelum fase aktif selesai dan diverifikasi.**

---

## PRINSIP PENGERJAAN — BACA DULU SEBELUM MULAI

**1. Bertahap, bukan sekaligus.**
Kerjakan tepat satu fase per sesi. Di akhir setiap fase, beri ringkasan apa yang sudah dibuat, apa yang masih pending, dan instruksi eksplisit apa yang harus dilakukan user sebelum melanjutkan ke fase berikutnya.

**2. Bersihkan sebelum membangun.**
Sebelum membuat file baru, cek apakah ada file lama (controller, route, model, view, page) yang tidak lagi dibutuhkan dalam arsitektur ini. Hapus yang tidak diperlukan. Jangan biarkan kode orphan menumpuk.

**3. Naming convention wajib — semua dalam bahasa Inggris, kecuali konten yang ditampilkan ke user.**

Konten yang boleh berbahasa Indonesia: teks UI (label, placeholder, pesan error, tooltip),
konten dalam database (judul dokumen knowledge base, isi dokumen), respons dari AI.
Semua lainnya wajib bahasa Inggris:

- **Controllers:** `{Resource}Controller` — method: `index`, `store`, `show`, `update`, `destroy`
- **Routes & endpoints:** noun plural, kebab-case, bahasa Inggris
  - `/api/chat-sessions` bukan `/api/sesi-chat`
  - `/api/chat-sessions/{token}/messages` bukan `/api/pesan`
  - Named routes: `chat-sessions.store`, `chat-sessions.messages.store`
- **Models:** singular PascalCase — `ChatSession`, `KnowledgeDocument`, `KnowledgeChunk`
- **Database tables:** snake_case plural — `chat_sessions`, `knowledge_documents`, `knowledge_chunks`
- **Kolom database:** snake_case, bahasa Inggris — `source_name`, `chunk_text`, `is_active`, `indexed_at`
- **Migrations:** `create_chat_sessions_table`, `create_knowledge_documents_table`
- **Artisan Commands:** `knowledge:index`, `knowledge:reindex`
- **AI Agents & Tools:** `CekarahAgent`, `ClassifyIntentTool`, `GetEscalationContactsTool`
- **React Pages & Components:** PascalCase — `Chat`, `Landing`, `About`, `MessageBubble`
- **JS hooks & utils:** camelCase — `useChat`, `useSession`
- **CSS classes:** Tailwind utility names tetap bahasa Inggris (sudah bawaan)

**4. Di akhir semua fase, sampaikan next steps yang jelas.**
Setelah fase terakhir selesai, tulis section "Langkah Selanjutnya" yang berisi: apa yang harus dikonfigurasi, apa yang perlu ditest secara manual, apa yang bisa dikembangkan lebih lanjut jika ada waktu.

---

## STATUS PROJECT

**Sudah ada — jangan diubah kecuali untuk menghapus yang tidak diperlukan:**
- Laravel 13 + Inertia.js + React
- PostgreSQL sudah terhubung
- Struktur folder Laravel bawaan

**Belum ada — yang akan dibangun:**
- `laravel/ai` SDK
- pgvector aktif
- Seluruh fitur Cekarah

---

## IDENTITAS APLIKASI

**Cekarah** — asisten AI berbasis chat untuk warga Indonesia dalam dua situasi kritis:
1. **Navigasi bantuan** — temukan layanan/bantuan resmi (bencana, sosial, darurat)
2. **Verifikasi klaim** — cek kebenaran informasi/hoaks yang beredar

Agent AI mendeteksi kebutuhan user secara otomatis dan memutuskan sendiri tools mana yang dipanggil.

**Kompetisi:** LKS Dikmen Nasional 2026 | **Deadline:** 3 Juli 2026 | **Demo:** 10 menit live

---

## STACK TEKNIS — TIDAK BISA DIUBAH

```
Backend    : Laravel 13 + laravel/ai (first-party SDK)
Frontend   : Inertia.js + React (sudah ada)
AI Provider: Gemini — model teks: gemini-2.0-flash, model embedding: text-embedding-004
             Gunakan enum Lab::Gemini, JANGAN hardcode string
Database   : PostgreSQL + pgvector
Vector     : Model Eloquent + whereVectorSimilarTo dari SDK
Agent      : Pola resmi SDK — implements Agent, Conversational, HasTools, HasStructuredOutput
             Trait RemembersConversations untuk history
             SimilaritySearch tool bawaan SDK untuk RAG
Chat I/O   : axios/fetch ke JSON endpoint — JANGAN lewat Inertia.router
```

---

## DESAIN & UI — PANDUAN WAJIB

Cekarah bukan aplikasi generik. Ini alat darurat — dipakai orang dalam kondisi stres, panik, atau krisis. Desain harus mencerminkan: **tenang, terpercaya, cepat dipahami**.

**Identitas visual Cekarah:**
- **Palette:** Background `#0F172A` (slate-950, gelap dalam) untuk header/sidebar. Area chat `#F8FAFC` (slate-50, hampir putih). Aksen utama `#3B82F6` (blue-500) untuk elemen interaktif. Aksen peringatan `#F59E0B` (amber-500). Darurat `#EF4444` (red-500).
- **Typography:** Gunakan font system stack dengan `font-feature-settings: "ss01"` untuk keterbacaan. Heading bold dan besar. Body text `text-base` dengan `leading-relaxed`. Semua teks UI dalam sentence case, bukan UPPERCASE.
- **Signature element:** Chat bubble AI diberi garis kiri `3px solid #3B82F6` dengan background putih bersih — bukan rounded penuh seperti WhatsApp, tapi lebih formal seperti dokumen resmi yang bisa dipercaya.
- **Hindari:** gradien pelangi, shadow berlebihan, card berlapis-lapis, icon emoji di button, animasi berputar tanpa makna.

**Tampilan yang dilarang keras (tanda desain AI generik):**
- Hero section dengan gradient ungu-biru dan teks "Selamat Datang di Asisten AI Kami"
- Card dengan shadow tinggi di setiap elemen
- Tombol dengan border-radius penuh (pill shape) untuk semua button
- Badge berwarna-warni untuk setiap status
- Padding besar yang membuang ruang layar HP

**Prinsip copy/teks di UI:**
- Semua label dan button ditulis dari sisi pengguna: "Cari bantuan" bukan "Submit Query"
- Error tidak minta maaf tapi memberi arahan: "Koneksi terputus — coba lagi atau hubungi BNPB 117"
- Loading state konkret: "Mencari di knowledge base..." bukan "Loading..."
- Empty state = undangan bertindak: tampilkan contoh pertanyaan, bukan teks "Belum ada pesan"

---

## ═══ FASE 1: INFRASTRUKTUR & SDK ═══

**Tujuan fase ini:** pgvector aktif + Laravel AI SDK terpasang + koneksi Gemini terverifikasi.

### Langkah 1.1 — Audit & Cleanup Project

Sebelum install apapun, jalankan audit:

```bash
# Cek file apa saja yang ada di project saat ini
php artisan route:list
ls app/Http/Controllers/
ls resources/js/Pages/
```

Hapus file-file berikut jika ada (bawaan Laravel starter yang tidak dibutuhkan):
- `app/Http/Controllers/Auth/` — seluruh folder (tidak ada auth di Cekarah)
- `resources/js/Pages/Auth/` — seluruh folder
- `resources/js/Pages/Welcome.jsx` atau `Welcome.vue` — ganti dengan halaman Chat
- Route group `auth` di `routes/web.php`
- Middleware `auth` jika tidak ada halaman yang membutuhkannya

Jangan hapus: `app/Http/Controllers/Controller.php`, middleware bawaan Laravel, konfigurasi Inertia.

### Langkah 1.2 — Aktivasi pgvector

```sql
CREATE EXTENSION IF NOT EXISTS vector;
SELECT extname, extversion FROM pg_extension WHERE extname = 'vector';
```

Jika package OS belum ada:
- Ubuntu/Debian: `sudo apt install postgresql-16-pgvector` (sesuaikan versi PG)
- macOS: `brew install pgvector`

**STOP — verifikasi ekstensi aktif sebelum lanjut.**

### Langkah 1.3 — Install Laravel AI SDK

```bash
composer require laravel/ai
php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"
php artisan migrate
```

Tambahkan ke `.env`:
```env
GEMINI_API_KEY=your_key_here
```

Di `config/ai.php` yang dihasilkan publish, konfigurasi:
```php
'default' => 'gemini',

'providers' => [
    'gemini' => [
        'driver' => 'gemini',
        'key'    => env('GEMINI_API_KEY'),
    ],
],

'models' => [
    'text'       => env('AI_TEXT_MODEL', 'gemini-2.0-flash'),
    'embeddings' => env('AI_EMBEDDING_MODEL', 'text-embedding-004'),
],
```

### Langkah 1.4 — Verifikasi Koneksi

Tambahkan route test sementara di `routes/api.php`:
```php
Route::get('/test-ai', function () {
    // Gunakan sintaks resmi Laravel AI SDK — baca config/ai.php yang dihasilkan
    // untuk memastikan cara panggil yang benar
    return response()->json(['status' => 'ok', 'response' => '...hasil dari Gemini...']);
})->name('test.ai');
```

Jalankan, verifikasi response muncul, lalu **hapus route ini**.

---

**✅ AKHIR FASE 1**
Sebelum lanjut ke Fase 2, pastikan:
- [ ] `SELECT extname FROM pg_extension WHERE extname = 'vector'` mengembalikan 1 baris
- [ ] `php artisan migrate` berhasil dan tabel `agent_conversations` sudah ada
- [ ] `/api/test-ai` mengembalikan respons dari Gemini (lalu route dihapus)
- [ ] File cleanup sudah dilakukan

Sampaikan ke user apa yang sudah selesai dan minta konfirmasi untuk lanjut ke Fase 2.

---

## ═══ FASE 2: DATABASE & KNOWLEDGE BASE ═══

**Tujuan fase ini:** Skema database siap, data knowledge base ter-seed, embeddings ter-index.

### Langkah 2.1 — Migrations

Buat dua migration baru. Jalankan `php artisan make:migration` untuk masing-masing.

**`create_knowledge_documents_table`:**
```php
Schema::create('knowledge_documents', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->string('source_url')->nullable();
    $table->string('source_name');
    $table->string('category');        // 'bantuan' | 'verifikasi' | 'prosedur'
    $table->string('topic')->nullable();
    $table->timestamp('source_date')->nullable();
    $table->timestamp('indexed_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**`create_knowledge_chunks_table`:**
```php
Schema::create('knowledge_chunks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('document_id')
          ->constrained('knowledge_documents')
          ->onDelete('cascade');
    $table->text('chunk_text');
    $table->integer('chunk_index');
    $table->timestamps();
    // Kolom 'embedding' vector(768) ditambah via raw SQL setelah migrate
});
```

**`create_chat_sessions_table`:**
```php
Schema::create('chat_sessions', function (Blueprint $table) {
    $table->id();
    $table->string('token', 64)->unique()->index();
    $table->string('conversation_id')->nullable(); // dari agent_conversations SDK
    $table->string('last_intent', 20)->nullable();
    $table->unsignedTinyInteger('last_confidence_pct')->nullable(); // 0-100
    $table->timestamps();
});
```

Jalankan: `php artisan migrate`

Setelah migrate, tambahkan kolom vector dan index via raw SQL atau `DB::statement` di seeder:
```sql
ALTER TABLE knowledge_chunks ADD COLUMN IF NOT EXISTS embedding vector(768);
CREATE INDEX IF NOT EXISTS knowledge_chunks_embedding_idx
    ON knowledge_chunks USING ivfflat (embedding vector_cosine_ops) WITH (lists = 50);
```

### Langkah 2.2 — Models

Buat tiga model. Ikuti naming convention.

**`app/Models/KnowledgeDocument.php`:**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeDocument extends Model
{
    protected $fillable = [
        'title', 'content', 'source_url', 'source_name',
        'category', 'topic', 'source_date', 'indexed_at', 'is_active',
    ];

    protected $casts = [
        'source_date' => 'datetime',
        'indexed_at'  => 'datetime',
        'is_active'   => 'boolean',
    ];

    public function chunks(): HasMany
    {
        return $this->hasMany(KnowledgeChunk::class, 'document_id');
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    public function scopeNeedsIndexing($query): void
    {
        $query->active()->where(
            fn ($q) => $q->whereNull('indexed_at')
                         ->orWhereColumn('updated_at', '>', 'indexed_at')
        );
    }
}
```

**`app/Models/KnowledgeChunk.php`:**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeChunk extends Model
{
    protected $fillable = ['document_id', 'chunk_text', 'chunk_index', 'embedding'];

    // Cast embedding ke array — cek docs SDK untuk cast yang tepat di pgvector
    protected $casts = ['embedding' => 'array'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(KnowledgeDocument::class, 'document_id');
    }
}
```

**`app/Models/ChatSession.php`:**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'token', 'conversation_id', 'last_intent', 'last_confidence_pct',
    ];
}
```

### Langkah 2.3 — Knowledge Base Seeder

Buat `database/seeders/KnowledgeSeeder.php` dengan **20 dokumen** berikut.
Setiap dokumen WAJIB punya `source_name` dan `source_url`. Data sintetis:
```php
'source_url' => 'sintetis://cekarah-team/nama-dokumen',
'source_name' => 'Data Sintetis Tim Cekarah (berdasarkan SOP [lembaga])',
```

**Kelompok A — Darurat & Evakuasi (6 dokumen):**
1. Prosedur evakuasi banjir step-by-step + hal yang dilarang dilakukan
2. Daftar dokumen yang harus dibawa saat evakuasi (dalam plastik kedap air)
3. Cara lapor ke posko BNPB dan prosedur mendapat logistik darurat
4. Prosedur pencarian orang hilang via Basarnas (115)
5. Fasilitas yang tersedia di pengungsian resmi
6. Direktori kontak darurat resmi: BNPB 117 ext 7, Basarnas 115, PMI 021-7992325, Kemensos 1500771

**Kelompok B — Bantuan Sosial Pasca Bencana (7 dokumen):**
1. Cara daftar bantuan darurat via aplikasi Cek Bansos — langkah per langkah + screenshot flow
2. Cara daftar offline lewat kantor desa/kelurahan
3. Syarat penerima DTSEN 2026 (WNI ber-KTP, desil 1-4, bukan ASN/TNI aktif)
4. Cara cek status penerima di cekbansos.kemensos.go.id
5. Alur pendaftaran PKH untuk korban bencana + dokumen yang dibutuhkan
6. Alur BPNT (Bantuan Pangan Non-Tunai) + mekanisme penyaluran
7. Cara usul sanggah desil DTSEN jika kondisi ekonomi tidak sesuai data

**Kelompok C — Verifikasi Hoaks Bencana (7 dokumen):**
1. Pola hoaks "air laut naik/tsunami palsu" + cara verifikasi ke BMKG
2. Pola hoaks "gempa susulan berbahaya" lewat pesan berantai
3. Pola hoaks nomor rekening donasi palsu saat bencana
4. Cara bedakan pengumuman BNPB/BMKG resmi vs hoaks
5. Saluran resmi verifikasi bencana: bmkg.go.id, bnpb.go.id, aplikasi Info BMKG, TVRI
6. Cara lapor konten hoaks ke aduankonten.id (Kemkomdigi)
7. Kasus nyata: hoaks "air laut naik" Pidie Jaya Aceh, Desember 2025 — kronologi, dampak, klarifikasi resmi

Daftarkan di `DatabaseSeeder.php`. Jalankan: `php artisan db:seed --class=KnowledgeSeeder`

### Langkah 2.4 — Embedding Service & Command

**`app/Services/KnowledgeIndexer.php`:**
```php
namespace App\Services;

use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Facades\Embeddings; // verifikasi namespace dari docs SDK

class KnowledgeIndexer
{
    public function chunkText(string $text, int $size = 400, int $overlap = 50): array
    {
        $words  = preg_split('/\s+/', trim($text));
        $chunks = [];
        $i      = 0;
        while ($i < count($words)) {
            $chunks[] = implode(' ', array_slice($words, $i, $size));
            $i       += max(1, $size - $overlap);
        }
        return array_filter($chunks);
    }

    public function index(KnowledgeDocument $document): void
    {
        KnowledgeChunk::where('document_id', $document->id)->delete();

        foreach ($this->chunkText($document->content) as $i => $chunk) {
            // Gunakan Embeddings facade dari Laravel AI SDK
            // Cek docs laravel.com/docs/13.x/ai-sdk#embeddings untuk sintaks tepat
            $vector = Embeddings::embed($chunk)->vector;

            DB::statement(
                "INSERT INTO knowledge_chunks
                    (document_id, chunk_text, chunk_index, embedding, created_at, updated_at)
                 VALUES (?, ?, ?, ?::vector, NOW(), NOW())",
                [$document->id, $chunk, $i, '[' . implode(',', $vector) . ']']
            );
        }

        $document->update(['indexed_at' => now()]);
    }
}
```

**Artisan Command `php artisan make:command IndexKnowledge` → signature `knowledge:index`:**
```php
public function handle(KnowledgeIndexer $indexer): void
{
    $documents = KnowledgeDocument::needsIndexing()->get();

    if ($documents->isEmpty()) {
        $this->info('Semua dokumen sudah ter-index.');
        return;
    }

    $bar = $this->output->createProgressBar($documents->count());
    $bar->start();

    foreach ($documents as $doc) {
        try {
            $indexer->index($doc);
            $bar->advance();
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error("Gagal [{$doc->id}] {$doc->title}: {$e->getMessage()}");
        }
    }

    $bar->finish();
    $this->newLine();
    $this->info('Selesai.');
}
```

Jalankan setelah seeder: `php artisan knowledge:index`

---

**✅ AKHIR FASE 2**
Sebelum lanjut, verifikasi:
- [ ] `php artisan migrate` sukses, semua tabel ada
- [ ] `php artisan db:seed --class=KnowledgeSeeder` sukses (20 dokumen)
- [ ] `php artisan knowledge:index` berjalan tanpa error
- [ ] Query `SELECT COUNT(*) FROM knowledge_chunks WHERE embedding IS NOT NULL` > 0

Sampaikan ke user apa yang sudah selesai dan minta konfirmasi untuk lanjut ke Fase 3.

---

## ═══ FASE 3: AGENT & TOOLS ═══

**Tujuan fase ini:** CekarahAgent siap, semua tools berfungsi, pipeline AI teruji.

### Langkah 3.1 — Scaffold

```bash
php artisan make:agent CekarahAgent
php artisan make:tool ClassifyIntentTool
php artisan make:tool GetEscalationContactsTool
php artisan make:tool CheckInformationFreshnessTool
```

### Langkah 3.2 — CekarahAgent

`app/Ai/Agents/CekarahAgent.php`:

```php
namespace App\Ai\Agents;

use App\Ai\Tools\ClassifyIntentTool;
use App\Ai\Tools\CheckInformationFreshnessTool;
use App\Ai\Tools\GetEscalationContactsTool;
use App\Models\KnowledgeChunk;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Laravel\Ai\Tools\SimilaritySearch;

class CekarahAgent implements Agent, Conversational, HasTools, HasStructuredOutput
{
    use Promptable, RemembersConversations;

    public function instructions(): string
    {
        return <<<INSTRUCTIONS
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

POSISIMU: AI berperan memandu, manusia petugas yang memutuskan.
INSTRUCTIONS;
    }

    public function tools(): iterable
    {
        return [
            new ClassifyIntentTool,

            SimilaritySearch::usingModel(KnowledgeChunk::class, 'embedding')
                ->withDescription(
                    'Cari informasi dari knowledge base resmi Cekarah (BNPB, Kemensos, PMI). ' .
                    'Gunakan untuk mencari prosedur bantuan, kontak darurat, ' .
                    'atau data untuk memverifikasi klaim yang beredar di masyarakat.'
                ),

            new CheckInformationFreshnessTool,
            new GetEscalationContactsTool,
        ];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'reply' => $schema->string()->required(),
            'intent' => $schema->string()
                ->enum(['navigasi', 'verifikasi', 'unclear'])
                ->required(),
            'confidence' => $schema->number()->min(0)->max(1)->required(),
            'escalation_suggested' => $schema->boolean()->required(),
            'escalation_contacts' => $schema->array()->items(
                $schema->object(fn ($s) => [
                    'name'      => $s->string()->required(),
                    'contact'   => $s->string()->required(),
                    'available' => $s->string()->required(),
                ])
            ),
            'sources_used' => $schema->array()->items(
                $schema->object(fn ($s) => [
                    'title'       => $s->string()->required(),
                    'source_name' => $s->string()->required(),
                    'is_stale'    => $s->boolean()->required(),
                ])
            ),
        ];
    }
}
```

### Langkah 3.3 — Tools

**`ClassifyIntentTool`** — schema: `{ message: string }`

Handle: keyword matching sederhana untuk navigasi vs verifikasi vs unclear.
Return JSON: `{ intent, signal_strength, suggestion }`.

**`CheckInformationFreshnessTool`** — schema: `{ source_names: string[] }`

Handle: query `KnowledgeDocument` by source_name, cek `source_date` vs `now()->subMonths(6)`.
Return JSON: array tiap sumber dengan `is_stale` dan `warning` jika stale.

**`GetEscalationContactsTool`** — schema: `{ intent: enum('navigasi','verifikasi','general'), reason: string }`

Handle: return kontak berdasarkan intent:
- navigasi: BNPB 117 ext 7, Basarnas 115, PMI 021-7992325, Kemensos 1500771
- verifikasi: bnpb.go.id, bmkg.go.id, aplikasi Info BMKG, aduankonten.id

### Langkah 3.4 — Test Agent Standalone

Buat route test sementara:
```php
Route::get('/test-agent', function () {
    $response = (new \App\Ai\Agents\CekarahAgent)->prompt(
        'Rumah saya kena banjir, butuh bantuan darurat'
    );
    return response()->json($response->toArray());
});
```

Verifikasi structured output kembali dengan semua field. Hapus route setelah berhasil.

---

**✅ AKHIR FASE 3**
Verifikasi:
- [ ] `/test-agent` mengembalikan JSON dengan field: reply, intent, confidence, escalation_suggested, sources_used
- [ ] `sources_used` tidak kosong (berarti RAG bekerja)
- [ ] Route test sudah dihapus

Sampaikan ke user dan minta konfirmasi untuk lanjut ke Fase 4.

---

## ═══ FASE 4: API LAYER (RESTful) ═══

**Tujuan fase ini:** Endpoint API bersih dan siap dikonsumsi frontend.

### Struktur Endpoint

```
POST   /api/chat-sessions          → ChatSessionController@store
POST   /api/chat-sessions/{token}/messages  → MessageController@store
GET    /api/chat-sessions/{token}/messages  → MessageController@index
```

### ChatSessionController

`app/Http/Controllers/Api/ChatSessionController.php`

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Support\Str;

class ChatSessionController extends Controller
{
    public function store(): \Illuminate\Http\JsonResponse
    {
        $session = ChatSession::create([
            'token' => Str::random(40),
        ]);

        return response()->json([
            'token'      => $session->token,
            'created_at' => $session->created_at->toIso8601String(),
        ], 201);
    }
}
```

### MessageController

`app/Http/Controllers/Api/MessageController.php`

```php
namespace App\Http\Controllers\Api;

use App\Ai\Agents\CekarahAgent;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, string $token): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $session = ChatSession::where('token', $token)->firstOrFail();

        try {
            $agent = new CekarahAgent;

            // Jika ada conversation_id yang tersimpan, lanjutkan sesi
            // Jika belum, mulai baru
            // ⚠️ Catatan: cek docs SDK apakah continue() bisa tanpa User model
            // Jika tidak bisa, load history manual dari agent_conversation_messages
            // dan inject lewat custom messages() method tanpa RemembersConversations
            if ($session->conversation_id) {
                $response = $agent->continue($session->conversation_id)
                                  ->prompt($request->content);
            } else {
                $response = $agent->prompt($request->content);
                $session->update(['conversation_id' => $response->conversationId]);
            }

            $data = $response->toArray();

            $session->update([
                'last_intent'          => $data['intent'] ?? null,
                'last_confidence_pct'  => isset($data['confidence'])
                                            ? (int) round($data['confidence'] * 100)
                                            : null,
            ]);

            return response()->json([
                'reply'                => $data['reply'],
                'intent'               => $data['intent'],
                'confidence'           => $data['confidence'],
                'escalation_suggested' => $data['escalation_suggested'],
                'escalation_contacts'  => $data['escalation_contacts'] ?? null,
                'sources_used'         => $data['sources_used'] ?? [],
            ], 201);

        } catch (\Throwable $e) {
            \Log::error('cekarah.message.error', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'reply'                => "Koneksi ke sistem AI terputus. " .
                                         "Untuk situasi darurat hubungi langsung:\n" .
                                         "• BNPB: 117 ext 7 (24 jam)\n" .
                                         "• Basarnas: 115",
                'intent'               => 'error',
                'confidence'           => 0,
                'escalation_suggested' => true,
                'escalation_contacts'  => [
                    ['name' => 'BNPB',     'contact' => '117 ext 7', 'available' => '24 jam'],
                    ['name' => 'Basarnas', 'contact' => '115',        'available' => '24 jam'],
                ],
                'sources_used' => [],
            ], 200); // tetap 200 agar frontend tidak crash
        }
    }

    public function index(string $token): \Illuminate\Http\JsonResponse
    {
        // Tampilkan status session saja — history detail ada di agent_conversation_messages
        $session = ChatSession::where('token', $token)->firstOrFail();

        return response()->json([
            'token'               => $session->token,
            'last_intent'         => $session->last_intent,
            'last_confidence_pct' => $session->last_confidence_pct,
        ]);
    }
}
```

### Routes

Di `routes/api.php` — **ganti semua konten dengan ini** (pastikan tidak ada route lama yang tertinggal):

```php
use App\Http\Controllers\Api\ChatSessionController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:20,1')->group(function () {
    Route::post('chat-sessions', [ChatSessionController::class, 'store'])
         ->name('chat-sessions.store');

    Route::post('chat-sessions/{token}/messages', [MessageController::class, 'store'])
         ->name('chat-sessions.messages.store');

    Route::get('chat-sessions/{token}/messages', [MessageController::class, 'index'])
         ->name('chat-sessions.messages.index');
});
```

---

**✅ AKHIR FASE 4**
Verifikasi via curl atau Postman:
- [ ] `POST /api/chat-sessions` → 201 dengan `token`
- [ ] `POST /api/chat-sessions/{token}/messages` dengan `content` → 201 dengan `reply`
- [ ] `GET /api/chat-sessions/{token}/messages` → 200

Sampaikan ke user dan minta konfirmasi untuk lanjut ke Fase 5.

---

## ═══ FASE 5: FRONTEND REACT ═══

**Tujuan fase ini:** UI chat yang modern, profesional, dan mobile-first.

### Langkah 5.1 — Cleanup Pages

Hapus semua page yang tidak diperlukan (`Welcome`, `Auth/*`, dll) jika belum dihapus di Fase 1.

### Langkah 5.2 — Struktur File

```
resources/js/
├── Pages/
│   ├── Landing.jsx     ← halaman landing (route '/')
│   ├── Chat.jsx        ← halaman chat (route '/chat')
│   └── About.jsx       ← halaman tentang sistem (route '/about')
├── Components/
│   ├── MessageBubble.jsx
│   ├── ConfidenceBar.jsx
│   ├── EscalationPanel.jsx
│   ├── SourceCard.jsx
│   └── TypingIndicator.jsx
└── hooks/
    └── useChat.js      ← semua logic state & API call
```

### Langkah 5.3 — Custom Hook `useChat.js`

```javascript
// resources/js/hooks/useChat.js
import { useState, useEffect, useRef } from 'react';
import axios from 'axios';

export function useChat() {
    const [messages, setMessages]     = useState([]);
    const [token, setToken]           = useState(null);
    const [isLoading, setIsLoading]   = useState(false);
    const [error, setError]           = useState(null);
    const bottomRef                   = useRef(null);

    useEffect(() => {
        axios.post('/api/chat-sessions')
             .then(r => setToken(r.data.token))
             .catch(() => setError('Gagal memulai sesi. Muat ulang halaman.'));
    }, []);

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, isLoading]);

    const send = async (content) => {
        if (!content.trim() || isLoading || !token) return;

        setMessages(prev => [...prev, { role: 'user', content }]);
        setIsLoading(true);
        setError(null);

        try {
            const { data } = await axios.post(
                `/api/chat-sessions/${token}/messages`,
                { content }
            );
            setMessages(prev => [...prev, { role: 'assistant', ...data }]);
        } catch {
            setError('Koneksi terputus. Untuk darurat hubungi BNPB 117 ext 7.');
        } finally {
            setIsLoading(false);
        }
    };

    return { messages, token, isLoading, error, send, bottomRef };
}
```

### Langkah 5.4 — Komponen

**`MessageBubble.jsx`** — menerima props: `role`, `content`, `intent`, `confidence`, `sources_used`, `escalation_suggested`, `escalation_contacts`

User bubble: align kanan, background `#3B82F6`, text putih, border-radius 16px 16px 4px 16px.
Assistant bubble: align kiri, background putih, border-left `3px solid #3B82F6`, border-radius 4px 16px 16px 16px. Padding 16px. Shadow sangat halus `shadow-sm`.

Di bawah teks assistant:
- Intent badge (teks kecil, tanpa background mencolok — cukup teks berwarna dengan dot indicator)
- `ConfidenceBar`
- `SourceCard` untuk tiap source
- `EscalationPanel` jika `escalation_suggested`

**`ConfidenceBar.jsx`** — bar tipis 4px tinggi, tidak ada label numerik jika pct > 70 (terlalu noisy). Tampilkan label hanya jika < 70 dengan teks "Verifikasi ke sumber resmi disarankan".

```jsx
const ConfidenceBar = ({ confidence }) => {
    const pct = Math.round((confidence ?? 0) * 100);
    const color = pct >= 70 ? '#22C55E' : pct >= 50 ? '#F59E0B' : '#EF4444';
    const showWarning = pct < 70;

    return (
        <div className="mt-3">
            <div className="w-full bg-slate-100 rounded-full" style={{ height: '3px' }}>
                <div
                    className="rounded-full transition-all duration-500"
                    style={{ width: `${pct}%`, height: '3px', backgroundColor: color }}
                />
            </div>
            {showWarning && (
                <p className="text-xs text-amber-600 mt-1">
                    Verifikasi ke sumber resmi disarankan
                </p>
            )}
        </div>
    );
};
```

**`EscalationPanel.jsx`** — panel subtle, bukan merah mencolok. Background `#FEF2F2`, border `1px solid #FECACA`, teks `#B91C1C`. Tampilkan list kontak. Judul: "Hubungi petugas resmi untuk konfirmasi:"

**`SourceCard.jsx`** — satu baris per source. Tampilkan `source_name`. Jika `is_stale`, tambahkan ikon ⚠️ kecil dan teks "Mungkin sudah diperbarui". Jika ada `source_url` yang bukan sintetis, buat dapat diklik.

**`TypingIndicator.jsx`** — tiga titik dengan animasi bounce berurutan (delay 0, 150ms, 300ms). Background dan padding sama dengan assistant bubble.

### Langkah 5.5 — Halaman `Chat.jsx`

```jsx
// Struktur utama — implementasi detail menyesuaikan komponen di atas

const EXAMPLE_QUESTIONS = [
    'Rumah saya kena banjir, butuh bantuan darurat',
    'Benarkah ada peringatan tsunami di Aceh malam ini?',
    'Cara daftar bantuan sosial sebagai korban bencana',
    'Nomor darurat bencana yang bisa dihubungi',
];

export default function Chat() {
    const { messages, isLoading, error, send, bottomRef } = useChat();
    const [input, setInput] = useState('');

    const handleSend = () => { send(input); setInput(''); };
    const handleKey  = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(); }
    };

    return (
        <div className="flex flex-col h-screen bg-slate-50">

            {/* Header — dark, trustworthy */}
            <header className="bg-slate-900 text-white px-4 py-3 flex items-center justify-between">
                <div>
                    <h1 className="font-semibold text-lg tracking-tight">Cekarah</h1>
                    <p className="text-slate-400 text-xs">Navigator bantuan & verifikasi informasi</p>
                </div>
                <span className="text-xs text-slate-500 border border-slate-700 rounded px-2 py-1">
                    Navigator awal — bukan otoritas final
                </span>
            </header>

            {/* Chat area */}
            <main className="flex-1 overflow-y-auto px-4 py-6 space-y-4 max-w-2xl mx-auto w-full">

                {/* Empty state */}
                {messages.length === 0 && !isLoading && (
                    <div className="text-center pt-8">
                        <p className="text-slate-500 text-sm mb-6">
                            Tanyakan sesuatu atau pilih contoh di bawah
                        </p>
                        <div className="grid grid-cols-1 gap-2">
                            {EXAMPLE_QUESTIONS.map(q => (
                                <button
                                    key={q}
                                    onClick={() => send(q)}
                                    className="text-left px-4 py-3 rounded-lg border border-slate-200
                                               bg-white text-slate-700 text-sm hover:border-blue-400
                                               hover:bg-blue-50 transition-colors"
                                >
                                    {q}
                                </button>
                            ))}
                        </div>
                    </div>
                )}

                {messages.map((msg, i) => (
                    <MessageBubble key={i} {...msg} />
                ))}

                {isLoading && <TypingIndicator />}
                {error && (
                    <p className="text-red-600 text-sm text-center py-2">{error}</p>
                )}

                <div ref={bottomRef} />
            </main>

            {/* Input area */}
            <div className="border-t border-slate-200 bg-white px-4 py-3 max-w-2xl mx-auto w-full">
                <div className="flex gap-2">
                    <textarea
                        value={input}
                        onChange={e => setInput(e.target.value)}
                        onKeyDown={handleKey}
                        placeholder="Tulis pertanyaan atau ceritakan situasimu..."
                        rows={2}
                        disabled={isLoading}
                        className="flex-1 resize-none border border-slate-200 rounded-lg px-3 py-2
                                   text-sm text-slate-800 focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:border-transparent disabled:opacity-50"
                    />
                    <button
                        onClick={handleSend}
                        disabled={isLoading || !input.trim()}
                        className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium
                                   hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed
                                   transition-colors self-end"
                    >
                        Kirim
                    </button>
                </div>
                <p className="text-slate-400 text-xs mt-2">
                    Enter untuk kirim · Shift+Enter untuk baris baru ·{' '}
                    <a href="/about" className="underline hover:text-slate-600">
                        Cara kerja sistem
                    </a>
                </p>
            </div>

        </div>
    );
}
```

### Langkah 5.6 — Halaman `About.jsx`

Halaman statis dengan konten:
- Apa itu Cekarah dan untuk siapa (1 paragraf, tidak berlebihan)
- Cara kerja dalam bahasa sederhana: "AI mencari di dokumen resmi dulu, bukan mengarang"
- Daftar semua sumber data beserta URL
- Batasan sistem: bisa salah, data bisa usang, bukan pengganti petugas
- Cara melaporkan jawaban yang salah

Desain: konsisten dengan Chat.jsx — header `bg-slate-900`, konten di container `max-w-2xl`, typography clean. Tidak ada ilustrasi.

### Langkah 5.7 — Halaman `Landing.jsx` (Satu Halaman Panjang)

**Identitas visual landing page ini spesifik untuk Cekarah — bukan template AI generik.**

Cekarah adalah alat darurat, bukan startup. Referensi desain: sistem peringatan resmi, bukan
SaaS marketing page. Karakternya: presisi dan berat, bukan dekoratif.

**Token desain yang WAJIB diikuti:**
```
Background hero  : #0A0F1E  (navy sangat gelap)
Aksen darurat    : #E63946  (merah sinyal — HANYA untuk angka data & CTA utama)
Background alt   : #F0F4FF  (biru sangat pucat untuk section alternating)
Teks hero        : #F8FAFC  (putih keabu-abuan)
Heading weight   : font-black dengan letter-spacing -0.03em (ketat)
Body             : font-normal, leading-relaxed
Border/divider   : 1px solid rgba(255,255,255,0.08) di section gelap
```

**Yang DILARANG keras — tanda desain AI generik:**
- Gradient text (background-clip text) pada heading
- Gradient background ungu-biru
- Card dengan shadow tinggi bertumpuk-tumpuk
- Ilustrasi robot atau icon bintang berkilauan
- Tiga kolom fitur simetris dengan icon emoji besar di atas
- Section "Mitra Kami" atau "Dipercaya oleh X pengguna"
- Animasi parallax yang berlebihan
- Tombol CTA pill shape berlapis gradient

**Struktur halaman — diimplementasikan dalam urutan ini:**

---

**SECTION 1 — HERO** (`bg-[#0A0F1E]`, full viewport height)

Layout asimetris: dua kolom di desktop, satu kolom di mobile.

Kolom kiri (60%):
```
Label kecil uppercase dengan tracking lebar:
  "EKSHIBISI KA — LKS DIKMEN NASIONAL 2026"
  (text-xs, text-slate-400, letter-spacing: 0.15em)

Heading utama — dua baris, font-black, text-5xl md:text-7xl, letter-spacing -0.03em:
  "48 jam pertama"
  "yang menentukan."
  (text-white, line-height 1.0)

Sub-heading — satu kalimat, tidak lebih:
  "Cekarah membantu warga menemukan bantuan resmi dan
   memverifikasi informasi dalam situasi darurat bencana."
  (text-slate-400, text-lg, mt-6, max-w-md)

CTA button:
  Primary: "Mulai sekarang →"
    (bg-[#E63946], text-white, px-8 py-4, font-semibold,
     hover:bg-red-700, transition, NO border-radius penuh — gunakan rounded-lg)
  Secondary: "Pelajari cara kerja" (text-slate-400, underline, ml-6)
```

Kolom kanan (40%) — hanya di desktop, tersembunyi di mobile:
```
Ticker angka darurat — tiga baris, masing-masing:
  Angka besar: font-black text-6xl text-white
  Label: text-sm text-slate-400 mt-1

Data yang ditampilkan (dari bukti nyata di project brief):
  1.199    → "korban meninggal, bencana Sumatera Nov 2025"
  114.200  → "warga mengungsi dalam 48 jam pertama"
  1.890    → "konten hoaks teridentifikasi (Okt 2024–Des 2025)"

Visual: angka-angka ini TIDAK dalam card, TIDAK ada shadow.
Hanya teks di atas background gelap dengan garis pemisah
1px solid rgba(255,255,255,0.1) antar baris.
Efek: angka berwarna putih, label berwarna slate-400.
```

Separator bawah: garis horizontal `border-t border-slate-800` sebelum section berikutnya.

---

**SECTION 2 — MASALAH** (`bg-white`, py-24)

Judul section: bukan "Masalah yang Kami Selesaikan" — tapi kalimat langsung:
```
"Dalam 48 jam pertama krisis, warga menghadapi dua masalah sekaligus."
(text-3xl font-bold text-slate-900, max-w-2xl)
```

Dua kolom masalah — layout asimetris, bukan card identik:

Kiri — masalah 1 (border-l-4 border-slate-900, pl-6):
```
Nomor: "01" (text-xs text-slate-400 font-mono mb-2)
Judul: "Tidak tahu harus kemana"
       (text-xl font-bold text-slate-900)
Deskripsi: "Informasi bantuan tersebar di puluhan kanal.
            Warga kebingungan menentukan lembaga yang tepat,
            dokumen yang dibutuhkan, dan langkah pertama."
```

Kanan — masalah 2 (border-l-4 border-[#E63946], pl-6):
```
Nomor: "02" (text-xs text-slate-400 font-mono mb-2)
Judul: "Tidak bisa bedakan mana yang benar"
Deskripsi: "Hoaks bencana menyebar lebih cepat dari bantuan.
            Kasus Pidie Jaya Desember 2025: pesan 'air laut naik'
            memicu evakuasi panik, mengacaukan operasi SAR."
```

---

**SECTION 3 — SOLUSI / CARA KERJA** (`bg-[#F0F4FF]`, py-24)

Label: "CARA KERJA" (text-xs uppercase tracking-widest text-slate-400)
Judul: "Satu kotak chat. Dua kemampuan."
       (text-4xl font-black text-slate-900 letter-spacing -0.02em, mt-2)

Flow diagram teks — tiga langkah horizontal di desktop, vertikal di mobile.
Bukan card floating, tapi step sederhana dengan connector line:

```
[Input bebas] ──→ [Deteksi kebutuhan] ──→ [Navigasi atau Verifikasi]
"Tulis situasimu     "Agent AI menentukan    "Langkah konkret +
 dengan kata-katamu   jalur yang tepat        sumber resmi +
 sendiri"             secara otomatis"        kontak petugas"
```

Implementasi: flex row di desktop. Connector `──→` adalah `<div>` dengan
`border-t-2 border-dashed border-slate-300` ditambah arrow. Step text di bawahnya.
Nomor step: `01`, `02`, `03` dalam `font-mono text-xs text-slate-400`.

Di bawah diagram, dua kolom fitur utama (bukan tiga — asimetris):

Kiri — "Navigasi Bantuan":
```
Heading: "Temukan bantuan yang tepat"
List item tanpa bullet bundar — gunakan tanda "→":
  → Prosedur evakuasi banjir step-by-step
  → Cara daftar bantuan sosial darurat (PKH, BPNT)
  → Kontak resmi BNPB, Basarnas, PMI, Kemensos
```

Kanan — "Verifikasi Klaim":
```
Heading: "Cek sebelum percaya"
  → Cross-check klaim dengan sumber BNPB & BMKG
  → Penjelasan dengan alasan, bukan vonis "hoaks"
  → Rujukan langsung ke sumber resmi
```

---

**SECTION 4 — DATA STATISTIK** (`bg-slate-900`, py-24)

Tiga angka besar di atas background gelap — ditampilkan horizontal.
Ini bukan infografik warna-warni. Angka putih, label slate-400.

```
390          1.890        5
laporan      konten       orang
kesejahteraan hoaks        ditangkap
sosial 2024  teridentifikasi karena hoaks
(Ombudsman)  (Kemkomdigi)   bencana Aceh
```

Di bawah angka: satu kalimat konteks kecil:
`"Data ini adalah alasan Cekarah dibangun."`
(text-slate-500, text-sm, text-center, mt-8)

---

**SECTION 5 — RESPONSIBLE AI** (`bg-white`, py-24)

Ini BUKAN section "Kenapa Pilih Kami". Ini adalah pernyataan posisi yang jujur.

Label: "RESPONSIBLE AI"
Judul: "AI ini punya batas. Sengaja."

Dua kolom teks tanpa card:

Kiri:
```
"Cekarah adalah navigator awal, bukan otoritas final.
 Setiap respons menyertakan rujukan sumber resmi dan
 kontak petugas manusia. Jika tidak yakin, sistem
 mendorong user untuk menghubungi petugas langsung —
 bukan mencoba menjawab dengan tebakan."
```

Kanan — tiga pernyataan ringkas dengan format "label: nilai":
```
Data sumber    : Open data publik & sintetis — tidak ada data pribadi
Transparansi   : Setiap jawaban menampilkan sumber dan tingkat keyakinan AI
Eskalasi       : Kontak petugas resmi tersedia di setiap respons
```

---

**SECTION 6 — CTA PENUTUP** (`bg-[#0A0F1E]`, py-32)

Sederhana. Dua elemen saja:

```
Heading: "Coba sekarang."
         (text-6xl font-black text-white letter-spacing -0.03em)

CTA: tombol besar "Mulai percakapan →"
     (bg-[#E63946], text-white, text-lg font-semibold,
      px-10 py-5, rounded-lg, hover:bg-red-700)
```

Di bawah tombol: teks kecil `"Tanpa akun. Tanpa data pribadi."` (text-slate-500, text-sm, mt-4)

---

**FOOTER** (`bg-[#060B14]`, py-8)

Tiga kolom:
```
Kiri  : "Cekarah" + "LKS Dikmen Nasional 2026"
Tengah: link → /chat, /about
Kanan : "Dibuat dengan Laravel 13 + Gemini AI"
        (text-slate-600 text-xs)
```

Garis atas: `border-t border-slate-800`

---

**Implementasi notes untuk Claude Code:**
- Semua section adalah `<section>` dengan `id` yang bisa di-scroll ke via anchor link
- Navbar sticky di atas: logo kiri, dua link kanan ("Tentang" + button "Buka Chat")
  Background navbar: `bg-[#0A0F1E]/90 backdrop-blur-sm` agar transparan saat di hero
- Smooth scroll: `html { scroll-behavior: smooth; }`
- Tidak ada animasi scroll-triggered yang kompleks — hanya `transition-colors` pada hover state
- Gunakan `Link` dari Inertia untuk navigasi internal (`/chat`, `/about`)
- Angka statistik di section hero dan data section: wrap dalam `<span>` dengan class
  `tabular-nums` agar angka tidak bergeser width saat mount

### Langkah 5.7 — Routes Web

Di `routes/web.php` — **ganti konten dengan ini:**
```php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/',      fn () => Inertia::render('Landing'))->name('home');
Route::get('/chat',  fn () => Inertia::render('Chat'))->name('chat');
Route::get('/about', fn () => Inertia::render('About'))->name('about');
```

---

**✅ AKHIR FASE 5**
Verifikasi:
- [ ] `npm run dev` — halaman Landing (`/`), Chat (`/chat`), About (`/about`) tampil tanpa error
- [ ] Landing page: hero heading besar + ticker angka darurat + semua 6 section lengkap
- [ ] Landing page: tidak ada gradient text, tidak ada card shadow tinggi, tidak ada ilustrasi robot
- [ ] Navbar sticky berfungsi, link ke `/chat` dan `/about` berjalan
- [ ] Chat page: kirim pesan test → respons muncul dengan bubble yang benar
- [ ] Tampilan di mobile 375px bersih untuk semua halaman (gunakan DevTools)
- [ ] Tidak ada file/komponen orphan yang tersisa

Sampaikan ke user dan minta konfirmasi untuk lanjut ke Fase 6.

---

## ═══ FASE 6: POLISH & RESPONSIBLE AI ═══

**Tujuan fase ini:** Aplikasi siap demo — disclaimer lengkap, error handling sempurna, tidak ada kebocoran info teknis ke user.

### Langkah 6.1 — Responsible AI Checks

Verifikasi bahwa hal-hal berikut sudah ada di setiap respons AI:
1. Kontak resmi minimal satu — jika tidak ada di `reply`, tambahkan di footer bubble
2. Disclaimer `is_stale` muncul jika ada sumber yang sudah > 6 bulan
3. `EscalationPanel` muncul jika `escalation_suggested = true` atau `confidence < 0.6`
4. Teks di UI tidak pernah menyatakan AI sebagai otoritas final

### Langkah 6.2 — Error Handling Audit

Cek semua path error:
- Gemini API timeout → pesan sopan + kontak darurat
- pgvector tidak menemukan hasil → respons dengan kontak langsung
- Session tidak ditemukan → 404 JSON yang informatif, bukan stack trace
- Input kosong atau terlalu panjang → validasi yang jelas di UI sebelum kirim

### Langkah 6.3 — `DISCLOSURE.md`

Buat di root project. Isi wajib:
```markdown
# Disclosure — Cekarah AI

## Model & Provider
- Text generation: Gemini 2.0 Flash (Google)
- Embeddings: text-embedding-004 (Google)
- Provider: Gemini API

## Framework & Package Utama
- Laravel [versi]
- laravel/ai [versi]
- Inertia.js [versi]
- React [versi]
- pgvector [versi]

## Sumber Data Knowledge Base
| Nama Sumber | URL | Kategori | Jenis Data |
|-------------|-----|----------|------------|
| [isi sesuai seeder] | ... | ... | Sintetis/Publik |

## Tools yang Digunakan Selama Development
- Claude Code (Anthropic) — Sonnet 4.6
- [tools lain jika ada]

## Catatan Responsible AI
- Seluruh data: open data publik atau sintetis — tidak ada data pribadi
- AI berperan sebagai navigator awal, bukan otoritas keputusan final
- Setiap respons menyertakan rujukan sumber dan kontak petugas manusia
```

### Langkah 6.4 — Test 4 Skenario Demo Wajib

Jalankan keempat skenario ini di browser dan verifikasi hasilnya:

| # | Input | ✅ Harus ada dalam respons | ❌ Tidak boleh ada |
|---|-------|--------------------------|-------------------|
| 1 | "Rumah kena banjir, butuh bantuan makanan dan tempat tinggal" | Langkah konkret + BNPB/PMI + cara daftar bantuan | Respons generik tanpa langkah |
| 2 | "Teman WA: air laut Aceh akan naik malam ini, benarkah?" | Penjelasan peringatan resmi hanya dari BMKG + cara cek | Kata "HOAKS" tanpa penjelasan |
| 3 | "Cara dapat PKH sebagai korban banjir?" | Langkah daftar DTSEN + cekbansos.kemensos.go.id + 1500771 | Tidak ada langkah konkret |
| 4 | "Saya butuh bantuan" | Pertanyaan klarifikasi dari AI | Jawaban dengan asumsi |

---

**✅ AKHIR FASE 6 — SEMUA FASE SELESAI**

---

## LANGKAH SELANJUTNYA (disampaikan setelah Fase 6 selesai)

Setelah semua fase selesai, sampaikan kepada user hal-hal berikut secara jelas:

### Yang perlu dilakukan sebelum submission (3 Juli 2026):

**Wajib:**
1. Rekam video pitch & demo (2–5 menit) yang menunjukkan: masalah → solusi → demo live → Responsible AI
2. Tulis Problem Canvas (max 4 halaman) berdasarkan data di DISCLOSURE.md dan sumber yang sudah terverifikasi
3. Submit ke https://forms.office.com/r/6phQnAceYA sebelum 3 Juli 2026 dengan: proposal PDF + link video + link aplikasi/repo

**Persiapan teknis untuk demo live di Jakarta (30 Juli):**
- Pastikan aplikasi bisa diakses via internet (deploy ke hosting atau jalankan ngrok)
- Siapkan 4 skenario demo yang sudah ditest di atas — latih sampai lancar 10 menit
- Bawa laptop + charger, jangan andalkan internet venue sepenuhnya (siapkan hotspot HP)

### Yang bisa dikembangkan jika ada waktu lebih:

- **Streaming response** — ganti `prompt()` dengan `stream()` dari SDK agar teks muncul bertahap seperti ChatGPT, tidak menunggu respons selesai penuh
- **Sumber data real** — tambahkan dokumen dari data.go.id (dataset fact-checking Kemkominfo) ke knowledge base
- **Admin panel sederhana** — halaman `/admin/knowledge` untuk melihat dokumen yang sudah ter-index (berguna saat demo juri bertanya "datanya dari mana?")
- **Halaman status koneksi** — indikator kecil di UI apakah sistem online atau offline

### Untuk sesi tanya jawab juri — tim harus bisa menjelaskan:
- Apa itu RAG dan kenapa tidak cukup langsung tanya ke Gemini saja
- Bagaimana agent memutuskan kapan pakai tool mana (tool-calling loop)
- Kenapa pgvector dan bukan cari teks biasa dengan LIKE
- Apa risikonya jika AI memberikan informasi yang salah dalam situasi darurat (dan mitigasi yang sudah dibangun)