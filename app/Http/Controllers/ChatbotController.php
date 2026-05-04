<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPdfJob;
use App\Models\RagDocument;
use App\Services\EmbeddingService;
use App\Services\RagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ChatbotController extends Controller
{
    public function __construct(
        private readonly RagService $rag,
        private readonly EmbeddingService $embedding
    ) {}

    // ─────────────────────────────────────────────────────────
    // POST /chatbot/chat
    // Endpoint utama: terima pesan user → retrieve → generate
    // Response: Server-Sent Events (text/event-stream)
    // ─────────────────────────────────────────────────────────
    public function chat(Request $request): Response
    {
        $request->validate([
            'message'    => ['required', 'string', 'max:500'],
            'session_id' => ['required', 'string', 'max:64'],
        ]);

        $query     = trim($request->input('message'));
        // $sessionId = $request->input('session_id');

        // Ambil chunks paling relevan
        $relevantChunks = $this->rag->retrieve($query);

        // Set header SSE
        $headers = [
            'Content-Type'                => 'text/event-stream',
            'Cache-Control'               => 'no-cache',
            'X-Accel-Buffering'           => 'no',    // Penting untuk Nginx
            'Access-Control-Allow-Origin' => '*',
        ];

        return response()->stream(function () use ($query, $relevantChunks): void {
            foreach ($this->rag->generate($query, $relevantChunks) as $chunk) {
                // Format SSE
                echo 'data: ' . json_encode(['text' => $chunk]) . "\n\n";
                ob_flush();
                flush();
            }

            // Kirim event selesai
            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
        }, 200, $headers);
    }

    // ─────────────────────────────────────────────────────────
    // GET /chatbot/suggested
    // Pertanyaan saran untuk memulai percakapan
    // ─────────────────────────────────────────────────────────
    public function suggested(): JsonResponse
    {
        return response()->json([
            'suggestions' => [
                'Apa itu UKM MCI?',
                'Bagaimana cara mendaftar jadi anggota?',
                'Apa saja divisi yang ada di MCI?',
                'Kapan jadwal open recruitment?',
                'Apa keuntungan bergabung di MCI?',
                'Berapa biaya pendaftaran UKM MCI?',
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // POST /chatbot/upload  (Admin only — via Filament atau form admin)
    // Upload PDF dan trigger background processing
    // ─────────────────────────────────────────────────────────
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'pdf'        => ['required', 'file', 'mimes:pdf', 'max:20480'],  // max 20MB
            'deskripsi'  => ['nullable', 'string', 'max:500'],
        ]);

        $file     = $request->file('pdf');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '_' . time() . '.pdf';

        // Simpan ke storage/app/rag-docs/
        $path = $file->storeAs('rag-docs', $filename, 'local');

        // Buat record dokumen
        $document = RagDocument::create([
            'nama_file'  => $file->getClientOriginalName(),
            'path_file'  => $path,
            'deskripsi'  => $request->input('deskripsi'),
            'status'     => 'processing',
        ]);

        // Dispatch background job
        ProcessPdfJob::dispatch($document->id);

        return response()->json([
            'pesan'       => 'PDF berhasil diupload dan sedang diproses. Chatbot akan siap dalam beberapa menit.',
            'document_id' => $document->id,
            'status'      => 'processing',
        ], 202);
    }

    // ─────────────────────────────────────────────────────────
    // GET /chatbot/status/{id}
    // Cek status processing PDF
    // ─────────────────────────────────────────────────────────
    public function status(int $id): JsonResponse
    {
        $doc = RagDocument::findOrFail($id);
        return response()->json([
            'status'       => $doc->status,
            'total_chunks' => $doc->total_chunks,
            'nama_file'    => $doc->nama_file,
        ]);
    }
}
