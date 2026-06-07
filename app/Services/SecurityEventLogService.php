<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SecurityEventLogService
{
    /**
     * Log security event
     */
    public function logEvent(array $eventData): void
    {
        $event = [
            'user_id' => $eventData['user_id'] ?? null,
            'event_type' => $eventData['event_type'] ?? 'unknown',
            'severity' => $eventData['severity'] ?? 'low',
            'ip_address' => $eventData['ip_address'] ?? request()->ip(),
            'user_agent' => $eventData['user_agent'] ?? request()->userAgent(),
            'context' => json_encode($eventData['context'] ?? []),
            'message' => $eventData['message'] ?? '',
            'metadata' => json_encode($eventData['metadata'] ?? []),
            'created_at' => now()->toDateTimeString(),
        ];

        // Log ke file
        $this->logToFile($event);

        // Simpan ke cache untuk real-time monitoring (last 100 events)
        $this->saveToCache($event);

        // Simpan ke database jika model tersedia
        $this->saveToDatabase($event);
    }

    /**
     * Log ke file
     */
    protected function logToFile(array $event): void
    {
        // Ambil nama user jika ada user_id
        $userName = 'Guest';
        $userEmail = null;
        if (!empty($event['user_id'])) {
            try {
                $user = \App\Models\User::find($event['user_id']);
                if ($user) {
                    $userName = $user->name ?? "User #{$event['user_id']}";
                    $userEmail = $user->email ?? null;
                }
            } catch (\Exception $e) {
                // Jika tidak bisa load user, gunakan ID
                $userName = "User #{$event['user_id']}";
            }
        }

        // Format log yang lebih rapi (tanpa timestamp karena Laravel sudah menambahkannya)
        $severityLabel = $this->getSeverityLabel($event['severity']);
        $logMessage = sprintf(
            "[%s] User: %s (%s) | IP: %s | Event: %s | %s",
            $severityLabel,
            $userName,
            $userEmail ?: ($event['user_id'] ?? 'Guest'),
            $event['ip_address'],
            $event['event_type'],
            $event['message']
        );

        try {
            // Cobalah menulis ke channel khusus security
            Log::channel('security')->info($logMessage, [
                'context' => json_decode($event['context'], true),
                'metadata' => json_decode($event['metadata'], true),
            ]);
        } catch (\Exception $e) {
            // Jika di Vercel/Serverless penulisan file dilarang, 
            // fallback ke log utama (stderr/error_log) agar tetap muncul di dashboard Vercel
            Log::warning("[SECURITY_FALLBACK] " . $logMessage, [
                'original_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get severity label dalam bahasa Indonesia
     */
    protected function getSeverityLabel(string $severity): string
    {
        return match(strtolower($severity)) {
            'low' => 'RENDAH',
            'medium' => 'SEDANG',
            'high' => 'TINGGI',
            'critical' => 'KRITIS',
            default => strtoupper($severity),
        };
    }

    /**
     * Simpan ke cache untuk real-time access
     */
    protected function saveToCache(array $event): void
    {
        $key = 'security_events:recent';
        $events = Cache::get($key, []);

        // Tambahkan event baru di awal
        array_unshift($events, $event);

        // Keep hanya 100 event terakhir
        if (count($events) > 100) {
            $events = array_slice($events, 0, 100);
        }

        Cache::put($key, $events, now()->addHours(24));
    }

    /**
     * Simpan ke database (jika tabel sudah ada)
     */
    protected function saveToDatabase(array $event): void
    {
        if (!class_exists(\App\Models\SecurityEvent::class)) {
            return;
        }

        try {
            $context = json_decode($event['context'] ?? '{}', true) ?: [];
            $metadata = json_decode($event['metadata'] ?? '{}', true) ?: [];

            $riskScore = $event['risk_score']
                ?? $context['risk_score']
                ?? null;

            $deviceFingerprint = $event['device_fingerprint']
                ?? $metadata['device_fingerprint']
                ?? null;

            \App\Models\SecurityEvent::create([
                'user_id' => $event['user_id'] ?? null,
                'event_type' => $event['event_type'] ?? 'unknown',
                'severity' => $event['severity'] ?? 'low',
                'ip_address' => $event['ip_address'] ?? null,
                'user_agent' => $event['user_agent'] ?? null,
                'device_fingerprint' => $deviceFingerprint,
                'context' => $context,
                'message' => $event['message'] ?? '',
                'metadata' => $metadata,
                'risk_score' => is_numeric($riskScore) ? (int) $riskScore : null,
                'created_at' => $event['created_at'] ?? now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save security event to database: ' . $e->getMessage());
        }
    }

    /**
     * Dapatkan recent security events
     */
    public function getRecentEvents(int $limit = 50): array
    {
        $key = 'security_events:recent';
        $events = Cache::get($key, []);

        return array_slice($events, 0, $limit);
    }

    /**
     * Dapatkan events untuk user tertentu
     */
    public function getUserEvents(int $userId, int $limit = 50): array
    {
        $key = 'security_events:recent';
        $events = Cache::get($key, []);

        $userEvents = array_filter($events, function ($event) use ($userId) {
            return ($event['user_id'] ?? null) === $userId;
        });

        return array_slice(array_values($userEvents), 0, $limit);
    }

    /**
     * Dapatkan events berdasarkan severity
     */
    public function getEventsBySeverity(string $severity, int $limit = 50): array
    {
        $key = 'security_events:recent';
        $events = Cache::get($key, []);

        $filteredEvents = array_filter($events, function ($event) use ($severity) {
            return ($event['severity'] ?? 'low') === $severity;
        });

        return array_slice(array_values($filteredEvents), 0, $limit);
    }

    /**
     * Log authentication event
     */
    public function logAuthentication(string $type, ?int $userId, bool $success, string $message = ''): void
    {
        $gps = request()->session()->get('zero_trust_gps');

        $this->logEvent([
            'user_id' => $userId,
            'event_type' => "auth_{$type}",
            'severity' => $success ? 'low' : 'high',
            'message' => $message ?: ($success ? "Authentication {$type} successful" : "Authentication {$type} failed"),
            'context' => [
                'success' => $success,
                'type' => $type,
                'ip' => request()->ip(),
                'gps' => $gps,
            ],
        ]);
    }

    /**
     * Log authorization event
     */
    public function logAuthorization(?int $userId, string $permission, bool $granted, string $resource = ''): void
    {
        $this->logEvent([
            'user_id' => $userId,
            'event_type' => 'authorization_check',
            'severity' => $granted ? 'low' : 'medium',
            'message' => $granted
                ? "Access granted to {$permission}"
                : "Access denied to {$permission}",
            'context' => [
                'permission' => $permission,
                'granted' => $granted,
                'resource' => $resource,
            ],
        ]);
    }

    /**
     * Log device event
     */
    public function logDeviceEvent(?int $userId, string $eventType, array $deviceInfo): void
    {
        $this->logEvent([
            'user_id' => $userId,
            'event_type' => "device_{$eventType}",
            'severity' => 'medium',
            'message' => "Device {$eventType}",
            'context' => $deviceInfo,
        ]);
    }

    /**
     * Log anomaly event
     */
    public function logAnomaly(?int $userId, string $anomalyType, array $context): void
    {
        $this->logEvent([
            'user_id' => $userId,
            'event_type' => 'anomaly_detected',
            'severity' => 'high',
            'message' => "Anomaly detected: {$anomalyType}",
            'context' => array_merge($context, ['anomaly_type' => $anomalyType]),
        ]);
    }

    /**
     * Log aktivitas CRUD (Create, Read, Update, Delete)
     */
    public function logActivity(
        ?int $userId,
        string $action, // CREATE, READ, UPDATE, DELETE
        string $resource, // Nama resource (Ticket, User, Department, dll)
        ?int $resourceId = null,
        ?string $resourceName = null,
        array $changes = [],
        array $additionalContext = []
    ): void {
        // Validasi action
        $validActions = ['CREATE', 'READ', 'UPDATE', 'DELETE'];
        if (!in_array(strtoupper($action), $validActions)) {
            throw new \InvalidArgumentException("Invalid action: {$action}. Must be one of: " . implode(', ', $validActions));
        }

        $action = strtoupper($action);
        
        // Tentukan severity berdasarkan action
        $severity = match($action) {
            'CREATE' => 'low',
            'READ' => 'low',
            'UPDATE' => 'medium',
            'DELETE' => 'high',
            default => 'low',
        };

        // Buat pesan yang deskriptif
        $message = $this->buildActivityMessage($action, $resource, $resourceId, $resourceName, $changes);

        // Context untuk log
        $context = array_merge([
            'action' => $action,
            'resource' => $resource,
            'resource_id' => $resourceId,
            'resource_name' => $resourceName,
            'changes' => $changes,
        ], $additionalContext);

        $this->logEvent([
            'user_id' => $userId,
            'event_type' => 'activity_' . strtolower($action),
            'severity' => $severity,
            'message' => $message,
            'context' => $context,
        ]);
    }

    /**
     * Build activity message
     */
    protected function buildActivityMessage(
        string $action,
        string $resource,
        ?int $resourceId,
        ?string $resourceName,
        array $changes
    ): string {
        $actionLabel = match($action) {
            'CREATE' => 'Membuat',
            'READ' => 'Melihat',
            'UPDATE' => 'Memperbarui',
            'DELETE' => 'Menghapus',
            default => $action,
        };

        $resourceLabel = $this->getResourceLabel($resource);
        
        $identifier = $resourceName 
            ? "{$resourceLabel} \"{$resourceName}\""
            : ($resourceId ? "{$resourceLabel} #{$resourceId}" : $resourceLabel);

        $message = "{$actionLabel} {$identifier}";

        // Tambahkan detail perubahan untuk UPDATE
        if ($action === 'UPDATE' && !empty($changes)) {
            $changeDetails = [];
            foreach ($changes as $field => $value) {
                if (is_array($value) && isset($value['old']) && isset($value['new'])) {
                    $changeDetails[] = "{$field}: \"{$value['old']}\" → \"{$value['new']}\"";
                } else {
                    $changeDetails[] = "{$field}: " . (is_array($value) ? json_encode($value) : $value);
                }
            }
            if (!empty($changeDetails)) {
                $message .= " | Perubahan: " . implode(', ', array_slice($changeDetails, 0, 5));
                if (count($changeDetails) > 5) {
                    $message .= " (dan " . (count($changeDetails) - 5) . " perubahan lainnya)";
                }
            }
        }

        return $message;
    }

    /**
     * Get resource label dalam bahasa Indonesia
     */
    protected function getResourceLabel(string $resource): string
    {
        return match(strtolower($resource)) {
            'ticket' => 'Tiket',
            'user' => 'Pengguna',
            'department' => 'Departemen',
            'help_topic', 'helptopic' => 'Topik Bantuan',
            'sla_plan', 'slaplan' => 'Rencana SLA',
            'priority' => 'Prioritas',
            'status' => 'Status',
            'team' => 'Tim',
            'canned_response', 'cannedresponse' => 'Respons Cepat',
            'organization' => 'Organisasi',
            'chatbot_response', 'chatbotresponse' => 'Respons Chatbot',
            'attachment' => 'Lampiran',
            'thread' => 'Thread',
            'note' => 'Catatan',
            default => ucfirst(str_replace('_', ' ', $resource)),
        };
    }
}
