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
        'nomor_tiket',
        'subjek',
        'email_pelapor',
        'nama_pelapor',
        'id_pengguna',
        'id_departemen',
        'id_topik_bantuan',
        'id_prioritas',
        'id_status',
        'id_rencana_sla',
        'jatuh_tempo_pada',
        'ditutup_pada',
        'ditugaskan_ke',
        'dikunci_oleh',
        'dikunci_sampai',
        'bidang_kustom'
    ];

    protected $casts = [
        'jatuh_tempo_pada' => 'datetime',
        'ditutup_pada' => 'datetime',
        'dikunci_sampai' => 'datetime',
        'bidang_kustom' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function (Ticket $t) {
            if (empty($t->uuid)) {
                $t->uuid = (string) Str::uuid();
            }
            if (empty($t->nomor_tiket)) {
                $prefix = env('TICKET_NUMBER_PREFIX', 'CSIRT');
                $length = (int) env('TICKET_NUMBER_LENGTH', 8);

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
                    $exists = static::where('nomor_tiket', $ticketNumber)->exists();

                    if (!$exists) {
                        $t->nomor_tiket = $ticketNumber;
                        break;
                    }

                    // Jika sudah mencoba berkali-kali dan masih duplikat, gunakan timestamp sebagai fallback
                    if ($attempts >= $maxAttempts) {
                        $timestamp = substr(str_replace(['-', ':', ' '], '', now()->toDateTimeString()), -8);
                        $randomSuffix = substr(str_shuffle($chars), 0, 4);
                        $t->nomor_tiket = "{$prefix}-{$timestamp}{$randomSuffix}";
                        break;
                    }
                } while ($attempts < $maxAttempts);
            }
        });
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'id_departemen');
    }
    public function topic()
    {
        return $this->belongsTo(HelpTopic::class, 'id_topik_bantuan');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'id_status');
    }
    public function priority()
    {
        return $this->belongsTo(Priority::class, 'id_prioritas');
    }
    public function sla()
    {
        return $this->belongsTo(SlaPlan::class, 'id_rencana_sla');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'ditugaskan_ke');
    }
    public function locker()
    {
        return $this->belongsTo(User::class, 'dikunci_oleh');
    }

    public function threads()
    {
        return $this->hasMany(TicketThread::class, 'id_tiket');
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
        if ($this->ditutup_pada !== null) {
            return false;
        }

        // Jika tidak ada jatuh_tempo_pada, tidak overdue
        if ($this->jatuh_tempo_pada === null) {
            return false;
        }

        // Hitung hari kerja sejak jatuh_tempo_pada
        $workingDaysSinceDue = static::countWorkingDays($this->jatuh_tempo_pada, now());

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
        $candidateIds = $query->whereNull('ditutup_pada')
            ->whereNotNull('jatuh_tempo_pada')
            ->where('jatuh_tempo_pada', '<', now())
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
