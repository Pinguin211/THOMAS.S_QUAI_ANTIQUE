<?php

namespace App\Entity\Timetable;

class Day
{
    private const KEY_DAY = 'day';
    private const KEY_NIGHT = 'night';
    private const ARR_KEY = [self::KEY_DAY, self::KEY_NIGHT];

    private string $name;
    private Session|null $day;
    private Session|null $night;

    public function __construct(string $name, Session|null $day, Session|null $night)
    {
        $this->day = $day;
        $this->name = $name;
        $this->night = $night;
    }


    public static function ConstructDayByArray(array $info, string $name): Day | false
    {
        if (!isset($info[self::KEY_DAY]) || !isset($info[self::KEY_NIGHT]))
            return false;
        $arr = [];
        foreach (self::ARR_KEY as $key) {
            if (is_array($info[$key]) && ($sess = Session::ConstructSessionByArray($info[$key])))
                $arr[$key] = $sess;
            elseif ($info[$key] == 'close')
                $arr[$key] = NULL;
            else
                return false;
        }
        return new Day($name, $arr[self::KEY_DAY], $arr[self::KEY_NIGHT]);
    }

    /**
     * @param Session|null $day
     */
    public function setDay(?Session $day): void
    {
        $this->day = $day;
    }

    /**
     * @param Session|null $night
     */
    public function setNight(?Session $night): void
    {
        $this->night = $night;
    }

    /**
     * @return Session|null
     */
    public function getDay(): ?Session
    {
        return $this->day;
    }

    /**
     * @return Session|null
     */
    public function getNight(): ?Session
    {
        return $this->night;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDaySessionString(string $closed_word = 'Fermé', string $separator_session = "-", string $separator_moment = 'h'): string
    {
        return $this->getSessionString(self::KEY_DAY, $closed_word, $separator_session, $separator_moment);
    }

    public function getNightSessionString(string $closed_word = 'Fermé', string $separator_session = "-", string $separator_moment = 'h'): string
    {
        return $this->getSessionString(self::KEY_NIGHT, $closed_word, $separator_session, $separator_moment);
    }

    private function getSessionString(string $key, string $closed_word = 'Fermé', string $separator_session = "-", string $separator_moment = 'h'): string
    {
        if ($key === self::KEY_DAY)
            $session = $this->day;
        else
            $session = $this->night;
        return $session ? $session->getSessionToString($separator_session, $separator_moment) : $closed_word;
    }

}