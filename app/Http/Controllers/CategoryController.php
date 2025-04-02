<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseTrait;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => CategoryResource::collection(Category::all()),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|min:2|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $data = $validator->safe();

        $category = Category::query()->create($data->only('name'));

        return response()->json([
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|min:2|unique:categories,name,' . $category->id,
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $data = $validator->safe();

        $category->update($data->only('name'));

        return response()->json([
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }
}
