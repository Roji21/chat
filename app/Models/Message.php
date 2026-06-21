<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender', 'recipient', 'message_text', 'sent_time'];

    protected static function boot()
    {
        parent::boot();

        // Validasi: Pengirim tidak boleh sama dengan penerima
        static::creating(function ($message) {
            if ($message->sender === $message->recipient) {
                throw new \Exception("Sender and recipient cannot be the same person.");
            }
        });
    }
}
