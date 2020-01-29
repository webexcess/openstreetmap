<?php

namespace WebExcess\OpenStreetMap\Service;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;

/**
 * @Flow\Scope("singleton")
 */
class GeocodingService
{

    /**
     * @param NodeInterface $node
     * @param $propertyName
     * @param $oldValue
     * @param $value
     * @throws \Neos\Flow\Http\Client\InfiniteRedirectionException
     */
    public function nodePropertyChanged(NodeInterface $node, $propertyName, $oldValue, $value)
    {
        if ($node->getNodeType()->isOfType('WebExcess.OpenStreetMap:Map') && $propertyName == 'address') {
            $node->setProperty('lat', '');
            $node->setProperty('lon', '');

            if (!empty($value)) {
                $latLon = $this->geocodeLatLonFromAddress($value);
                if ($latLon) {
                    $node->setProperty('lat', $latLon['lat']);
                    $node->setProperty('lon', $latLon['lon']);
                }
            }
        }
    }

    /**
     * @param $address
     * @return array|bool
     * @throws \Neos\Flow\Http\Client\InfiniteRedirectionException
     */
    public function geocodeLatLonFromAddress($address)
    {
        $url = 'https://nominatim.openstreetmap.org/search?q=' . urlencode($address) . '&limit=1&format=json&addressdetails=1';

        $browser = new Browser();
        $browser->setRequestEngine(new CurlEngine());
        $response = $browser->request($url);
        $jsonContent = $response->getBody();

        if ($jsonContent) {
            $json = json_decode($jsonContent, true);
            if (isset($json[0]) && isset($json[0]['lat']) && isset($json[0]['lon'])) {
                return [
                    'lat' => $json[0]['lat'],
                    'lon' => $json[0]['lon'],
                ];
            }
        }

        return false;
    }

}
