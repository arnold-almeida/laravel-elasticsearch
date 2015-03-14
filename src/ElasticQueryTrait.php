<?php


trait ElasticQueryTrait
{
    protected $params = [
        'pagination' => [
            'from' => 0,
            'size' => 10,
        ]
    ];

    /**
     * @link http://www.elastic.co/guide/en/elasticsearch/guide/current/empty-search.html#empty-search
     */
    public function emptySearch()
    {
        return $this->client->search();
    }

    public function matchKey($key, $value)
    {
        return [
            'match' => [
                $key => $value
            ]
        ];
    }
}
