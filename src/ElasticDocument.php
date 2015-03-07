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


class ElasticDocument implements ElasticBaseInterface
{
    protected $model;

    public __construct(\Illuminate\Database\Eloquent\Model $model)
    {
        $this->model = $model;
    }


}

