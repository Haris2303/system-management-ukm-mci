<?php

namespace App\Support;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Pencatat aktivitas aplikasi ke channel "activity" (storage/logs/activity-*.log).
 *
 * Mencatat: login/logout/gagal login, serta create/update/delete model App\Models\*.
 * Request HTTP dicatat oleh App\Http\Middleware\LogRequestActivity.
 */
class ActivityLogger
{
    /** Atribut yang tidak boleh masuk log. */
    private const HIDDEN = ['password', 'remember_token', 'embedding', 'two_factor_secret', 'two_factor_recovery_codes'];

    /** Model dengan volume tinggi yang dilewati. */
    private const IGNORED_MODELS = [\App\Models\RagChunk::class, \App\Models\ChatHistory::class];

    public static function enabled(): bool
    {
        return (bool) config('logging.activity_enabled', true);
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        if (! self::enabled()) {
            return;
        }

        try {
            Log::channel('activity')->log($level, $message, $context);
        } catch (\Throwable) {
            // Logging tidak boleh menjatuhkan aplikasi.
        }
    }

    public static function register(): void
    {
        Event::listen(Login::class, fn (Login $e) => self::log('info', 'auth.login', self::authContext($e->user)));
        Event::listen(Logout::class, fn (Logout $e) => self::log('info', 'auth.logout', self::authContext($e->user)));
        Event::listen(Failed::class, fn (Failed $e) => self::log('warning', 'auth.failed', [
            'email' => $e->credentials['email'] ?? null,
            'ip'    => request()->ip(),
        ]));

        foreach (['created', 'updated', 'deleted'] as $action) {
            Event::listen("eloquent.{$action}: *", function (string $event, array $payload) use ($action) {
                $model = $payload[0] ?? null;

                if ($model instanceof Model) {
                    self::logModel($action, $model);
                }
            });
        }
    }

    private static function logModel(string $action, Model $model): void
    {
        $class = $model::class;

        if (! str_starts_with($class, 'App\\Models\\') || in_array($class, self::IGNORED_MODELS, true)) {
            return;
        }

        $context = [
            'model'   => class_basename($class),
            'id'      => $model->getKey(),
            'user_id' => auth()->id(),
        ];

        if ($action === 'updated') {
            $changes = collect($model->getChanges())->except([...self::HIDDEN, 'updated_at']);

            if ($changes->isEmpty()) {
                return;
            }

            $context['changes'] = $changes
                ->map(fn ($new, $key) => ['old' => self::scalar($model->getOriginal($key)), 'new' => self::scalar($new)])
                ->all();
        }

        self::log('info', "model.{$action}", $context);
    }

    private static function authContext(mixed $user): array
    {
        return [
            'user_id' => $user?->getAuthIdentifier(),
            'email'   => $user->email ?? null,
            'ip'      => request()->ip(),
        ];
    }

    /** Potong nilai panjang agar log tetap ringkas. */
    private static function scalar(mixed $value): mixed
    {
        return is_string($value) && mb_strlen($value) > 100 ? mb_substr($value, 0, 100) . '...' : $value;
    }
}
