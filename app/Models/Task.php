<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public const STATUSES = ['To Do', 'In Progress', 'Done'];

    protected $fillable = ['title', 'description', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
