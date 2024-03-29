<?php
namespace LeagueWrap\Dto\StaticData;

use LeagueWrap\Dto\AbstractListDto;

class SummonerSpellList extends AbstractListDto {

	protected $listKey = 'data';

	public function __construct(array $info)
	{
		if (isset($info['data']))
		{
			$spells = [];
			foreach ($info['data'] as $id => $spell)
			{
				$summonerSpellDto = new SummonerSpell($spell);
				$spells[$id]      = $summonerSpellDto;
			}
			$info['data'] = $spells;
		}

		parent::__construct($info);
	}

	/**
	 * A quick short cut to get the summoner spells by id.
	 *
	 * @param int $id
	 * @return SummonerSpell|null
	 */
	public function getSpell($id)
	{
		if (isset($this->info['data'][$id]))
		{
			return $this->info['data'][$id];
		}

		return null;
	}
}
