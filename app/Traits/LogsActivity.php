<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            static::logActivity($model, 'created', null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            static::logActivity($model, 'updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function (Model $model) {
            static::logActivity($model, 'deleted', $model->getAttributes(), null);
        });
    }

    protected static function logActivity(Model $model, string $action, ?array $old, ?array $new)
    {
        // Avoid logging timestamps if they are the only changes
        if ($action === 'updated' && count($new) === 1 && isset($new['updated_at'])) {
            return;
        }

        AuditLog::create([
            'user_id' => auth()->id() ?? 1, // Fallback to system user
            'event' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'action' => "{$action} " . class_basename($model),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
        ]);
    }

    public function activities()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}
