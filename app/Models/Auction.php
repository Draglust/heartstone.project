<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 10 Feb 2018 00:24:00 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Auction
 * 
 * @property int $id
 * @property int $auc
 * @property int $item
 * @property string $owner
 * @property string $ownerRealm
 * @property int $bid
 * @property int $buyout
 * @property int $quantity
 * @property string $timeLeft
 * @property int $rand
 * @property int $seed
 * @property int $context
 * @property int $petSpeciesId
 * @property int $petBreedId
 * @property int $petLevel
 * @property int $petQualityId
 * @property string $remember_token
 * @property int $dateJsonId
 *
 * @package App\Models
 */
class Auction extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'auc' => 'int',
		'item' => 'int',
		'bid' => 'int',
		'buyout' => 'int',
		'quantity' => 'int',
		'rand' => 'int',
		'seed' => 'int',
		'context' => 'int',
		'petSpeciesId' => 'int',
		'petBreedId' => 'int',
		'petLevel' => 'int',
		'petQualityId' => 'int',
		'dateJsonId' => 'int'
	];

	protected $hidden = [
		'remember_token'
	];

	protected $fillable = [
		'auc',
		'item',
		'owner',
		'ownerRealm',
		'bid',
		'buyout',
		'quantity',
		'timeLeft',
		'rand',
		'seed',
		'context',
		'petSpeciesId',
		'petBreedId',
		'petLevel',
		'petQualityId',
		'remember_token',
		'dateJsonId'
	];
}
