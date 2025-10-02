<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title', 'description', 'status', 'priority', 'due_date',
        'estimated_hours', 'actual_hours', 'attachments', 'project_id',
        'assigned_to', 'created_by'
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'attachments' => 'array', // JSON casting
        ];
    }

    // Accessor - is overdue
    protected function getIsOverdueAttribute()
    {
        return $this->due_date->isPast() && $this->status !== 'completed';
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tags::class)->withTimestamps();
    }
}
