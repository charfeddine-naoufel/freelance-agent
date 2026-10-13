<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\FreelancerProfile;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelancer_profile_id',
        'company',
        'position',
        'description',
        'started_at',
        'ended_at',
        'is_current',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'is_current' => 'boolean',
    ];

    public function freelancerProfile(): BelongsTo
    {
        return $this->belongsTo(FreelancerProfile::class);
    }
}