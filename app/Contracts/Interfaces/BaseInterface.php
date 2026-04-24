<?php

namespace App\Contracts\Interfaces;

interface BaseInterface
{
    public function store(array $data): mixed;
    public function update(mixed $id, array $data): mixed;
    public function delete(mixed $id): mixed;
    public function show(mixed $id): mixed;
    public function get(): mixed;
}
