<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService){
        $this->categoryService = $categoryService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request):Response
    {
         $request->validate([
            'per_page' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'search' => 'nullable|string|max:255'
        ]);
         $categories = $this->categoryService->getAll($request, $request->per_page);

         $results = ['categories' => CategoryResource::collection($categories)];

         if($request->per_page){
            $results['per_page'] = $categories->perPage();
            $results['current_page'] = $categories->currentPage();
            $results['last_page'] = $categories-> lastPage();
            $results['total'] = $categories->total();
         }

      return response([
        'message' => __('app.data_load_success', ['data'=> __('app.categories')]),
        'results' => $results,
      ]);

    }

    public function get (Request $request, string $uuid){
        
        $category = $this->categoryService->getByUuid($uuid);

        return response([
            'message' => __('app.data_load_success',['data'=> __('app.category')]),
            'results' => new CategoryResource($category),
        ]);
    }

}
