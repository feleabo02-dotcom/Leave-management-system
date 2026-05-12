<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * AuditService
 *
 * Records every significant action in the ERP for compliance and traceability.
 * Usage:
 *   AuditService::log('created', $employee);
 *   AuditService::log('approved', $leaveRequest, ['status' => 'pending'], ['status' => 'approved']);
 *   AuditService::logEvent('login', null, 'User logged in');
 */
class AuditService
{
    /**
     * Log a model event (created, updated, deleted, etc.)
     */
    public static function log(
        string $event,
        ?Model $model = null,
        array  $oldValues = [],
        array  $newValues = [],
        string $action = null
    ): void {
        try {
            AuditLog::create([
                'user_id'        => Auth::id(),
                'event'          => $event,
                'auditable_type' => $model ? get_class($model) : null,
                'auditable_id'   => $model?->getKey(),
                'action'         => $action ?? $event,
                'old_values'     => $oldValues ?: null,
                'new_values'     => $newValues ?: null,
                'ip_address'     => Request::ip(),
                'url'            => Request::fullUrl(),
                'company_id'     => Auth::user()?->company_id,
            ]);
        } catch (\Throwable $e) {
            // Never let audit failure break the application
            logger()->error('AuditService failed: ' . $e->getMessage());
        }
    }

    /**
     * Log a simple freeform event (login, export, etc.)
     */
    public static function logEvent(string $event, ?string $description = null): void
    {
        self::log($event, null, [], ['description' => $description]);
    }

    /**
     * Diff two arrays and return only changed fields.
     */
    public static function diff(array $old, array $new): array
    {
        $changed = [];
        foreach ($new as $key => $value) {
            if (!array_key_exists($key, $old) || $old[$key] !== $value) {
                $changed[$key] = ['old' => $old[$key] ?? null, 'new' => $value];
            }
        }
        return $changed;
    }
}
