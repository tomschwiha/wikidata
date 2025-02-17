<?php 

namespace Wikidata;

use Exception;
use GuzzleHttp\Client;
use Wikidata\Entity;
use Wikidata\SearchResult;
use Wikidata\SparqlClient;

class Wikidata {
	
    const API_ENDPOINT = 'https://www.wikidata.org/w/api.php';

    /**
     * Search entities by term
     * 
     * @param string $query
     * @param string $lang Language (default: en) 
     * @param string $limit Max count of returning items (default: 10)
     * 
     * @return \Illuminate\Support\Collection Return collection of \Wikidata\SearchResult
     */
    public function search($query, $lang = 'en', $limit = 10) 
    {    
        $client = new Client(); 

        $response = $client->get(self::API_ENDPOINT, [
            'query' => [
                'action' => 'wbsearchentities',
                'format' => 'json',
                'strictlanguage' => true,
                'language' => $lang,
                'uselang' => $lang,
                'search' => $query,
                'limit' => $limit,
                'props' => ''
            ]
        ]);

        $results = json_decode($response->getBody(), true);

        $data = isset($results['search']) ? $results['search'] : [];

        $collection = collect($data);

        $output = $collection->map(function($item) use ($lang) {
            return new SearchResult($item, $lang, 'api');
        });

        return $output;
    }

    /**
     * Search entities by property ID and it value
     * 
     * @param string $property Wikidata ID of property (e.g.: P646)
     * @param string $value String value of property or Wikidata entity ID (e.g.: Q11696)
     * @param string $lang Language (default: en)
     * @param string $limit Max count of returning items (default: 10)
     * 
     * @return \Illuminate\Support\Collection Return collection of \Wikidata\SearchResult
     */
    public function searchBy($property, $value = null, $lang = 'en', $limit = 10) 
    {
        if(!is_pid($property)) {
            throw new Exception("First argument in searchBy() must be a valid Wikidata property ID (e.g.: P646).", 1);
        }

        if(!$value) {
            throw new Exception("Second argument in searchBy() must be a string or a valid Wikidata entity ID (e.g.: Q646).", 1);
        }

        $subject = is_qid($value) ? 'wd:'.$value : '"'.$value.'"';

        $query = '
            SELECT ?item ?itemLabel ?itemAltLabel ?itemDescription WHERE {
                ?item wdt:'.$property.' '.$subject.'.
                SERVICE wikibase:label {
                    bd:serviceParam wikibase:language "'. $lang .'".
                }
            } LIMIT '.$limit.'
        ';

        $client = new SparqlClient();

        $data = $client->execute( $query ); 

        $collection = collect($data);

        $output = $collection->map(function($item) use ($lang) {
            return new SearchResult($item, $lang, 'sparql');
        });

        return $output;
    }

    /**
     * Get entity by ID
     * 
     * @param string $entityId Wikidata entity ID (e.g.: Q11696)
     * @param string $lang Language
     * 
     * @return \Wikidata\Entity Return entity
     */
    public function get($entityId, $lang = 'en') 
    {
        if(!is_qid($entityId)) {
            throw new Exception("First argument in get() must by a valid Wikidata entity ID (e.g.: Q646).", 1);
        }

        $query = '
            SELECT ?item ?itemLabel ?itemDescription ?itemAltLabel ?prop ?propLabel (GROUP_CONCAT(DISTINCT ?valueLabel;separator=", ") AS ?propValue) WHERE { 
                  BIND(wd:'.$entityId.' AS ?item).
                  ?prop wikibase:directClaim ?p .
                  ?item ?p ?value .
                  SERVICE wikibase:label { 
                    bd:serviceParam wikibase:language "'.$lang.'". 
                    ?value rdfs:label ?valueLabel . 
                    ?prop rdfs:label ?propLabel . 
                    ?item rdfs:label ?itemLabel . 
                    ?item skos:altLabel ?itemAltLabel . 
                    ?item schema:description ?itemDescription . 
                  }    
                } group by ?item ?itemLabel ?itemDescription ?itemAltLabel ?prop ?propLabel
        '; 

        $client = new SparqlClient();

        $data = $client->execute($query); 

        if(!$data) {
            return null;
        }

        $entity = new Entity($data, $lang);

        return $entity;
    }
}