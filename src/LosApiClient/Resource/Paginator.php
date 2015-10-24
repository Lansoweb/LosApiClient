<?php
namespace LosApiClient\Resource;

final class Paginator
{

    const PAGE_COUNT  = 'page_count';
    const PAGE_SIZE   = 'page_size';
    const TOTAL_ITEMS = 'total_items';
    const PAGE        = 'page';

    private $pageSize   = 0;
    private $pageCount  = 0;
    private $totalItems = 0;
    private $page       = 1;

    public function __construct(array $data = null)
    {
        if (! empty($data)) {
            $this->pageCount  = (int) array_key_exists(self::PAGE_COUNT, $data) ? $data[self::PAGE_COUNT] : 0;
            $this->pageSize   = (int) array_key_exists(self::PAGE_SIZE, $data) ? $data[self::PAGE_SIZE] : 0;
            $this->totalItems = (int) array_key_exists(self::TOTAL_ITEMS, $data) ? $data[self::TOTAL_ITEMS] : 0;
            $this->page       = (int) array_key_exists(self::PAGE, $data) ? $data[self::PAGE] : 1;
        }
    }

    public function setPageSize($input)
    {
        $input = (int) $input;
        $this->pageSize = $input;

        return $this;
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function setPageCount($input)
    {
        $input = (int) $input;
        $this->pageCount = $input;

        return $this;
    }

    public function getPageCount()
    {
        return $this->pageCount;
    }

    public function setTotalItems($input)
    {
        $input = (int) $input;
        $this->totalItems = $input;

        return $this;
    }

    public function getTotalItems()
    {
        return $this->totalItems;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPage($page)
    {
        $this->page = (int) $page;
        return $this;
    }

    public function hasMorePages()
    {
        return $this->page < $this->pageCount;
    }

    public function getNextPage()
    {
        return $this->page++;
    }

}
