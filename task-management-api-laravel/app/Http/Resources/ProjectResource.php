<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'id'=>$this->id,
			'name'=>$this->name,
			'description'=>$this->description,
			'status'=>$this->status,
			'color'=>$this->color,
			'deadline'=>$this->deadline,
			'is_overdue'=>$this->is_overdue,
			'task_count'=>$this->whenLoaded('tasks', fn() => $this->tasks->where('status', 'completed')->count()) //include relationships only if they were eager‑loaded on the model
			,
			'user'=> new UserResource($this->whenLoaded('user')),
			'created_at' => $this->created_at->toDateTimeString(),
			'updated_at' => $this->updated_at->toDateTimeString()
		];
    }
}
