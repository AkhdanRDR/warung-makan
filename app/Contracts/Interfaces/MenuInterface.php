<?php

namespace App\Contracts\Interfaces;

interface MenuInterface extends BaseInterface
{
    public function restore(mixed $id): mixed;
    public function index(int $perPage, int $page, ?array $filters = null): mixed;
}
