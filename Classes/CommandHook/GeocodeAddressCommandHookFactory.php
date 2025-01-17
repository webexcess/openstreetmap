<?php

namespace WebExcess\OpenStreetMap\CommandHook;

use Neos\ContentRepository\Core\CommandHandler\CommandHookInterface;
use Neos\ContentRepository\Core\Factory\CommandHookFactoryInterface;
use Neos\ContentRepository\Core\Factory\CommandHooksFactoryDependencies;
use WebExcess\OpenStreetMap\Service\GeocodingService;

class GeocodeAddressCommandHookFactory implements CommandHookFactoryInterface
{
    public function __construct(
        protected readonly GeocodingService $geocodingService,
    )
    {
    }

    public function build(CommandHooksFactoryDependencies $commandHooksFactoryDependencies): CommandHookInterface
    {
        return new GeocodeAddressCommandHook(
            $this->geocodingService,
            $commandHooksFactoryDependencies->contentGraphReadModel,
            $commandHooksFactoryDependencies->nodeTypeManager
        );
    }
}
