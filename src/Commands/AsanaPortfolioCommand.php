<?php
namespace CarloNicora\Minimalism\Services\Asana\Commands;

use CarloNicora\Minimalism\Services\Asana\Abstracts\AbstractAsanaCommand;
use CarloNicora\Minimalism\Services\Asana\Data\AsanaPortfolio;
use stdClass;

class AsanaPortfolioCommand extends AbstractAsanaCommand
{
    /**
     * @param string $portfolioId
     * @return stdClass
     */
    public function loadPortfolio(
        string $portfolioId,
    ): stdClass
    {
        /** @var stdClass $response */
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $response = $this->client->portfolios->getPortfolio(
            portfolio_gid: $portfolioId,
        );

        return $response;
    }

    /**
     * @param string $portfolioId
     * @return AsanaPortfolio
     */
    public function getPortfolio(
        string $portfolioId,
    ): AsanaPortfolio
    {
        return $this->factory->create(
            type: AsanaPortfolio::class,
            data: $this->client->portfolios->getPortfolio(
                portfolio_gid: $portfolioId,
            ),
        );
    }
}