<?php

namespace App\Entity\Timetable;

class Day
{
    public const KEY_DAY = 'day';
    public const KEY_NIGHT = 'night';
    public const ARR_KEY = [self::KEY_DAY, self::KEY_NIGHT];
    private const CLOSED_KEY = 'closed';

    public const KEY_TYPE_DAY = 1;
    public const KEY_TYPE_NIGHT = 2;


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
            if ($key === self::KEY_DAY)
                $type = self::KEY_TYPE_DAY;
            else
                $type = self::KEY_TYPE_NIGHT;
            if (is_array($info[$key]) && ($sess = Session::ConstructSessionByArray($info[$key], $type)))
                $arr[$key] = $sess;
            elseif ($info[$key] == self::CLOSED_KEY)
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
    public function getName(bool $traduc = false): string
    {
        if ($traduc)
            return Timetable::keyFrTraduction($this->name);
        else
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

    public function getSessionByName(string $name): Session | null
    {
        if ($name === self::KEY_DAY)
            return $this->getDay();
        elseif (self::KEY_NIGHT)
            return $this->getNight();
        else
            return null;
    }

    public function getSessionByKeyType(int $type): Session | null
    {
        if ($type === self::KEY_TYPE_DAY)
            return $this->getDay();
        elseif ($type === self::KEY_TYPE_NIGHT)
            return $this->getNight();
        else
            return null;
    }

    public function getAsArray(): array
    {
        return [
            self::KEY_DAY => $this->getDay() ? $this->getDay()->getAsArray() : self::CLOSED_KEY,
            self::KEY_NIGHT => $this->getNight() ? $this->getNight()->getAsArray() : self::CLOSED_KEY
        ];
    }

    public function getNextSessionDayFromDateTime(\DateTime $dateTime): Session | false
    {
        if (($day = $this->getDay()) && ($hour = $day->getStart()->getMomentToDateTime($dateTime)))
        {
            if ($hour > $dateTime)
                return $day;
        }
        return false;
    }

    public function getNextSessionNightFromDateTime(\DateTime $dateTime): Session | false
    {
        if (($night = $this->getNight()) && ($hour = $night->getStart()->getMomentToDateTime($dateTime)))
        {
            if ($hour > $dateTime)
                return $night;
        }
        return false;
    }
}