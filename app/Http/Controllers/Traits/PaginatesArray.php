<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginatesArray
{
    protected function paginateArray(
        array $items,
        Request $request,
        array $searchKeys = ['nip', 'nama_lengkap'],
        int $perPage = 15,
    ): LengthAwarePaginator {
        $search = $request->input('search');

        if ($search) {
            $q = mb_strtolower($search);
            $items = array_values(array_filter($items, function (array $item) use ($q, $searchKeys): bool {
                foreach ($searchKeys as $key) {
                    if (isset($item[$key]) && str_contains(mb_strtolower((string) $item[$key]), $q)) {
                        return true;
                    }
                }

                return false;
            }));
        }

        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        return new LengthAwarePaginator(
            array_slice($items, $offset, $perPage),
            count($items),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );
    }
}
