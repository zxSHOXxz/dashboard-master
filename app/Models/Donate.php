<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donate extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'value',
        'donor_id',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
