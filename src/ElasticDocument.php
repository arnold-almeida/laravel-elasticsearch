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

abstract class ElasticDocument implements BasicDocumentInterface
{

    /**
     * Elasticsearch\Client
     */
    protected $client;

    protected $model;

    /**
     * [__construct description]
     *
     * @param Model  $model
     *
     *        - An Elequoent model
     *        - in theory should also work, jenssegers/laravel-mongodb
     *        - or any object that implements getId ? or similar ?
     *
     * @param String $transformer
     *
     *        The name of the transformer class to call
     *        for now assume people are using fractal
     *
     */
    public function __construct(Model $model, $transformerName)
    {
        $this->model = $model;
        $this->transformerName = $transformerName;
    }

    /**
     * @todo Make this implementation a bit tighter
     *
     * @return Transformed fractal array
     */
    public function setBody() {
        $transformer = new $this->transformerName($this->model);
        return $transformer->transform();
    }

    public function index()
    {
        $client = new \Elasticsearch\Client();
        $client->index([
            'index' => $this->setIndex(),
            'type' => $this->setType(),
            'id' => $this->setId(),
            'body' => $this->setBody()
        ]);
    }

    public function update()
    {
        return $this->index();
    }

    public function delete()
    {
        $client = new \Elasticsearch\Client();
        $client->index([
            'index' => $this->setIndex(),
            'type' => $this->setType(),
            'id' => $this->setId(),
        ]);
    }


}

