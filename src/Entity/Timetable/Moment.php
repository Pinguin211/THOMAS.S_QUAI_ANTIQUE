<?php

namespace App\Entity\Timetable;

class Moment
{
    private int $hours;
    private int $min;

    public function __construct(int $hours, int $min)
    {
        $this->hours = $hours;
        $this->min = $min;
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
        $this->hours = $hours;
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
        $this->min = $min;
    }

    public function getMomentToString(string $separator = 'h'): string
    {
        if ($this->hours === 0)
            $h = '00';
        else
            $h = $this->hours;
        if ($this->min === 0)
            $m = '00';
        else
            $m = $this->min;

        return $h . $separator . $m;
    }
}
