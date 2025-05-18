<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramRecipient extends Model
{
    protected $fillable = ['chat_id', 'username'];
}
