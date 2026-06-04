<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditTrailObserver
{
    public function created(Model $model): void
    {
        $this->write($model, 'CREATED', null, $model->auditValuesForCreate());
    }

    public function updated(Model $model): void
    {
        $newValues = $model->auditNewValuesForChanges();

        if ($newValues === []) {
            return;
        }

        $this->write(
            $model,
            $model->auditActionForUpdate(),
            $model->auditOldValuesForChanges(),
            $newValues
        );
    }

    public function deleted(Model $model): void
    {
        $this->write($model, 'DELETED', $model->auditCleanPayload($model->getOriginal()), null);
    }

    private function write(Model $model, string $event, ?array $oldValues, ?array $newValues): AuditLog
    {
        return AuditLog::create([
            'event' => $event,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'idusuario' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'request_method' => request()?->method(),
            'url' => request()?->fullUrl(),
            'reason' => $event === 'ANULACION' ? $model->auditReasonFromRequest() : null,
            'transaction_uuid' => (string) Str::uuid(),
            'created_at' => now(),
        ]);
    }
}
