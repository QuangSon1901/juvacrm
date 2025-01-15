<?php

namespace App\Services;

class PaginationService
{
    public static function paginate($query, $currentPage, $perPage)
    {
        $currentPage = isset($currentPage) && is_numeric($currentPage) ? (int)$currentPage - 1 : 0;
        $offset = $currentPage * $perPage;
        $query = $query->orderBy('created_at', 'desc');
        $totalRecords = $query->count();
        $data = $query->offset($offset)->limit($perPage)->get();

        return [
            'data' => $data,
            'sorter' => [
                'offset' => $offset,
                'perpage' => $perPage,
                'totalpages' => ceil($totalRecords / $perPage),
                'sorterpage' => $currentPage + 1,
                'sorterrecords' => $totalRecords,
            ],
        ];
    }
}
