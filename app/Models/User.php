<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject //, MustVerifyEmail
{
	use Notifiable,
		HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'created_at' => 'datetime:Y-m-d',
		'updated_at' => 'datetime:Y-m-d',
	];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [
		'photo_url',
	];

	protected $searcheble = [
		'id',
		'email',
		'name',
		'updated_at',
	];

	/**
	 * Get the profile photo URL attribute.
	 *
	 * @return string
	 */
	public function getPhotoUrlAttribute()
	{
		return vsprintf('https://www.gravatar.com/avatar/%s.jpg?s=200&d=%s', [
			md5(strtolower($this->email)),
			$this->name ? urlencode("https://ui-avatars.com/api/$this->name") : 'mp',
		]);
	}

	/**
	 * Get the oauth providers.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function oauthProviders()
	{
		return $this->hasMany(OAuthProvider::class);
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ResetPassword($token));
	}

	/**
	 * Send the email verification notification.
	 *
	 * @return void
	 */
	public function sendEmailVerificationNotification()
	{
		$this->notify(new VerifyEmail);
	}

	/**
	 * @return int
	 */
	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * @return array
	 */
	public function getJWTCustomClaims()
	{
		return [];
	}

	public function clients()
	{
		return $this->hasMany(Client::class, 'manager_id');
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
}
