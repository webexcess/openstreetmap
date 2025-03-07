<?php

namespace WebExcess\OpenStreetMap\CommandHook;

use Neos\ContentRepository\Core\CommandHandler\CommandHookInterface;
use Neos\ContentRepository\Core\CommandHandler\CommandInterface;
use Neos\ContentRepository\Core\CommandHandler\Commands;
use Neos\ContentRepository\Core\EventStore\PublishedEvents;
use Neos\ContentRepository\Core\Feature\NodeCreation\Command\CreateNodeAggregateWithNode;
use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\NodeType\NodeTypeManager;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\Projection\ContentGraph\ContentGraphReadModelInterface;
use Neos\Neos\Domain\Service\NeosVisibilityConstraints;
use WebExcess\OpenStreetMap\Service\GeocodingService;

class GeocodeAddressCommandHook implements CommandHookInterface
{
    protected const NODETYPE_NAME = 'WebExcess.OpenStreetMap:Map';

    public function __construct(
        protected readonly GeocodingService $geocodingService,
        protected readonly ContentGraphReadModelInterface $contentGraphReadModel,
        protected readonly NodeTypeManager $nodeTypeMananger
    )
    {
    }

    public function onBeforeHandle(CommandInterface $command): CommandInterface
    {
        return match (true) {
            $command instanceof SetNodeProperties => $this->handleSetNodeProperties($command),
            $command instanceof CreateNodeAggregateWithNode => $this->handleCreateNodeAggregateWithNode($command),
            default => $command
        };
    }

    public function onAfterHandle(CommandInterface $command, PublishedEvents $events): Commands
    {
        return Commands::createEmpty();
    }

    private function handleSetNodeProperties(SetNodeProperties $command): CommandInterface
    {
        $contentGraph = $this->contentGraphReadModel->getContentGraph($command->workspaceName);
        $subgraph = $contentGraph->getSubgraph($command->originDimensionSpacePoint->toDimensionSpacePoint(), NeosVisibilityConstraints::excludeRemoved());
        $node = $subgraph->findNodeById($command->nodeAggregateId);
        if ($node === null || !$this->nodeTypeMananger->getNodeType($node->nodeTypeName)->isOfType(NodeTypeName::fromString(self::NODETYPE_NAME))) {
            return $command;
        }

        $address = $command->propertyValues->values['address'] ?? null;
        if ($address) {
            $latLon = $this->geocodingService->geocodeLatLonFromAddress($address);

            return SetNodeProperties::create(
                $command->workspaceName,
                $command->nodeAggregateId,
                $command->originDimensionSpacePoint,
                $command->propertyValues
                    ->withValue('lat', $latLon['lat'] ?? null)
                    ->withValue('lon', $latLon['lon'] ?? null),
            );
        }

        return $command;
    }

    private function handleCreateNodeAggregateWithNode(CreateNodeAggregateWithNode $command)
    {
        if (!$this->nodeTypeMananger->getNodeType($command->nodeTypeName)->isOfType(NodeTypeName::fromString(self::NODETYPE_NAME))) {
            return $command;
        }
        $address = $command->initialPropertyValues->values['address'] ?? null;

        if ($address) {
            $latLon = $this->geocodingService->geocodeLatLonFromAddress($address);

            return $command->withInitialPropertyValues(
                $command->initialPropertyValues
                    ->withValue('lat', $latLon['lat'] ?? null)
                    ->withValue('lon', $latLon['lon'] ?? null)
            );
        }

        return $command;
    }


}
