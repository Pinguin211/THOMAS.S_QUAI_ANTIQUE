<?php

namespace App\Service;

use App\Entity\Timetable\Timetable;
use App\Kernel;
use App\Lib\JsonFile;

class InfoFileInterface
{
    private PathInterface $path;

    public function __construct(PathInterface $path)
    {
        $this->path = $path;
    }

    public function getTimetable(): Timetable|false
    {
        if (!($file = JsonFile::ConstructWithPath($this->path->getInfoFilePath())))
            return false;
        elseif (!($arr = $file->getInFile(Timetable::KEY_TIMETABLE)))
            return false;
        elseif (!is_array($arr))
            return false;
        return Timetable::ConstructWithArray($arr);
    }

    public function setTimetable(Timetable $timetable)
    {
        $json = JsonFile::ConstructWithPath($this->path->getInfoFilePath());
        if (!$json)
            return;
        $json->setInFile($timetable->getAsArray(), Timetable::KEY_TIMETABLE);
        $json->saveFile();

    }

}