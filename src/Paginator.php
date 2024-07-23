<?php

namespace Nuwber\Oponka;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{
    public function __construct(protected Result $result, int $limit, int $page)
    {
        parent::__construct(
            $result->hits(),
            $result->totalHits(),
            $limit,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $hitsReference = &$this->items;

        $result->setHits($hitsReference);
    }

    /**
     * Access the oponka result object.
     */
    public function result(): Result
    {
        return $this->result;
    }
}
