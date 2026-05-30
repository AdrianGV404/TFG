<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskTimeEntry;
use App\Models\User;

class NotificationService
{
    public static function send(
        User   $user,
        string $type,
        string $title,
        string $body,
        array  $data = []
    ): void {
        $settings = $user->settings;

        $wantsNotif = match ($type) {
            Notification::TYPE_ASSIGNMENT    => $settings?->notif_tasks         ?? true,
            Notification::TYPE_DUE_SOON      => $settings?->notif_alerts        ?? true,
            Notification::TYPE_STATUS_CHANGE => $settings?->notif_status_change ?? true,
            default                          => true,
        };

        if (! $wantsNotif) {
            return;
        }

        $channel = $settings?->notif_channel ?? 'app';

        if (in_array($channel, ['app', 'both'])) {
            Notification::create([
                'user_id' => $user->id,
                'type'    => $type,
                'title'   => $title,
                'body'    => $body,
                'data'    => $data,
            ]);
        }
    }

    public static function notifyProjectAssignment(User $user, string $projectName): void
    {
        self::send(
            $user,
            Notification::TYPE_ASSIGNMENT,
            'Nuevo proyecto asignado',
            "Has sido añadido al proyecto \"{$projectName}\".",
        );
    }

    public static function notifyTaskAssignment(User $user, string $taskTitle, string $projectName): void
    {
        self::send(
            $user,
            Notification::TYPE_ASSIGNMENT,
            'Nueva tarea asignada',
            "Se te ha asignado la tarea \"{$taskTitle}\" en el proyecto \"{$projectName}\".",
        );
    }

public static function notifyStatusChange(
    Task   $task,
    string $oldStatus,
    string $newStatus,
    int    $changedByUserId
): void {
    $labels = [
        'pending'     => 'Pendiente',
        'in_progress' => 'En progreso',
        'on_hold'     => 'En pausa',
        'testing'     => 'En pruebas',
        'done'        => 'Hecha',
    ];

    $workerIds = $task->project->users()
        ->where('users.id', '!=', $changedByUserId)
        ->pluck('users.id')
        ->toArray();

    if (empty($workerIds)) {
        return;
    }

    $oldLabel = $labels[$oldStatus] ?? $oldStatus;
    $newLabel = $labels[$newStatus] ?? $newStatus;

    $users = User::whereIn('id', $workerIds)->with('settings')->get();

    foreach ($users as $user) {
        self::send(
            $user,
            Notification::TYPE_STATUS_CHANGE,
            "Tarea actualizada: {$task->title}",
            "Estado cambiado de \"{$oldLabel}\" a \"{$newLabel}\".",
            ['task_id' => $task->id, 'project_id' => $task->project_id],
        );
    }
}

    public static function notifyDueSoon(Task $task): void
    {
        $workerIds = TaskTimeEntry::where('task_id', $task->id)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (empty($workerIds)) {
            return;
        }

        $users = User::whereIn('id', $workerIds)->with('settings')->get();

        foreach ($users as $user) {
            self::send(
                $user,
                Notification::TYPE_DUE_SOON,
                'Tarea próxima a vencer',
                "La tarea \"{$task->title}\" vence el {$task->due_date->format('d/m/Y')}.",
                ['task_id' => $task->id, 'project_id' => $task->project_id],
            );
        }
    }
}