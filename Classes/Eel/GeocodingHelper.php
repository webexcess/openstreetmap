<?php

namespace WebExcess\OpenStreetMap\Eel;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use WebExcess\OpenStreetMap\Service\GeocodingService;

class GeocodingHelper implements ProtectedContextAwareInterface
{

    /**
     * @Flow\Inject
     * @var GeocodingService
     */
    protected $geocodingService;

    /**
     * @param $address
     * @return array|bool
     * @throws \Neos\Flow\Http\Client\InfiniteRedirectionException
     */
    public function latLonFromAddress($address)
    {
        return $this->geocodingService->geocodeLatLonFromAddress($address);
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
