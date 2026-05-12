<?php

namespace App\Services;

/**
 * WorkflowService
 *
 * Generic state-machine helper for ERP approval workflows.
 *
 * Usage:
 *   WorkflowService::transition($leaveRequest, 'approve', auth()->user());
 */
class WorkflowService
{
    /**
     * Allowed transitions per model type.
     * Format: 'current_state' => ['action' => 'next_state']
     */
    protected static array $transitions = [
        'leave_request' => [
            'draft'       => ['submit'   => 'submitted'],
            'submitted'   => ['review'   => 'under_review', 'reject' => 'rejected', 'cancel' => 'cancelled'],
            'under_review'=> ['approve'  => 'approved',     'reject' => 'rejected'],
            'approved'    => ['cancel'   => 'cancelled',    'complete' => 'completed'],
            'rejected'    => ['reset'    => 'draft'],
        ],
        'purchase_request' => [
            'draft'       => ['submit'   => 'submitted'],
            'submitted'   => ['approve'  => 'approved',  'reject' => 'rejected'],
            'approved'    => ['order'    => 'ordered'],
            'ordered'     => ['receive'  => 'received'],
        ],
        'asset_request' => [
            'draft'       => ['submit'   => 'submitted'],
            'submitted'   => ['approve'  => 'approved',  'reject' => 'rejected'],
            'approved'    => ['assign'   => 'assigned'],
            'assigned'    => ['return'   => 'returned'],
        ],
    ];

    /**
     * Perform a workflow transition on the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model    Must have a `status` column
     * @param  string                              $action   e.g. 'approve', 'reject'
     * @param  \App\Models\User|null               $actor
     * @param  string                              $type     Workflow type key (leave_request, etc.)
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function transition($model, string $action, $actor = null, string $type = ''): bool
    {
        // Auto-detect type from model class name if not provided
        if (! $type) {
            $type = self::guessType($model);
        }

        $currentState = $model->status;
        $allowedMap   = self::$transitions[$type][$currentState] ?? null;

        if (! $allowedMap) {
            throw new \InvalidArgumentException(
                "No transitions defined for [{$type}] in state [{$currentState}]."
            );
        }

        if (! isset($allowedMap[$action])) {
            throw new \InvalidArgumentException(
                "Action [{$action}] is not allowed from state [{$currentState}] for [{$type}]."
            );
        }

        $newState = $allowedMap[$action];

        $model->status = $newState;

        // Stamp who performed the action
        if ($actor) {
            $approvedField = $action . '_by';
            $approvedAtField = $action . '_at';
            if (isset($model->$approvedField)) {
                $model->$approvedField = $actor->id;
            }
            if (isset($model->$approvedAtField)) {
                $model->$approvedAtField = now();
            }
        }

        return $model->save();
    }

    /**
     * Check if an action is allowed on the model's current state.
     */
    public static function can($model, string $action, string $type = ''): bool
    {
        if (! $type) {
            $type = self::guessType($model);
        }
        $currentState = $model->status;
        $allowedMap   = self::$transitions[$type][$currentState] ?? [];
        return isset($allowedMap[$action]);
    }

    /**
     * Get all available actions for a model's current state.
     */
    public static function availableActions($model, string $type = ''): array
    {
        if (! $type) {
            $type = self::guessType($model);
        }
        return array_keys(self::$transitions[$type][$model->status] ?? []);
    }

    /**
     * Guess workflow type from model class name (e.g., LeaveRequest → leave_request).
     */
    protected static function guessType($model): string
    {
        $class = class_basename($model);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));
    }
}
