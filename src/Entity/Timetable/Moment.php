<?php

namespace App\Entity\Timetable;

class Moment
{
    private int $hours;
    private int $min;

    public function __construct(int $hours, int $min)
    {
        $this->hours = self::CorrectHours($hours);
        $this->min = self::CorrectMin($min);
    }

    public static function ConstructSessionByString(string $session): Moment | false
    {
        $arr = explode('.', $session);
        if (count($arr) !== 2)
            return false;
        else
            return new Moment((int)$arr[0], (int)$arr[1]);
    }

    /**
     * @param int $hours
     */
    public function setHours(int $hours): void
    {
        $this->hours = self::CorrectHours($hours);
    }

    /**
     * @return int
     */
    public function getHours(): int
    {
        return $this->hours;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin(int $min): void
    {
        $this->min = self::CorrectMin($min);
    }

    public function getMomentToString(string $separator = 'h'): string
    {
        if ($this->hours < 10)
            $h = '0' . $this->hours;
        else
            $h = $this->hours;
        if ($this->min < 10)
            $m = '0' . $this->min;
        else
            $m = $this->min;

        return $h . $separator . $m;
    }

    private static function CorrectHours(int $hours)
    {
        if ($hours > 23)
            return 23;
        if ($hours < 0)
            return 0;
        return $hours;
    }

    private static function CorrectMin(int $min)
    {
        if ($min > 59)
            return 59;
        if ($min < 0)
            return 0;
        return $min;
    }

    public function getMomentToDateTime(\DateTime $date): \DateTime | false
    {
        $str = $date->format('Y-m-d') . ' ' . $this->getMomentToString(':');
        return \DateTime::createFromFormat('Y-m-d H:i', $str);
    }
}
