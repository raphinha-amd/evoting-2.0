<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemilu extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_private',
        'password',
        'ukm_id',
        'status',
        'user_id',
        'total_participants',
    ];

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodis()
    {
        return $this->belongsToMany(Prodi::class, 'pemilu_prodi');
    }

    public function ukm()
    {
        return $this->belongsTo(Ukm::class);
    }

    public function kandidats()
    {
        return $this->hasMany(Kandidat::class);
    }

    public function voting()
    {
        return $this->hasMany(Voting::class);
    }
}
