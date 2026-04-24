<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\MenuInterface;
use App\Helpers\BaseResponse;
use App\Helpers\ExceptionMapper;
use App\Http\Handlers\MenuHandler;
use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function __construct(
        private MenuInterface $menuRepo,
        private MenuHandler $menuHandler
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->menuRepo->index(
                $request->per_page ?? 10,
                $request->page ?? 1,
                $request->only(['search', 'category', 'status'])
            );
            return BaseResponse::Paginate(
                'Menu Berhasil diambil',
                MenuResource::collection($data),
                200,
                $data
            );
        } catch (\Throwable $th) {
            return BaseResponse::Error(
                'Gagal mengambil menu',
                $th->getMessage(),
                ExceptionMapper::getStatusCode($th)
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->menuHandler->store($request->all());
            DB::commit();
            return BaseResponse::Success(
                'Menu Berhasil dibuat',
                MenuResource::make($data),
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if (isset($request->image)) {
                Storage::disk('public')->delete($request->image);
            }
            return BaseResponse::Error(
                'Gagal membuat menu',
                $th->getMessage(),
                ExceptionMapper::getStatusCode($th)
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->menuRepo->show($id);
            return BaseResponse::Success(
                'Menu Berhasil diambil',
                MenuResource::make($data),
                200
            );
        } catch (\Throwable $th) {
            return BaseResponse::Error(
                'Gagal mengambil menu',
                $th->getMessage(),
                ExceptionMapper::getStatusCode($th)
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->menuHandler->update($id, $request->all());
            DB::commit();
            return BaseResponse::Success(
                'Menu Berhasil diperbarui',
                MenuResource::make($data),
                200
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            if (isset($request->image)) {
                Storage::disk('public')->delete($request->image);
            }
            return BaseResponse::Error(
                'Gagal memperbarui menu',
                $th->getMessage(),
                ExceptionMapper::getStatusCode($th)
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->menuRepo->delete($id);
            DB::commit();
            return BaseResponse::Success(
                'Menu Berhasil dihapus',
                null,
                200
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return BaseResponse::Error(
                'Gagal menghapus menu',
                $th->getMessage(),
                ExceptionMapper::getStatusCode($th)
            );
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->menuRepo->restore($id);
            DB::commit();
            return BaseResponse::Success(
                'Menu Berhasil dipulihkan',
                MenuResource::make($data),
                200
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return BaseResponse::Error(
                'Gagal memulihkan menu',
                $th->getMessage(),
                ExceptionMapper::getStatusCode($th)
            );
        }
    }
}
