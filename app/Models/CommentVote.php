<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model
{
    use HasFactory;

    public function goalComment()
    {
        return $this->belongsTo(GoalComment::class);
    }
}
