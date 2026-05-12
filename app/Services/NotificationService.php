<?php

namespace App\Services;

use App\Models\ErpNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

/**
 * NotificationService
 *
 * Handles in-app and email notifications.
 *
 * Usage:
 *   NotificationService::send($user, 'Leave Approved', 'Your leave has been approved.', $leaveRequest);
 *   NotificationService::sendToRole('hr_manager', 'New Leave Request', $leaveRequest);
 */
class NotificationService
{
    /**
     * Send an in-app notification to a specific user.
     */
    public static function send(
        User   $recipient,
        string $title,
        string $body = '',
        ?Model $relatedModel = null,
        string $type = 'info',
        string $url = '',
        string $icon = ''
    ): ErpNotification {
        $notification = ErpNotification::create([
            'user_id'          => $recipient->id,
            'title'            => $title,
            'body'             => $body,
            'type'             => $type,
            'icon'             => $icon,
            'url'              => $url,
            'notifiable_type'  => $relatedModel ? get_class($relatedModel) : null,
            'notifiable_id'    => $relatedModel?->getKey(),
        ]);

        return $notification;
    }

    /**
     * Send in-app notification to multiple users.
     */
    public static function sendToMany(
        iterable $users,
        string   $title,
        string   $body = '',
        ?Model   $relatedModel = null,
        string   $type = 'info',
        string   $url = ''
    ): void {
        foreach ($users as $user) {
            self::send($user, $title, $body, $relatedModel, $type, $url);
        }
    }

    /**
     * Send in-app notification to all users with a given role slug.
     */
    public static function sendToRole(
        string $roleSlug,
        string $title,
        string $body = '',
        ?Model $relatedModel = null,
        string $type = 'info',
        string $url = ''
    ): void {
        $users = User::whereHas('roles', fn ($q) => $q->where('slug', $roleSlug))->get();
        self::sendToMany($users, $title, $body, $relatedModel, $type, $url);
    }

    /**
     * Get unread notification count for a user.
     */
    public static function unreadCount(int $userId): int
    {
        return ErpNotification::forUser($userId)->unread()->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllRead(int $userId): void
    {
        ErpNotification::forUser($userId)->unread()->update(['read_at' => now()]);
    }
}
