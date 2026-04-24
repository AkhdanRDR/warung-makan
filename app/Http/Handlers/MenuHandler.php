<?php

namespace App\Http\Handlers;

use App\Contracts\Interfaces\MenuInterface;
use Illuminate\Support\Facades\Storage;

class MenuHandler
{
    public function __construct(
        private MenuInterface $menuRepo
    ) {
    }

    public function store(array $data): mixed
    {
        if (isset($data['image'])) {
            Storage::disk('public')->put('menu_images', $data['image']);
            $data['image'] = 'menu_images/' . $data['image']->hashName();
        }

        return $this->menuRepo->store($data);
    }

    public function update(string $id, array $data): mixed
    {
        if (isset($data['image'])) {
            Storage::disk('public')->put('menu_images', $data['image']);
            $data['image'] = 'menu_images/' . $data['image']->hashName();
        }

        return $this->menuRepo->update($id, $data);
    }
}
