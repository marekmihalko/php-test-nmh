<?php

namespace App\Util;

trait PaginationTrait
{
    static $maxLimit = 50;

    public function getPage($page = 1)
    {
        if ($page < 1) {
            $page = 1;
        }

        return floor($page);
    }

    public function getLimitPerPage($limit = 30)
    {
        if ($limit < 1 || $limit > self::$maxLimit) {
            $limit = self::$maxLimit;
        }

        return floor($limit);
    }

    public function getOffset($page, $limit)
    {
        $offset = 0;
        if ($page != 0 && $page != 1) {
            $offset = ($page - 1) * $limit;
        }

        return $offset;
    }
}