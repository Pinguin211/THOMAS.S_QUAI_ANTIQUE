<?php

namespace App\Entity\Timetable;

use App\Lib\JsonFile;
use App\Service\PathInterface;
use Symfony\Component\HttpFoundation\File\File;

class Timetable
{
    public const KEY_TIMETABLE = 'timetable';

    private const KEY_MONDAY = 'monday';
    private const KEY_TUESDAY = 'tuesday';
    private const KEY_WEDNESDAY = 'wednesday';
    private const KEY_THURSDAY = 'thursday';
    private const KEY_FRIDAY = 'friday';
    private const KEY_SATURDAY = 'saturday';
    private const KEY_SUNDAY = 'sunday';
    private const ARR_KEY_DAY = [
        self::KEY_MONDAY, self::KEY_TUESDAY, self::KEY_WEDNESDAY, self::KEY_THURSDAY,
        self::KEY_FRIDAY, self::KEY_SATURDAY, self::KEY_SUNDAY
    ];

    /**
     * @var Day[] $day
     */
    private array $day;

    public function __construct(array $day)
    {
        $this->day = $day;
    }

    public static function ConstructWithArray(array $info): Timetable|false
    {
        $arr = [];
        foreach (self::ARR_KEY_DAY as $key)
        {
            if (isset($info[$key]) && is_array($info[$key]) &&
                ($day = Day::ConstructDayByArray($info[$key], self::keyFrTraduction($key))))
                $arr[] = $day;
        }
        if (empty($arr))
            return false;
        else
            return new Timetable($arr);
    }

    public static function keyFrTraduction($key): string
    {
        return match ($key) {
            self::KEY_MONDAY => 'Lundi',
            self::KEY_TUESDAY => 'Mardi',
            self::KEY_WEDNESDAY => 'Mercredi',
            self::KEY_THURSDAY => 'Jeudi',
            self::KEY_FRIDAY => 'vendredi',
            self::KEY_SATURDAY => 'Samedi',
            self::KEY_SUNDAY => 'Dimanche',
            default => 'Unknown'
        };
    }

}