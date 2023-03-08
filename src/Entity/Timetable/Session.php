<?php

namespace App\Entity\Timetable;

class Session
{
    private Moment $start;
    private Moment $end;

    private const KEY_OPEN = 'open';
    private const KEY_CLOSED = 'closed';

    public function __construct(Moment $start, Moment $end)
    {
        $this->end = $end;
        $this->start = $start;
    }

    public static function ConstructSessionByArray(array $info): Session | false
    {
        if (!isset($info[self::KEY_OPEN]) || !is_string($info[self::KEY_OPEN]) ||
                !($mom_op = Moment::ConstructSessionByString($info[self::KEY_OPEN])) ||
            !isset($info[self::KEY_CLOSED]) || !is_string($info[self::KEY_CLOSED]) ||
                !($mom_cl = Moment::ConstructSessionByString($info[self::KEY_CLOSED])))
            return false;
        return new Session($mom_op, $mom_cl);
    }

    /**
     * @return Moment
     */
    public function getEnd(): Moment
    {
        return $this->end;
    }

    /**
     * @return Moment
     */
    public function getStart(): Moment
    {
        return $this->start;
    }

    /**
     * @param Moment $end
     */
    public function setEnd(Moment $end): void
    {
        $this->end = $end;
    }

    /**
     * @param Moment $start
     */
    public function setStart(Moment $start): void
    {
        $this->start = $start;
    }

    public function getSessionToString(string $separator = "-", string $separator_moment = 'h'): string
    {
        return  $this->start->getMomentToString($separator_moment) . $separator . $this->end->getMomentToString($separator_moment);
    }
}