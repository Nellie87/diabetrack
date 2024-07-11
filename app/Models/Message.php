<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['from_id', 'to_id', 'content', 'parent_id'];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id','id');
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }
}
