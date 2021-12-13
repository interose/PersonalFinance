<?php

namespace App\Twig;

use App\Repository\CurrentBalanceRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CurrentBalanceExtension extends AbstractExtension
{
    private CurrentBalanceRepository $repository;

    /**
     * CurrentBalanceExtension constructor.
     *
     * @param CurrentBalanceRepository $repository
     */
    public function __construct(CurrentBalanceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_balance', [$this, 'getCurrentBalance']),
        ];
    }

    /**
     * @return string
     */
    public function getCurrentBalance(): string
    {
        $formatter = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->repository->getMainAccountBalance(), 'EUR');
    }
}
