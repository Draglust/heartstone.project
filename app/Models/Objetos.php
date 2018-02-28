<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 25 Feb 2018 10:49:49 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Objeto
 * 
 * @property int $Id
 * @property string $Nombre
 * @property string $Descripcion
 * @property string $Icono
 *
 * @package App\Models
 */
class Objeto extends Eloquent
{
	protected $connection = 'mysql';
	protected $table = 'objeto';
	protected $primaryKey = 'Id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Id' => 'int'
	];

	protected $fillable = [
		'Nombre',
		'Descripcion',
		'Icono'
	];
}
