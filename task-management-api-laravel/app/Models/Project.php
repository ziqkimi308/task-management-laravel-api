<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

	// Configuration Properties
	protected $fillable = [
		'user_id',
		'name',
		'description',
		'status',
		'color',
		'deadline'
	];

	protected $casts = [
		'deadline'=>'date'
	];

	// Foreign Rel
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function tasks()
	{
		return $this->hasMany(Task::class);
	}

	// Scope
	// Basically reusable query or custom query we define to be use repeatly. Scope is defined in Model, then use in other like Controller.
	public function scopeActive($query)
	{
		return $query->where('status', 'active');
	}

	public function scopeCompleted($query)
	{
		return $query->where('status', 'completed');
	}

	// Accessor
	// Scope is for query builder but accessor directly return value or change value
	public function getIsOverdueAttribute()
	{
		if (!$this->deadline) {
			return false;
		}
		return $this->deadline->isPast() && $this->status !== 'completed';
	}
}
