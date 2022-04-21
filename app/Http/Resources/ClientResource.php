<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */


	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'manager_id' => $this->manager_id,
			'name' => $this->name,
			'email' => $this->email,
			'tags' => $this->tags,
			'manager' => $this->whenLoaded('manager')
		];
	}
}
