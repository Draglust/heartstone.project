<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 25 Feb 2018 10:49:49 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Model;

/**
 * Class Urljson
 * 
 * @property int $id
 * @property string $url
 * @property \Carbon\Carbon $date
 * @property int $dateNum
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Urljson extends Model
{
	protected $connection = 'mysql';
	protected $table = 'urljson';

	protected $casts = [
		'dateNum' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $hidden = [
		'remember_token'
	];

	protected $fillable = [
		'url',
		'date',
		'dateNum',
		'remember_token'
	];
}
