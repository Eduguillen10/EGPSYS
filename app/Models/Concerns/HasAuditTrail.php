<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use App\Observers\AuditTrailObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasAuditTrail
{
    protected static bool $auditSaveInProgress = false;

    public static function bootHasAuditTrail(): void
    {
        static::observe(AuditTrailObserver::class);
    }

    public function save(array $options = []): bool
    {
        if (static::$auditSaveInProgress || DB::transactionLevel() > 0) {
            return parent::save($options);
        }

        static::$auditSaveInProgress = true;

        try {
            return DB::transaction(fn (): bool => parent::save($options));
        } finally {
            static::$auditSaveInProgress = false;
        }
    }

    public function delete()
    {
        if (static::$auditSaveInProgress || DB::transactionLevel() > 0) {
            return parent::delete();
        }

        static::$auditSaveInProgress = true;

        try {
            return DB::transaction(fn () => parent::delete());
        } finally {
            static::$auditSaveInProgress = false;
        }
    }

    public function auditActionForUpdate(): string
    {
        return $this->auditIsAnulacion() ? 'ANULACION' : 'UPDATED';
    }

    public function auditIsAnulacion(): bool
    {
        $changes = $this->getChanges();

        foreach (['estado', 'cobro_estado'] as $field) {
            if (! array_key_exists($field, $changes)) {
                continue;
            }

            $value = Str::upper((string) $changes[$field]);

            if (in_array($value, ['A', 'ANULADO', 'ANULADA', 'CANCELADO', 'CANCELADA'], true)) {
                return true;
            }
        }

        return false;
    }

    public function auditOldValuesForChanges(): array
    {
        $oldValues = [];

        foreach ($this->auditRelevantChanges() as $field => $value) {
            $oldValues[$field] = $this->getOriginal($field);
        }

        return $this->auditCleanPayload($oldValues);
    }

    public function auditNewValuesForChanges(): array
    {
        $newValues = [];

        foreach ($this->auditRelevantChanges() as $field => $value) {
            $newValues[$field] = $this->getAttribute($field);
        }

        return $this->auditCleanPayload($newValues);
    }

    public function auditValuesForCreate(): array
    {
        return $this->auditCleanPayload($this->getAttributes());
    }

    public function auditAnulado(?string $reason = null, array $oldValues = [], array $newValues = []): AuditLog
    {
        return AuditLog::create([
            'event' => 'ANULACION',
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'user_id' => Auth::id(),
            'old_values' => $this->auditCleanPayload($oldValues),
            'new_values' => $this->auditCleanPayload($newValues),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'request_method' => request()?->method(),
            'url' => request()?->fullUrl(),
            'reason' => $reason ?: $this->auditReasonFromRequest(),
            'transaction_uuid' => (string) Str::uuid(),
            'created_at' => now(),
        ]);
    }

    public function auditReasonFromRequest(): ?string
    {
        foreach (['motivo_anulacion', 'motivo_cancelacion', 'motivo', 'observacion', 'obs', 'comentario'] as $field) {
            $value = request()->input($field);

            if (filled($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    public function auditCleanPayload(array $payload): array
    {
        $sensitiveFields = array_map('strtolower', $this->auditSensitiveFields());

        return $this->auditRemoveSensitiveFields($payload, $sensitiveFields);
    }

    protected function auditRelevantChanges(): array
    {
        return collect($this->getChanges())
            ->except(['created_at', 'updated_at', 'deleted_at'])
            ->all();
    }

    protected function auditSensitiveFields(): array
    {
        $baseFields = [
            'numero_tarjeta',
            'nro_tarjeta',
            'tarjeta_numero',
            'card_number',
            'pan',
            'cvv',
            'cvc',
            'token_pago',
            'payment_token',
            'password',
            'password_confirmation',
        ];

        $customFields = property_exists($this, 'auditSensitive')
            ? (array) $this->auditSensitive
            : [];

        return array_values(array_unique(array_merge($baseFields, $customFields)));
    }

    protected function auditRemoveSensitiveFields(array $payload, array $sensitiveFields): array
    {
        foreach ($payload as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveFields, true)) {
                unset($payload[$key]);
                continue;
            }

            if (is_array($value)) {
                $payload[$key] = $this->auditRemoveSensitiveFields($value, $sensitiveFields);
            }
        }

        return $payload;
    }
}
