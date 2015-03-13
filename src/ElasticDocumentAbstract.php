<?php
/*
 * This file is part of the Almeida\LaravelElasticSearch package.
 *
 * (c) Arnold Almeida <arnold@floatingpoints.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Almeida\LaravelElasticSearch;

use Illuminate\Database\Eloquent\Model;

abstract class ElasticDocumentAbstract
{
    // required
    protected $index;

    // required
    protected $type;

    protected $options = [
        'transformer' => null       // Name of transformer to use
    ];

    protected $id;

    protected $body;

    /**
     * @var Elasticsearch\Client
     */
    protected $client = null;

    /**
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct($options = [])
    {
        $this->client = new \Elasticsearch\Client();

        $this->options = array_merge($this->options, $options);

        if (!empty($options['transformer'])) {
            $this->setTransformer($options['transformer']);
        }
    }

    /**
     * (Optional) Your object id, uuid, etc
     */
    abstract public function setId($obj);

    protected function createClient()
    {
        $this->client = new \Elasticsearch\Client();
    }

    /**
     * @param String $transformer
     *
     *        The name of the transformer class to call
     *        for now assume people are using fractal
     */
    public function setTransformer($transformer)
    {
        $this->options['transformer'] = $transformer;
    }

    /**
     * This is where it gets intresting.
     *
     * Most articles say $object->toArray()
     *
     * Don't be lazy. Use a Transformer.
     * Send @philsturgeon some bitcoin for [thephpleague/fractal]
     *
     * @param Model $obj Illuminate\Database\Eloquent\Model
     *
     * @return Transformed league/fractal array
     */
    public function setBody(Model $obj)
    {
        // @todo - create an more specific exception
        if (empty($this->options['transformer'])) {
            throw new Exception("No transformer set", E_USER_ERROR);
        }

        $transformerName = $this->options['transformer'];

        $transformer = new $transformerName($obj);
        $this->body  = $transformer->transform();

        return $this->body;
    }

    public function index()
    {
        if (empty($this->body)) {
            // https://www.youtube.com/watch?v=A_ulZiob5I0
            // If you str_replace('Boy', 'Body', $youtubeTitle) it totally works...
            throw new Exception("Unable to create and index with no body.", E_USER_ERROR);
        }

        return $this->client->index([
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $this->id,
            'body'  => $this->body
        ]);
    }

    /**
     * Alias
     */
    public function create()
    {
        return $this->index();
    }

    /**
     * Alias
     */
    public function update()
    {
        return $this->index();
    }

    public function delete()
    {
        return $this->client->index([
            'index' => $this->index,
            'type' => $this->type,
            'id' => $this->id,
        ]);
    }


    /**
     * Exposes search
     *
     * @param  Array  $query
     */
    public function search(Array $query)
    {
        return $this->client->search([
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => $query
            ]
        ]);
    }

    /**
     * Just a basic search
     *
     * @param  [type] $term [description]
     * @return [type]       [description]
     */
    public function basicSearch($term)
    {
        return $this->client->search([
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => $term
                    ]
                ]
            ]
        ]);
    }

    /**
     * Alias for $this->search()
     */
    public function raw(Array $query)
    {
        return $this->search($query);
    }


}

