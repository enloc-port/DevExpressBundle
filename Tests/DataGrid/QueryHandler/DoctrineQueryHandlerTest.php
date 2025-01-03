<?php

namespace Bilendi\DevExpressBundle\DataGrid\QueryHandler;

use Bilendi\DevExpressBundle\DataGrid\Expression\ComparisonExpression;
use Bilendi\DevExpressBundle\DataGrid\Expression\EmptyExpression;
use Bilendi\DevExpressBundle\DataGrid\Search\SearchQuery;
use Bilendi\DevExpressBundle\DataGrid\Search\SearchSort;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison as DoctrineComparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class DoctrineQueryHandlerTest extends TestCase
{
    public function testTransformField()
    {
        $config = $this->getConfigMock();
        $config->expects($this->once())
                ->method('mapField');
        $handler = new DoctrineQueryHandler($config, $this->getQBMock(), $this->getQueryMock());
        $handler->transformField('pouet');
    }

    public function testTransformFieldInsensitiveCase()
    {
        $config = $this->getConfigMock();
        $handler = new DoctrineQueryHandler($config, $this->getQBMock(), $this->getQueryMock());
        $this->assertEquals('LOWER(pouet)', $handler->transformFieldCase('pouet', 'coucou'));
    }

    public function testTransformFieldSensitiveCase()
    {
        $config = $this->getConfigMock();
        $config->method('isCaseSensitive')->willReturn(true);
        $handler = new DoctrineQueryHandler($config, $this->getQBMock(), $this->getQueryMock());
        $this->assertEquals('pouet', $handler->transformFieldCase('pouet', 'coucou'));
    }

    public function testTransformValueInsensitiveCase()
    {
        $config = $this->getConfigMock();
        $config->method('isCaseSensitive')->willReturn(false);
        $handler = new DoctrineQueryHandler($config, $this->getQBMock(), $this->getQueryMock());
        $value = $handler->transformValueCase('CouCou');
        $this->assertEquals('coucou', $value);
    }

    public function testTransformValueSensitiveCase()
    {
        $config = $this->getConfigMock();
        $config->method('isCaseSensitive')->willReturn(true);
        $handler = new DoctrineQueryHandler($config, $this->getQBMock(), $this->getQueryMock());
        $value = $handler->transformValueCase('CouCou');
        $this->assertEquals('CouCou', $value);
    }

    public function testAddFilters()
    {
        $config = $this->getConfigMock();
        $config->expects($this->once())
            ->method('mapField')
            ->willReturn('lol');

        $query = $this->getQueryMock();
        $query->expects($this->once())
            ->method('getFilter')
            ->willReturn(new ComparisonExpression('lol', '>', 'haha'));

        $qb = $this->getQBMock();
        $qb->expects($this->once())
            ->method('andWhere')
            ->with(new Expr\Comparison('LOWER(lol)', DoctrineComparison::GT, ':p0'));

        $qb->expects($this->once())
            ->method('setParameter')
            ->with('p0', 'haha');
        $handler = new DoctrineQueryHandler($config, $qb, $query);
        $handler->addFilters();
    }

    public function testAddFiltersWithDefaultFilters()
    {
        $config = $this->getConfigMockWithDefaultFilters();
        $config->expects($this->once())
            ->method('mapField')
            ->willReturn('coucou');

        $query = $this->getQueryMock();
        $query->expects($this->once())
            ->method('getFilter')
            ->willReturn(new EmptyExpression());

        $qb = $this->getQBMock();
        $qb->expects($this->once())
            ->method('andWhere')
            ->with(new Expr\Comparison('coucou', DoctrineComparison::GT, ':p0'));

        $qb->expects($this->once())
            ->method('setParameter')
            ->with('p0', 3);
        $handler = new DoctrineQueryHandler($config, $qb, $query);
        $handler->addFilters();
    }

    public function testAddSorting()
    {
        $config = $this->getConfigMock();
        $config->expects( $this->exactly( 2 ) )
            ->method('mapField')
            ->willReturnOnConsecutiveCalls( 'lol', 'haha');

        $query = $this->getQueryMock();
        $query->expects($this->once())
                ->method('getSort')
                ->willReturn([
                    new SearchSort('lol', true),
                    new SearchSort('haha', false),
                ]);

        $qb = $this->getQBMock();
        $qb->expects($this->exactly( 2 ))
            ->method('addOrderBy')
            ->willReturn([
                new SearchSort('lol', true),
                new SearchSort('haha', false),
            ]);
        $handler = new DoctrineQueryHandler($config, $qb, $query);
        $handler->addSorting();
    }

    public function testAddPagination()
    {
        $qb = $this->getQBMock();
        $qb->method('setFirstResult')
            ->with(1);
        $qb->method('setMaxResults')
            ->with(2);

        $query = $this->getQueryMock();
        $query->expects($this->once())
            ->method('getStartIndex')
            ->willReturn(1);
        $query->expects($this->once())
            ->method('getMaxResults')
            ->willReturn(2);
        $handler = new DoctrineQueryHandler($this->getConfigMock(), $qb, $query);
        $handler->addPagination();
    }

    protected function getConfigMock()
    {
        return $this->getMockBuilder(DoctrineQueryConfig::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    protected function getConfigMockWithDefaultFilters()
    {
        $mock = $this->getMockBuilder(DoctrineQueryConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mock->method('getDefaultFilters')
             ->willReturn([new ComparisonExpression('coucou', '>', 3)]);

        return $mock;
    }

    protected function getQBMock()
    {
        return $this->getMockBuilder(QueryBuilder::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    protected function getQueryMock()
    {
        return $this->getMockBuilder(SearchQuery::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
