<?php
namespace LeagueWrap\Api;

use LeagueWrap\Dto\Champion as Champ;
use LeagueWrap\Dto\ChampionList;

class Champion extends AbstractApi {
	
	/**
	 * Do we want to only get the free champions?
	 *
	 * @param string
	 */
	protected $free = 'false';

	/**
	 * Valid versions for this api call.
	 *
	 * @var array
	 */
	protected $versions = [
		'v1.2',
	];

	/**
	 * A list of all permitted regions for the Champion api call.
	 *
	 * @param array
	 */
	protected $permittedRegions = [
		'br',
		'eune',
		'euw',
		'lan',
		'las',
		'na',
		'oce',
		'ru',
		'tr',
		'kr',
	];

	/**
	 * The amount of time we intend to remember the response for.
	 *
	 * @var int
	 */
	protected $defaultRemember = 86400;

	/**
	 * Gets all the champions in the given region.
	 *
	 * @return ChampionList
	 */
	public function all()
	{
		$params = [
			'freeToPlay' => $this->free,
		];

		$array = $this->request('champion', $params);

		// set up the champions
		return new ChampionList($array);
	}

	/**
	 * Gets the information for a single champion
	 *
	 * @param int $id
	 * @return Champ
	 */
	public function championById($id)
	{
		$info = $this->request('champion/'.$id);
		return new Champ($info);
	}

	/**
	 * Gets all the free champions for this week.
	 *
	 * @uses $this->all()
	 * @return championList
	 */
	public function free()
	{
		$this->free = 'true';
		return $this->all();
	}
}
