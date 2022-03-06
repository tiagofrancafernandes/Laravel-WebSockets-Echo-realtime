<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable     = [
        'message',
        'readed',
        'from',
        'to',
    ];

    protected $casts     = [
        'readed' => 'boolean',
    ];

    /**
     * Message from
     */
    public function from()
    {
        return $this->belongsTo(User::class, 'from', 'id');
    }

    /**
     * Message to
     */
    public function to()
    {
        return $this->belongsTo(User::class, 'to', 'id');
    }
}
