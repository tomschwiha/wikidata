<?php

namespace Wikidata\Tests;

use Exception;
use Wikidata\Wikidata;
use PHPUnit\Framework\TestCase;

class WikidataTest extends TestCase 
{
  protected $wikidata;

  public function setUp()
  {
    $this->wikidata = new Wikidata();
  }

  public function testSearchByTerm() 
  {
    $results = $this->wikidata->search('London');

    $this->assertInstanceOf('Illuminate\Support\Collection', $results);

    $result = $results->first();

    $this->assertInstanceOf('Wikidata\SearchResult', $result);
  }

  public function testSearchOnAnotherLanguage() 
  {
    $results = $this->wikidata->search('London', 'fr');

    $this->assertEquals('fr', $results->first()->lang);
  }

  public function testSearchWithLimit() 
  {
    $results = $this->wikidata->search('car', 'en', 10);

    $this->assertEquals(10, $results->count());
  }

  public function testSearchResultsCouldBeEmpty() 
  {
    $results = $this->wikidata->search('asdfgh');

    $this->assertInstanceOf('Illuminate\Support\Collection', $results);

    $this->assertEquals(true, $results->isEmpty());
  }

  public function testSearchByPropertyIdAndValue() 
  {
    $results = $this->wikidata->searchBy('P646', '/m/02mjmr');

    $this->assertInstanceOf('Illuminate\Support\Collection', $results);

    $result = $results->first();

    $this->assertInstanceOf('Wikidata\SearchResult', $result);
  }

  public function testSearchByThrowExceptionIfSecondPropertyMissing() 
  {
    $this->expectException(Exception::class);

    $this->wikidata->searchBy('P646');
  }

  public function testSearchByThrowExceptionIfPropertyIdInvalid() 
  {
    $this->expectException(Exception::class);

    $this->wikidata->searchBy('Pasd', '/m/02mjmr');
  }

  public function testSearchByPropertyIdAndEntityId() 
  {
    $results = $this->wikidata->searchBy('P39', 'Q11696');

    $this->assertInstanceOf('Illuminate\Support\Collection', $results);

    $result = $results->first();

    $this->assertInstanceOf('Wikidata\SearchResult', $result);
  }

  public function testGetEntityById() 
  {
    $entity = $this->wikidata->get('Q44077');

    $this->assertInstanceOf('Wikidata\Entity', $entity);
  }

  public function testGetEntityOnAnotherLanguage() 
  {
    $entity = $this->wikidata->get('Q44077', 'es');

    $this->assertEquals('es', $entity->lang);
  }

  public function testGetEntityThrowExceptionIfEntityIdInvalid() 
  {
    $this->expectException(Exception::class);

    $this->wikidata->get('P1234');
  }
}
