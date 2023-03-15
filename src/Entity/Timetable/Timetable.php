<?php

namespace App\Entity\Timetable;


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
    public const ARR_KEY_DAYS = [
        self::KEY_MONDAY, self::KEY_TUESDAY, self::KEY_WEDNESDAY, self::KEY_THURSDAY,
        self::KEY_FRIDAY, self::KEY_SATURDAY, self::KEY_SUNDAY
    ];

    /**
     * @var Day[] $day
     */
    private array $days;

    public function __construct(array $days)
    {
        $this->days = $days;
    }

    public static function ConstructWithArray(array $info): Timetable|false
    {
        $arr = [];
        foreach (self::ARR_KEY_DAYS as $key)
        {
            if (isset($info[$key]) && is_array($info[$key]) &&
                ($day = Day::ConstructDayByArray($info[$key], $key)))
                $arr[] = $day;
        }
        if (empty($arr))
            return false;
        else
            return new Timetable($arr);
    }

    public static function keyFrTraduction(string $key): string
    {
        return match ($key) {
            self::KEY_MONDAY => 'Lundi',
            self::KEY_TUESDAY => 'Mardi',
            self::KEY_WEDNESDAY => 'Mercredi',
            self::KEY_THURSDAY => 'Jeudi',
            self::KEY_FRIDAY => 'vendredi',
            self::KEY_SATURDAY => 'Samedi',
            self::KEY_SUNDAY => 'Dimanche',
            default => $key
        };
    }

    /**
     * @return Day[]
     */
    public function getDays(): array
    {
        return $this->days;
    }

    public function getDay(string $name): Day | false
    {
        foreach ($this->days as $day)
        {
            if ($day->getName() == $name)
                return $day;
        }
        return false;
    }

    public function addDay(Day $day)
    {
        $this->days[] = $day;
    }

    public function setDays(array $days)
    {
        $this->days = $days;
    }

    public function getAsArray(): array
    {
        $arr = [];
        foreach ($this->days as $day)
            $arr[$day->getName()] = $day->getAsArray();
        return $arr;
    }

    public function getDayFromDateTime(\DateTime $dateTime): Day | false
    {
        return $this->getDay(strtolower($dateTime->format('l')));
    }

    public function getHourFromStage(int $stage, int $session_key_type, \DateTime $date, int $interval = 15): \DateTime | false
    {
        if (!($day = $this->getDayFromDateTime($date)) ||
            !($sess = $day->getSessionByKeyType($session_key_type)))
            return false;
        $start = $sess->getStart()->getMomentToDateTime($date);
        $stage = abs($stage);
        while ($stage > 0)
        {
            $start->modify("+$interval minutes");
            $stage--;
        }
        return $start;
    }
}