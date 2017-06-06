<?php

namespace Zentrium\Bundle\MapBundle\Wmts;

use Http\Client\Common\HttpMethodsClient;
use RuntimeException;
use Sabre\Xml\Deserializer;
use Sabre\Xml\Reader;
use Sabre\Xml\Service;

class CapabilitiesParser
{
    /**
     * @internal
     */
    const XLINK_TAG = '{http://www.w3.org/1999/xlink}href';

    private static $localNamespaces = ['http://www.opengis.net/ows/1.1', 'http://www.opengis.net/wmts/1.0'];

    /**
     * @var HttpMethodsClient
     */
    private $httpClient;

    public function __construct(HttpMethodsClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->service = new Service();
        $this->service->elementMap = $this->buildElementMap();
    }

    public function parseFromUrl($url)
    {
        $response = $this->httpClient->get($url);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Could not download XML.');
        }

        return $this->parse((string) $response->getBody());
    }

    public function parseFromFile($path)
    {
        $xml = file_get_contents($path);
        if (!is_string($xml)) {
            throw new RuntimeException('Could not read file.');
        }

        return $this->parse($xml);
    }

    public function parse($xml)
    {
        return $this->service->parse($xml);
    }

    public function minify(array $capabilities, array $layers)
    {
        $minified = $capabilities;

        unset($minified['ServiceIdentification']['Keywords']);
        unset($minified['ServiceProvider']['ServiceContact']);
        unset($minified['Themes']);

        $usedTileMatrices = [];
        $minified['Contents']['Layer'] = array_values(array_filter($minified['Contents']['Layer'], function ($layer) use ($layers, &$usedTileMatrices) {
            if (in_array($layer['Identifier'], $layers)) {
                foreach ($layer['TileMatrixSetLink'] as $link) {
                    $usedTileMatrices[] = $link['TileMatrixSet'];
                }

                return true;
            } else {
                return false;
            }
        }));

        $minified['Contents']['TileMatrixSet'] = array_values(array_filter($minified['Contents']['TileMatrixSet'], function ($matrixSet) use ($usedTileMatrices) {
            return in_array($matrixSet['Identifier'], $usedTileMatrices);
        }));

        return $minified;
    }

    protected function buildElementMap()
    {
        return [
            '{http://www.opengis.net/ows/1.1}HTTP' => [$this, 'parseKeyMultiValue'],
            '{http://www.opengis.net/wmts/1.0}Capabilities' => [$this, 'parseCapabilities'],
            '{http://www.opengis.net/wmts/1.0}ServiceMetadataURL' => [$this, 'parseLink'],
            '{http://www.opengis.net/ows/1.1}ServiceIdentification' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}ServiceProvider' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}ProviderSite' => [$this, 'parseLink'],
            '{http://www.opengis.net/ows/1.1}ServiceContact' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}ContactInfo' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}Phone' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}Address' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}OperationsMetadata' => [$this, 'parseOperations'],
            '{http://www.opengis.net/ows/1.1}Operation' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}DCP' => [$this, 'parseKeyValue'],
            '{http://www.opengis.net/ows/1.1}Get' => [$this, 'parseOperation'],
            '{http://www.opengis.net/ows/1.1}Constraint' => [$this, 'parseConstraint'],
            '{http://www.opengis.net/ows/1.1}AllowedValues' => [$this, 'parseKeyMultiValue'],
            '{http://www.opengis.net/wmts/1.0}Contents' => [$this, 'parseKeyMultiValue'],
            '{http://www.opengis.net/wmts/1.0}Layer' => [$this, 'parseLayer'],
            '{http://www.opengis.net/wmts/1.0}Style' => [$this, 'parseStyle'],
            '{http://www.opengis.net/wmts/1.0}Dimension' => [$this, 'parseDimension'],
            '{http://www.opengis.net/ows/1.1}WGS84BoundingBox' => [$this, 'parseBoundingBox'],
            '{http://www.opengis.net/ows/1.1}BoundingBox' => [$this, 'parseBoundingBox'],
            '{http://www.opengis.net/wmts/1.0}TileMatrixSetLink' => [$this, 'parseTileMatrixSetLink'],
            '{http://www.opengis.net/wmts/1.0}ResourceURL' => [$this, 'parseAttributes'],
            '{http://www.opengis.net/wmts/1.0}TileMatrixSet' => [$this, 'parseTileMatrixSet'],
            '{http://www.opengis.net/wmts/1.0}TileMatrix' => [$this, 'parseTileMatrix'],
            '{http://www.opengis.net/wmts/1.0}Themes' => [$this, 'parseKeyMultiValue'],
            '{http://www.opengis.net/wmts/1.0}Theme' => [$this, 'parseTheme'],
            '{http://www.opengis.net/ows/1.1}Keywords' => [$this, 'parseKeywords'],
        ];
    }

    /**
     * @internal
     */
    public function parseKeyValue(Reader $reader)
    {
        if ($reader->isEmptyElement) {
            $reader->next();

            return [];
        }
        $values = [];
        $reader->read();
        do {
            if ($reader->nodeType === Reader::ELEMENT) {
                if (in_array($reader->namespaceURI, self::$localNamespaces)) {
                    $values[$reader->localName] = $reader->parseCurrentElement()['value'];
                } else {
                    $clark = $reader->getClark();
                    $values[$clark] = $reader->parseCurrentElement()['value'];
                }
            } else {
                $reader->read();
            }
        } while ($reader->nodeType !== Reader::END_ELEMENT);
        $reader->read();

        return $values;
    }

    /**
     * @internal
     */
    public function parseKeyMultiValue(Reader $reader)
    {
        if ($reader->isEmptyElement) {
            $reader->next();

            return [];
        }
        $values = [];
        $reader->read();
        do {
            if ($reader->nodeType === Reader::ELEMENT) {
                if (in_array($reader->namespaceURI, self::$localNamespaces)) {
                    $key = $reader->localName;
                } else {
                    $key = $reader->getClark();
                }
                if (!isset($values[$key])) {
                    $values[$key] = [];
                }
                $values[$key][] = $reader->parseCurrentElement()['value'];
            } else {
                $reader->read();
            }
        } while ($reader->nodeType !== Reader::END_ELEMENT);
        $reader->read();

        return $values;
    }

    /**
     * @internal
     */
    public function parseAttributes(Reader $reader)
    {
        $attributes = $reader->parseAttributes();
        $reader->next();

        return $attributes;
    }

    /**
     * @internal
     */
    public function parseWithAttributes(Reader $reader, $parser, $attributeMap)
    {
        $attributes = $reader->parseAttributes();
        $result = call_user_func($parser, $reader);

        foreach ($attributeMap as $attribute => $key) {
            if (isset($attributes[$attribute])) {
                $result[$key] = $attributes[$attribute];
            }
        }

        return $result;
    }

    /**
     * @internal
     */
    public function parseLink(Reader $reader)
    {
        $attributes = $reader->parseAttributes();
        $reader->next();

        if (!isset($attributes[self::XLINK_TAG])) {
            return null;
        }

        return $attributes[self::XLINK_TAG];
    }

    /**
     * @internal
     */
    public function parseCapabilities(Reader $reader)
    {
        return $this->parseWithAttributes($reader, [$this, 'parseKeyValue'], ['version' => 'version']);
    }

    /**
     * @internal
     */
    public function parseOperations(Reader $reader)
    {
        $tree = $reader->parseInnerTree();
        $result = [];
        foreach ($tree as $node) {
            if (!isset($node['name']) || $node['name'] !== '{http://www.opengis.net/ows/1.1}Operation' || !isset($node['attributes']['name'])) {
                continue;
            }
            $result[$node['attributes']['name']] = $node['value'];
        }

        return $result;
    }

    /**
     * @internal
     */
    public function parseOperation(Reader $reader)
    {
        return $this->parseWithAttributes($reader, [$this, 'parseKeyMultiValue'], [self::XLINK_TAG => 'href']);
    }

    /**
     * @internal
     */
    public function parseConstraint(Reader $reader)
    {
        return $this->parseWithAttributes($reader, [$this, 'parseKeyValue'], ['name' => 'name']);
    }

    /**
     * @internal
     */
    public function parseLayer(Reader $reader)
    {
        $result = $this->parseKeyMultiValue($reader);

        return $this->takeFirst($result, ['Identifier', 'Title', 'Abstract', 'WGS84BoundingBox']);
    }

    /**
     * @internal
     */
    public function parseDimension(Reader $reader)
    {
        $result = $this->parseKeyMultiValue($reader);

        return $this->takeFirst($result, ['Identifier', 'Default']);
    }

    /**
     * @internal
     */
    public function parseTheme(Reader $reader)
    {
        $result = $this->parseKeyMultiValue($reader);

        return $this->takeFirst($result, ['Identifier', 'Title', 'Abstract']);
    }

    /**
     * @internal
     */
    public function parseBoundingBox(Reader $reader)
    {
        $result = $this->parseKeyValue($reader);
        if (!isset($result['LowerCorner']) || !isset($result['UpperCorner'])) {
            return null;
        }
        $values = array_map('doubleval', array_merge(explode(' ', $result['LowerCorner']), explode(' ', $result['UpperCorner'])));
        if (count($values) != 4) {
            return null;
        }

        return $values;
    }

    /**
     * @internal
     */
    public function parseStyle(Reader $reader)
    {
        $result = $this->parseWithAttributes($reader, [$this, 'parseKeyMultiValue'], ['isDefault' => 'isDefault']);
        $result = $this->takeFirst($result, ['Title', 'Identifier']);
        if (isset($result['isDefault'])) {
            $result['isDefault'] = ($result['isDefault'] === 'true');
        }

        return $result;
    }

    /**
     * @internal
     */
    public function parseTileMatrixSetLink(Reader $reader)
    {
        $result = $reader->readText();
        $reader->next();

        return ['TileMatrixSet' => $result];
    }

    /**
     * @internal
     */
    public function parseTileMatrixSet(Reader $reader)
    {
        $result = $this->parseKeyMultiValue($reader);
        foreach ($result as $key => &$value) {
            if ($key != 'TileMatrix') {
                $value = $value[0];
            }
        }
        unset($value);

        return $result;
    }

    /**
     * @internal
     */
    public function parseTileMatrix(Reader $reader)
    {
        $result = $this->parseKeyValue($reader);
        $doubles = ['ScaleDenominator', 'TileWidth', 'TileHeight', 'MatrixWidth', 'MatrixHeight'];
        $doubleLists = ['TopLeftCorner'];
        foreach ($result as $key => &$value) {
            if (in_array($key, $doubles)) {
                $value = floatval($value);
            } elseif (in_array($key, $doubleLists)) {
                $value = array_map('doubleval', explode(' ', $value));
            }
        }
        unset($value);

        return $result;
    }

    /**
     * @internal
     */
    public function parseKeywords(Reader $reader)
    {
        return Deserializer\repeatingElements($reader, '{http://www.opengis.net/ows/1.1}Keyword');
    }

    private function takeFirst($map, $keys)
    {
        foreach ($keys as $key) {
            if (isset($map[$key])) {
                $map[$key] = $map[$key][0];
            }
        }

        return $map;
    }
}
