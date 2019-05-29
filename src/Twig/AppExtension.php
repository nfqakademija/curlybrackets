<?php

namespace App\Twig;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('until', [$this, 'formatDate']),
        ];
    }

    public function formatDate(\DateTime $dateTime)
    {
        Carbon::setLocale('lt');
        return Carbon::parse($dateTime)->diffForHumans();
    }
}