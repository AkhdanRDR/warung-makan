<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\MenuInterface;
use App\Models\Menu;

class MenuRepository extends BaseRepository implements MenuInterface
{
    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    public function index(int $perPage, int $page, ?array $filters = null): mixed
    {
        $query = $this->model->query();

        if ($filters) {
            if (isset($filters['search'])) {
                $query->where('name', 'like', '%' . $filters['search'] . '%');
            }
            if (isset($filters['category'])) {
                $query->where('category', $filters['category']);
            }
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
        }

        return $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);
    }

    public function restore(mixed $id): mixed
    {
        $model = $this->model->query()->withTrashed()->findOrFail($id);
        $model->restore();
        return $model;
    }
}
