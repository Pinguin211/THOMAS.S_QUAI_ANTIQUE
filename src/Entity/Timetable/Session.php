<?php

namespace App\Entity\Timetable;

class Session
{
    private Moment $start;
    private Moment $end;
    private int $type;

    public const KEY_OPEN = 'open';
    public const KEY_CLOSED = 'closed';



    public const ARR_KEY = [self::KEY_OPEN, self::KEY_CLOSED];

    public function __construct(Moment $start, Moment $end, int $type)
    {
        $this->end = $end;
        $this->start = $start;
        $this->type = $type;
    }

    public static function ConstructSessionByArray(array $info, int $type): Session | false
    {
        if (!isset($info[self::KEY_OPEN]) || !is_string($info[self::KEY_OPEN]) ||
                !($mom_op = Moment::ConstructSessionByString($info[self::KEY_OPEN])) ||
            !isset($info[self::KEY_CLOSED]) || !is_string($info[self::KEY_CLOSED]) ||
                !($mom_cl = Moment::ConstructSessionByString($info[self::KEY_CLOSED])))
            return false;
        return new Session($mom_op, $mom_cl, $type);
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

    public function getOpenClosedByName(string $name)
    {
        if ($name === self::KEY_OPEN)
            return $this->getStart();
        else
            return $this->getEnd();
    }

    public function getAsArray(): array
    {
        return [
            self::KEY_OPEN => $this->getStart()->getMomentToString('.'),
            self::KEY_CLOSED => $this->getEnd()->getMomentToString('.')
        ];
    }

    public function getType(): int
    {
        return $this->type;
    }
}