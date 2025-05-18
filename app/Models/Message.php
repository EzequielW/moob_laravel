<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'platform', 'recipient', 'content', 'attachment_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
