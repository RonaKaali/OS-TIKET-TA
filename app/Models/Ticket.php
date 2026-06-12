<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tiket';

    protected $fillable = [
        'uuid',
        'ticket_number',
        'subject',
        'reporter_email',
        'reporter_name',
        'user_id',
        'department_id',
        'help_topic_id',
        'priority_id',
        'status_id',
        'sla_plan_id',
        'due_at',
        'closed_at',
        'assigned_to',
        'assigned_at',
        'acknowledged_at',
        'locked_by',
        'locked_until',
        'custom_fields'
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'assigned_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'closed_at' => 'datetime',
        'locked_until' => 'datetime',
        'custom_fields' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function (Ticket $t) {
            if (empty($t->uuid)) {
                $t->uuid = (string) Str::uuid();
            }
            if (empty($t->ticket_number)) {
                $prefix = config('ticket.number_prefix', 'CSIRT');
                $length = max(4, (int) config('ticket.number_length', 8));

                // Generate random alphanumeric string
                $maxAttempts = 10;
                $attempts = 0;

                do {
                    // Generate random string: kombinasi huruf besar dan angka
                    // Menghindari karakter yang membingungkan: 0, O, I, 1, L
                    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                    $random = '';
                    for ($i = 0; $i < $length; $i++) {
                        $random .= $chars[random_int(0, strlen($chars) - 1)];
                    }

                    $ticketNumber = "{$prefix}-{$random}";
                    $attempts++;

                    // Cek apakah nomor sudah ada
                    $exists = static::where('ticket_number', $ticketNumber)->exists();

                    if (!$exists) {
                        $t->ticket_number = $ticketNumber;
                        break;
                    }

                    // Jika sudah mencoba berkali-kali dan masih duplikat, gunakan timestamp sebagai fallback
                    if ($attempts >= $maxAttempts) {
                        $timestamp = substr(str_replace(['-', ':', ' '], '', now()->toDateTimeString()), -8);
                        $randomSuffix = substr(str_shuffle($chars), 0, 4);
                        $t->ticket_number = "{$prefix}-{$timestamp}{$randomSuffix}";
                        break;
                    }
                } while ($attempts < $maxAttempts);
            }
        });
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function topic()
    {
        return $this->belongsTo(HelpTopic::class, 'help_topic_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }
    public function sla()
    {
        return $this->belongsTo(SlaPlan::class, 'sla_plan_id');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function threads()
    {
        return $this->hasMany(TicketThread::class, 'ticket_id');
    }

    /**
     * Menghitung jumlah hari kerja (Senin-Jumat) antara dua tanggal
     * 
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon|null $endDate
     * @return int
     */
    public static function countWorkingDays($startDate, $endDate = null)
    {
        if ($endDate === null) {
            $endDate = now();
        }

        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->startOfDay();

        if ($start->gt($end)) {
            return 0;
        }

        $workingDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            // Senin = 1, Jumat = 5
            if ($current->dayOfWeek >= 1 && $current->dayOfWeek <= 5) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Mengecek apakah tiket sudah overdue berdasarkan hari kerja
     * Overdue jika lebih dari 5 hari kerja sejak due_at
     * 
     * @return bool
     */
    public function isOverdue()
    {
        // Jika sudah ditutup, tidak overdue
        if ($this->closed_at !== null) {
            return false;
        }

        // Jika tidak ada due_at, tidak overdue
        if ($this->due_at === null) {
            return false;
        }

        // Hitung hari kerja sejak due_at
        $workingDaysSinceDue = static::countWorkingDays($this->due_at, now());

        // Overdue jika lebih dari 5 hari kerja
        return $workingDaysSinceDue > 5;
    }

    /**
     * Scope untuk query tiket yang overdue
     * Filter berdasarkan hari kerja (lebih dari 5 hari kerja)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        // Ambil semua tiket yang memenuhi kriteria dasar
        $candidateIds = $query->whereNull('closed_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->pluck('id');

        // Filter berdasarkan perhitungan hari kerja
        $overdueIds = static::whereIn('id', $candidateIds)
            ->get()
            ->filter(function ($ticket) {
                return $ticket->isOverdue();
            })
            ->pluck('id');

        return $query->whereIn('id', $overdueIds);
    }
}
