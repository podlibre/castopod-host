<?php

declare(strict_types=1);

/**
 * Class AnalyticsPodcastsByHour Entity for AnalyticsPodcastsByHour
 *
 * @copyright  2020 Ad Aures
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html AGPL3
 * @link       https://castopod.org/
 */

namespace Modules\Analytics\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

/**
 * @property int $podcast_id
 * @property Time $date
 * @property int $hour
 * @property int $hits
 * @property Time $created_at
 * @property Time $updated_at
 */
class AnalyticsPodcastsByHour extends Entity
{
    /**
     * @var list<string>
     */
    protected $dates = ['date', 'created_at', 'updated_at'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'podcast_id' => 'integer',
        'hour'       => 'integer',
        'hits'       => 'integer',
    ];
}
