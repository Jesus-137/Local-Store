<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_id',
        'title',
        'notes',
        'start',
        'end',
        'status'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
