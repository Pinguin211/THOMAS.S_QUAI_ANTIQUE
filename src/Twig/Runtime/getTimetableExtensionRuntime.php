<?php

namespace App\Twig\Runtime;

use App\Entity\Timetable\Timetable;
use App\Service\InfoFileInterface;
use Twig\Extension\RuntimeExtensionInterface;

class getTimetableExtensionRuntime implements RuntimeExtensionInterface
{
    private InfoFileInterface $infoFile;

    public function __construct(InfoFileInterface $infoFile)
    {
        $this->infoFile = $infoFile;
    }

    public function getTimetable(): Timetable | false
    {
        return $this->infoFile->getTimetable();
    }

    public function getDayTraduc(string $day): string
    {
        return Timetable::keyFrTraduction($day);
    }
}
