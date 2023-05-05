<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;

/**
 *
 */
class Collection extends BaseCollection
{
    /**
     * @param int $perPage
     * @param int|null $page
     * @param int|null $total
     * @param string $pageName
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage, int $page = null, int $total = null, string $pageName = 'page'): LengthAwarePaginator
    {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

        return new LengthAwarePaginator(
            $this->forPage($page, $perPage)->values(),
            $total ?: $this->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }

}
