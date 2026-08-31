<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'assessment_id',
        'certificate_number',
        'verification_token',
        'issue_date',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function assessment()
    {
        return $this->belongsTo(InternAssessment::class, 'assessment_id');
    }

    public static function generateCertificateNumber(User $intern): string
    {
        $year = date('Y');
        $month = date('m');
        $divCode = $intern->internProfile?->division?->code ?? 'PLND-SUB';
        $count = self::whereYear('created_at', $year)->count() + 1;
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "CERT/{$divCode}/{$year}/{$month}/{$sequence}";
    }

    public static function generateUniqueToken(): string
    {
        return Str::random(40);
    }
}
