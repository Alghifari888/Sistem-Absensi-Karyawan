<?php

namespace App\Observers\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait RecordsAuditLog
{
    /**
     * Attribute keys that must never be written to old_values/new_values (e.g. password).
     * Override in the using class to customize.
     */
    protected function auditExcept(): array
    {
        return [];
    }

    protected function recordAudit(Model $model, string $activity, ?array $old = null, ?array $new = null): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $old ? $this->filterAuditValues($old) : null,
            'new_values' => $new ? $this->filterAuditValues($new) : null,
        ]);
    }

    protected function filterAuditValues(array $values): array
    {
        return array_diff_key($values, array_flip($this->auditExcept()));
    }
}
