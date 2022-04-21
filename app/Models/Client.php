<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'email',
		'tags',
		'manager_id',
		'updated_at',
		'created_at',
	];

	protected $casts = [
		'tags' => 'array',
		'created_at' => 'datetime:Y-m-d',
		'updated_at' => 'datetime:Y-m-d',
	];

	protected $searcheble = [
		'clients.id',
		'clients.email',
		'clients.name',
		'clients.updated_at',
		// 'clients.created_at',
		'manager.name',
	];
	
	public function manager()
	{
		return $this->belongsTo(User::class);
	}

	public function scopeSearch($query, $search)
	{
		return $query->where(function($q) use ($search) {
			foreach ($this->searcheble as $column) {
				$q = $q->orWhere($column, 'like', '%' . $search . '%');
			}
		});
	}

	public function scopeFilter($query, $column, $value)
	{
		if (is_array($value) && count($value)) {
			$query = $query->whereIn($column, $value);
		}
		if (is_string($value)) {
			$query = $query->where($column, $value)
				->orWhereDate($column, $value);
		}
		return $query;
	}

	public function scopeJoinManager($query)
	{
		return $query->select('clients.*')->join('users as manager', 'manager.id', '=', 'clients.manager_id');
	}
}
