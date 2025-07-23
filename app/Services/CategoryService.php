<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
class CategoryService {
    public function getAll(object $request , ?int $pagination = null): Collection|LengthAwarePaginator{

        $categories = Category::orderBy('name')->where('active', 1);

        if ($request->search){
          $search = $request->search;
          $categories->where(function ($query) use($search){
            $query->where('code', 'LIKE' , "%{$search}%")->orWhere('name', 'LIKE', "%{$search}%");
          });

        }
        return $pagination ? $categories->paginate($pagination) : $categories->get();

    }

    public function getByUuid(string $uuid){
        $category = Category::where(['active' => 1, 'uuid' => $uuid])->first();

        if(!$category){
            abort(404, __('app.data_not_found', ['data'=> __('app.category')]));
        }
        return $category;

    }
}
