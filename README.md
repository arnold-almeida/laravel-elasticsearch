### Preamble

I need to improve search. Currently using mongob, 2dspheres etc.

Bring on Elasticsearch.

##### Afternoon hack

- Trying to put togeter a nice package so we can use ElasticSearch easily
- Should play well with league/fractal
- Should play well with illuminate/collections
- Should play well with fractal/item
- Should play well with fractal/resources
- etc..

### Install via Composer

    require : {
        "almeida/laravel-elasticsearch" : "dev-master"
    }


### Step 1

Extend the abstract `ElasticDocument` so you get some easy CRUD.

Lets assume we are working with movies.

```php

/**
 * Acme\Search\MovieDocument
 */
class MovieDocument extends \Almeida\LaravelElasticSearch\ElasticDocument
{
    /**
     * the "database"
     */
    public function setIndex()
    {
        // what do you think of when mention cluster ?
        return 'cluster ...';
    }

    /**
     * the "table" / "collection"
     */
    public function setType() {
        return 'movies';
    }

	 /**
	 * look at the ElasticDocument constructor for more info
	 */
    public function setId() {
        return $this->model->id;
    }

}
```

### Creating / Updating an index

Create an index.

Assuming you have already gave a working `fractal/transformer`

```php

	$document = new \Acme\Search\MovieDocument($listing, 'Acme\MovieTransformer');
	$document->index();
	// or $document->create();
	// or $document->update();

```
### Deleting an index

Deleting an index.

```php

	$document = new \Acme\Search\MovieDocument($listing);
	$document->index();
	// or $document->create();
	// or $document->update();

```


### Indexing en masse in the Console

```php

	$n = 0;

	Movie::chunk(100, function($movies) use(&$n) {

		foreach ($movies as $i => $movie) {

			$document = new \Acme\Search\MovieDocument($movie, 'Acme\MovieTransformer');
			$document->index();

			$this->info("Indexed : {$movie->title}");

			$n++;
		}
	});

	$this->info("Indexed [{$n}] movies");

```

#### Searching (WIP)

Promise to work on it over the next few days.

```php
	$document->search($query);
```


### Todo

- Ive added a basic search($query) to ElasticDocument but i will abstract as below over the next few days

- Move search, stats, and aggerates to Traits so we only use them on applicable documents

- Apparantly composer is not autoloading so ive manually added

 ``'Almeida\\LaravelElasticSearch\\' => array($vendorDir . '/almeida/laravel-elasticsearch/src')``

 to my PSR4 autoloader for now.



## GOTCHAS

Deving locally on OSX i couldnt get the client to connect to elasticsearch even
though i could access it in the browser at http://localhost:9200

This fixed it for now...

- http://benjaminknofe.com/blog/2015/02/19/no-connection-on-localhost-to-elasticsearch-on-osx/

The correct way to access is actually through, http://127.0.0.1:9200

Will work it out when i sort out nicer config etc.

### Required reading

- http://www.elastic.co/guide/en/elasticsearch/guide/current/

##### Relevance

How is it calculated ?

- http://www.elastic.co/guide/en/elasticsearch/guide/current/relevance-intro.html

##### Monitoring elasticsearch using Marvel (dev)

https://www.elastic.co/downloads/marvel


### Thanks for the notes

- http://www.slideshare.net/bencorlett/discovering-elasticsearch
- http://blog.madewithlove.be/post/integrating-elasticsearch-with-your-laravel-app/
- http://blog.madewithlove.be/post/elasticsearch-aggregations/
- https://www.youtube.com/watch?v=waTWeJeFp4A
- https://www.youtube.com/watch?v=7FLXjgB0PQI
- http://laravel.com/docs/4.2/eloquent#model-observers
- https://www.youtube.com/watch?v=GrdzX9BNfkg


I accept bitcoin tips. [18tEqEUnyJaqvKh3CCNAAai9seztLb3Tw9].

They should too! Come on its going to be a thing.

### LICENSE

MIT
