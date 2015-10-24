<?php
namespace LosApiClient\Resource;

use Nocarrier\Hal;

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

    public function getData()
    {
        return $this->hal->getData();
    }

}
