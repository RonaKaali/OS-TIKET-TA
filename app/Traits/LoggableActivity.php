<?php

namespace App\Traits;

use App\Services\SecurityEventLogService;
use Illuminate\Support\Facades\Auth;

trait LoggableActivity
{
    /**
     * Log aktivitas CREATE
     */
    protected function logCreate(string $resource, $model, array $additionalContext = []): void
    {
        $logService = app(SecurityEventLogService::class);
        $userId = Auth::id();

        $logService->logActivity(
            userId: $userId,
            action: 'CREATE',
            resource: $resource,
            resourceId: $model->id ?? null,
            resourceName: $this->getResourceName($model),
            changes: [],
            additionalContext: $additionalContext
        );
    }

    /**
     * Log aktivitas READ
     */
    protected function logRead(string $resource, $model = null, array $additionalContext = []): void
    {
        $logService = app(SecurityEventLogService::class);
        $userId = Auth::id();

        $resourceId = is_object($model) ? ($model->id ?? null) : $model;
        $resourceName = is_object($model) ? $this->getResourceName($model) : null;

        $logService->logActivity(
            userId: $userId,
            action: 'READ',
            resource: $resource,
            resourceId: $resourceId,
            resourceName: $resourceName,
            changes: [],
            additionalContext: $additionalContext
        );
    }

    /**
     * Log aktivitas UPDATE
     */
    protected function logUpdate(string $resource, $model, array $originalData = [], array $additionalContext = []): void
    {
        $logService = app(SecurityEventLogService::class);
        $userId = Auth::id();

        // Deteksi perubahan
        $changes = [];
        if (!empty($originalData) && is_object($model)) {
            foreach ($originalData as $key => $oldValue) {
                $newValue = $model->getAttribute($key);
                if ($oldValue != $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        $logService->logActivity(
            userId: $userId,
            action: 'UPDATE',
            resource: $resource,
            resourceId: $model->id ?? null,
            resourceName: $this->getResourceName($model),
            changes: $changes,
            additionalContext: $additionalContext
        );
    }

    /**
     * Log aktivitas DELETE
     */
    protected function logDelete(string $resource, $model, array $additionalContext = []): void
    {
        $logService = app(SecurityEventLogService::class);
        $userId = Auth::id();

        // Ambil nama sebelum dihapus
        $resourceName = $this->getResourceName($model);
        $resourceId = $model->id ?? null;

        $logService->logActivity(
            userId: $userId,
            action: 'DELETE',
            resource: $resource,
            resourceId: $resourceId,
            resourceName: $resourceName,
            changes: [],
            additionalContext: $additionalContext
        );
    }

    /**
     * Get resource name dari model
     */
    protected function getResourceName($model): ?string
    {
        if (!is_object($model)) {
            return null;
        }

        // Coba berbagai field yang umum digunakan untuk nama
        $nameFields = ['name', 'title', 'subject', 'email', 'ticket_number', 'number'];
        
        foreach ($nameFields as $field) {
            if (isset($model->$field)) {
                return $model->$field;
            }
        }

        // Jika tidak ada, gunakan ID
        return $model->id ? "#{$model->id}" : null;
    }
}

