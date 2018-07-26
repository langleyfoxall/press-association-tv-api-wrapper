<?php
/**
 * Created by PhpStorm.
 * User: jordan
 * Date: 26/07/18
 * Time: 11:47
 */

namespace LangleyFoxall\PressAssociationTvApi\Objects;


use Illuminate\Support\Collection;

/**
 * Class Schedule
 * @package LangleyFoxall\PressAssociationTvApi\Objects
 */
class Schedule
{
    /**
     * @var Collection
     */
    public $items;

    /**
     * @var array
     */
    private $sportsGenresMap = [
        'Football'        => ['football-club', 'football-international', 'football/soccer'],
        'Tennis'          => ['tennis', 'wheelchair-tennis'],
        'Basketball'      => ['basketball', 'wheelchair-basketball'],
        'Hockey'          => ['hockey', 'ice-hockey'],
        'Rugby'           => ['rugby-league-domestic', 'rugby-league-international', 'rugby-union-domestic', 'rugby-union-international', 'wheelchair-rugby'],
        'Baseball'        => ['baseball'],
        'Boxing'          => ['boxing'],
        'Cricket'         => ['cricket-domestic', 'cricket-international'],
        'Golf'            => ['golf'],
        'Badminton'       => ['badminton'],
        'Horse Racing'    => ['horse-racing'],
    ];

    /**
     * Schedule constructor.
     * @param Collection $items
     */
    public function __construct(Collection $items)
    {
        $this->items = $items;
    }

    /**
     * @param string $genre
     * @return Collection
     */
    public function getByGenre(string $genre)
    {
        if (!$genre) {
            throw new \InvalidArgumentException('Genre is required.');
        }

        $genres = isset($this->sportsGenresMap[$genre]) ? $this->sportsGenresMap[$genre] : [$genre];

        return $this->getByGenres($genres);

    }

    /**
     * @param array $genres
     * @return Collection
     */
    private function getByGenres(array $genres)
    {
        if (!$genres) {
            throw new \InvalidArgumentException('Genres is required.');
        }

        return $this->items->filter(function($item) use ($genres) {
            foreach($item->genres as $genre) {
                if (in_array($genre, $genres)) {
                    return true;
                }
            }
            return false;
        });
    }
}