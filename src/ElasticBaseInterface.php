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


interface ElasticeBaseInterface {

    /**
     * the "database"
     */
    public function index();

    /**
     * the "table"
     */
    public function type();

    /**
     * Your object id, uuid, etc
     */
    public function id();

    /**
     * This is where it gets intresting.
     *
     * Most articles say $object->toArray()
     *
     * Don't be lazy. Use a Transformer.
     * Send @philsturgeon some bitcoin for [thephpleague/fractal]
     */
    public function body();




}
