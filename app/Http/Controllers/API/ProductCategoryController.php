<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('id');
        $show_product = $request->input('show_product');
        if($id)
        {
            $category = ProductCategory::with(['category'])->find($id);

            if($category){
                return ResponseFormatter::success(
                    $category,
                    'Data Kategori berhasil diambil'
                );
            }
            else {
                return ResponseFormatter::error(
                    null,
                    'Data Kategori tidak ada',
                    484
                );
            }
        }

        $category = ProductCategory::query();

        if($name){
            $category->where('name','like', '%' . '$name' . '$');
        }

        if($show_product) {
            $category->with('product');
        }

        return ResponseFormatter::success(
            $category->paginate($limit),
            'Data list kategori berhasil diambil'
        );
    }
}
