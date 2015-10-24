<?php
namespace LosApiClient\Resource;

use Nocarrier\Hal;
use LosApiClient\Exception\InvalidArgumentException;

class Resource
{
    private $hal;

    private $paginator;

    public function __construct(Hal $resource)
    {
        $this->hal = $resource;

        $this->setPaginator($resource->getData());
    }

    private function setUri($uri)
    {
        $this->hal->setUri($uri);
        return $this;
    }

    public function getUri()
    {
        return $this->hal->getUri();
    }

    public function getLinks()
    {
        return $this->hal->getLinks();
    }

    public function getLink($rel)
    {
        return $this->hal->getLink($rel);
    }

    private function setPaginator(array $input)
    {
        $this->paginator = new Paginator($input);

        return $this;
    }

    public function getPaginator()
    {
        return $this->paginator;
    }

    public function isCollection()
    {
        return (bool) ($this->paginator->getPageSize() > 0);
    }

    public function getData($includeResources = true)
    {
        return array_merge($this->hal->getData(), $this->getResources());
    }

    public function getFirstResource($rel)
    {
        $resource = $this->hal->getFirstResource($rel);
        if ($resource === null) {
            throw new InvalidArgumentException("Rel '$rel' not found.");
        }
        return $resource->getData();
    }

    public function getResources($filterRel = null)
    {
        $rels = $this->hal->getResources();
        $result = [];
        foreach ($rels as $rel => $resources) {
            if ($filterRel !== null && $rel != $filterRel) {
                continue;
            }
            foreach ($resources as $resource) {
                $result[$rel][] = $resource->getData();
            }
        }
        if ($filterRel !== null && array_key_exists($filterRel, $result)) {
            return $result[$filterRel];
        }
        return $result;
    }

}
