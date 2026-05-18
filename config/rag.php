<?php
// ── config/rag.php ────────────────────────────────────────────
// Tambahkan ke config/ proyek Laravel Anda

return [

    // ── Embedding Provider ────────────────────────────────────
    // Pilihan: 'voyage' | 'openai' | 'tfidf'
    // 'tfidf' tidak butuh API key (gratis, untuk development)
    // 'voyage' direkomendasikan untuk production (daftar di voyageai.com)
    'embedding_provider' => env('EMBEDDING_PROVIDER', 'gemini'),

    'gemini_api_key' => env('GEMINI_API_KEY'),

    // API key untuk Voyage AI atau OpenAI
    'embedding_api_key' => env('EMBEDDING_API_KEY', ''),

    // Model embedding gemini
    'embedding_model' => env('EMBEDDING_MODEL', 'gemini-embedding-001'),

];
