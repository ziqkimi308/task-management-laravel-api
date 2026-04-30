<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
	use HasFactory, SoftDeletes;

	// For mass-assignment
	protected $fillable = [
		'user_id',
		'project_id',
		'title',
		'description',
		'status',
		'priority',
		'due_date',
		'completed_at'
	];

	// For auto value conversion
	protected $casts = [
		'due_date' => 'date',
		'completed_at' => 'datetime'
	];

	// Append
	protected $appends = [
		'is_overdue'
	];

	// Foreign rel
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function project()
	{
		return $this->belongsTo(Project::class);
	}

	// Scopes 
	// Scopes always receives $query
	public function scopePending($query)
	{
		return $query->whereIn('status', ['todo', 'in_progress']);
	}

	public function scopeOverdue($query)
	{
		return $query->where('due_date', '<', now())
			->where('status', '!=', 'completed');
	}

	public function scopeCompleted($query)
	{
		return $query->where('status', 'completed');
	}

	public function scopeHighPriority($query)
	{
		return $query->whereIn('priority', ['high', 'urgent']);
	}

	// Accessor
	public function getIsOverdueAttribute()
	{
		if (!$this->due_date || $this->status === 'completed') {
			return false;
		}
		return $this->due_date->isPast();
	}

	// Event Listener
	protected static function booted()
	{
		static::updating(function ($task) {
			// Check if status is modified
			if ($task->isDirty('status')) {
				// Retrieve original value of an attribute before any changes made in memory
				if ($task->status === 'completed' && $task->getOriginal('status') !== 'completed') {
					$task->completed_at = now();
				} elseif ($task->status !== 'completed') {
					$task->completed_at = null;
				}
			}
		});
	}
}
