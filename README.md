[![wikidata](https://raw.githubusercontent.com/maxlath/wikidata-cli/master/assets/wikidata_logo_alone.jpg)](https://wikidata.org)


# Wikidata [![Build Status](https://travis-ci.org/freearhey/wikidata.svg?branch=master)](https://travis-ci.org/freearhey/wikidata)

Wikidata provides a API for searching and retrieving data from [wikidata.org](https://www.wikidata.org).

## Installation

```sh
composer require freearhey/wikidata
```

## Usage

First we need to create an instance of `Wikidata` class and save it to some variable, like this:

```php
require_once('vendor/autoload.php');

use Wikidata\Wikidata;

$wikidata = new Wikidata();
```

after that we can use one of the available methods to access the Wikidata database.

## Available Methods

### `search()`

The `search()` method give you a way to find Wikidata entity by it label.

```php
$results = $wikidata->search($query, $lang, $limit);
```

Arguments:

- `$query`: term to search (required) 
- `$lang`: specify the results language (default: 'en')
- `$limit`: set a custom limit (default: 10)

Example:

```php
$results = $wikidata->search('car', 'fr', 5);

/*
  Collection {
    #items: array:5 [
      0 => SearchResult {
        id: "Q1043"
        lang: "fr"
        label: "Carl von Linné"
        description: "naturaliste suédois (1707-1778)"
        aliases: []
      }
      1 => SearchResult {
        id: "Q14599311"
        lang: "fr"
        label: "Apoptose"
        description: "Mort programmée des cellules"
        aliases: array:1 [
          0 => "Caryolyse"
        ]
      }
      ...
    ]
  }
*/
```

The `search()` method always returns `Illuminate\Support\Collection` class with results. This means you can use all the [methods available](https://laravel.com/docs/5.6/collections#available-methods) in Laravel's Collections.

### `searchBy()`

The `searchBy` help you to find Wikidata entities by it properties value. 

```php
$results = $wikidata->searchBy($propId, $entityId, $lang, $limit);
```

Arguments:

- `$propId`: id of the property by which to search (required)
- `$entityId`: id of the entity (required) 
- `$lang`: specify the results language (default: 'en')
- `$limit`: set a custom limit (default: 10)

Example:

```php
// List of people who born in city Pomona, US
$results = $wikidata->searchBy('P19', 'Q486868');

/*
  Collection {
    #items: array:10 [
      0 => SearchResult {
        id: "Q22254338"
        lang: "en"
        label: "Coco Velvett"
        description: "American pornographic actress"
        aliases: array:2 []
      }
      1 => SearchResult {
        id: "Q24176246"
        lang: "en"
        label: "Donald D. Engen"
        description: null
        aliases: []
      }
      ...
    ]
  }
*/
```

The `searchBy()` method always returns `Illuminate\Support\Collection` class with results. This means you can use all the [methods available](https://laravel.com/docs/5.6/collections#available-methods) in Laravel's Collections.

### `get()`

The `get()` returns Wikidata entity by specified ID.

```php
$entity = $wikidata->get($entityId, $lang);
```

Arguments:

- `$entityId`: id of the entity (required) 
- `$lang`: specify the results language (default: 'en')

Example:

```php
// Get all data about Steve Jobs
$entity = $wikidata->get('Q19837');

/*
  Entity {
    id: "Q19837"
    lang: "en"
    label: "Steve Jobs"
    aliases: array:2 [
      0 => "Steven Jobs"
      1 => "Steven Paul Jobs"
    ]
    description: "American entrepreneur and co-founder of Apple Inc."
    properties: Collection {
      #items: array:98 [
        "P18" => Property {
          id: "P18"
          label: "image"
          value: "http://commons.wikimedia.org/wiki/Special:FilePath/Steve%20Jobs%20Headshot%202010-CROP2.jpg"
        }
        ...
      ]
    }
  }
*/


// List of all properties as array
$properties = $entity->properties->toArray();

/*
  [
    "P18" => Property {
      id: "P18"
      label: "image"
      value: "http://commons.wikimedia.org/wiki/Special:FilePath/Steve%20Jobs%20Headshot%202010-CROP2.jpg"
    },
    "P19" => Property {
      id: "P19"
      label: "place of birth"
      value: "San Francisco"
    },
    ...
  ]
 */
```

### Testing

```sh
vendor/bin/phpunit
```

### Contribution
If you find a bug or want to contribute to the code or documentation, you can help by submitting an [issue](https://github.com/freearhey/wikidata/issues) or a [pull request](https://github.com/freearhey/wikidata/pulls).

### License
Wikidata is licensed under the [MIT license](http://opensource.org/licenses/MIT).