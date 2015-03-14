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
    // the 'collection'
    protected $index;

    // required
    // the 'object' we are storing
    protected $type;

    protected $options = [
        'transformer' => null       // Name of transformer to use
    ];

    protected $body;

    /**
     * The raw response from an Elastic call
     * @var Array
     */
    protected $raw;

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

    public function getBody()
    {
        return $this->body;
    }

    /**
     * Index a document
     *
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function index($id)
    {
        if (empty($this->body)) {
            throw new Exception("Unable to create and index with no body.", E_USER_ERROR);
        }

        return $this->client->delete([
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'body' => $this->body,
        ]);
    }

    /**
     * "truncate" an index
     */
    public function truncate()
    {
        $params = [
            'index' => $this->index,
        ];
        return $this->client->indices()->delete($params);
    }

    /**
     * Delete's a document
     */
    public function delete($id)
    {
        return $this->client->delete([
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
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

}

