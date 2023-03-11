<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\getTimetableExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class getTimetableExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getTimetable', [getTimetableExtensionRuntime::class, 'getTimetable']),
            new TwigFunction('getDayTraduc', [getTimetableExtensionRuntime::class, 'getDayTraduc']),
        ];
    }
}
