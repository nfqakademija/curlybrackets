<?php

namespace App\Twig;

use Carbon\Carbon;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class AppExtension
 *
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @return array|TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('until', [$this, 'formatDate']),
        ];
    }

    /**
     * @param DateTime $dateTime
     * @return string
     */
    public function formatDate(DateTime $dateTime): string
    {
        Carbon::setLocale('lt');
        return Carbon::parse($dateTime)->diffForHumans();
    }
}
