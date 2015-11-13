<?php
namespace LosApiClient\Resource;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-11-10 at 18:27:24.
 */
class PaginatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Paginator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Paginator([
            Paginator::PAGE => 1,
            Paginator::PAGE_COUNT => 2,
            Paginator::PAGE_SIZE => 10,
            Paginator::TOTAL_ITEMS => 18,
        ]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers LosApiClient\Resource\Paginator::__construct
     */
    public function testConstructor()
    {
        $this->assertSame(1, $this->object->getPage());
        $this->assertSame(2, $this->object->getPageCount());
        $this->assertSame(10, $this->object->getPageSize());
        $this->assertSame(18, $this->object->getTotalItems());
    }

    /**
     * @covers LosApiClient\Resource\Paginator::setPageSize
     * @covers LosApiClient\Resource\Paginator::getPageSize
     */
    public function testSetGetPageSize()
    {
        $this->assertSame($this->object, $this->object->setPageSize(15));
        $this->assertSame(15, $this->object->getPageSize());
    }

    /**
     * @covers LosApiClient\Resource\Paginator::setPageCount
     * @covers LosApiClient\Resource\Paginator::getPageCount
     */
    public function testSetGetPageCount()
    {
        $this->assertSame($this->object, $this->object->setPageCount(15));
        $this->assertSame(15, $this->object->getPageCount());
    }

    /**
     * @covers LosApiClient\Resource\Paginator::setTotalItems
     * @covers LosApiClient\Resource\Paginator::getTotalItems
     */
    public function testSetGetTotalItems()
    {
        $this->assertSame($this->object, $this->object->setTotalItems(15));
        $this->assertSame(15, $this->object->getTotalItems());
    }

    /**
     * @covers LosApiClient\Resource\Paginator::getPage
     * @covers LosApiClient\Resource\Paginator::setPage
     */
    public function testGetPage()
    {
        $this->assertSame($this->object, $this->object->setPage(15));
        $this->assertSame(15, $this->object->getPage());
    }

    /**
     * @covers LosApiClient\Resource\Paginator::hasMorePages
     */
    public function testHasMorePages()
    {
        $this->object->setPageCount(10);
        $this->object->setPage(1);
        $this->assertTrue($this->object->hasMorePages());
    }

    /**
     * @covers LosApiClient\Resource\Paginator::getNextPage
     */
    public function testGetNextPage()
    {
        $this->object->setPage(1);
        $this->assertSame(2, $this->object->getNextPage());
    }
}