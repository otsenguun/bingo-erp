<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseRequest;
use App\Models\ProductCategory;

class UpdateProductCategoryRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $slug  = $this->route('product_category');

        $category = ProductCategory::where('slug', $slug)->first();

        return [
            'name' => 'required|string|max:50|unique:product_categories,name,'.$category->id,
            'note' => 'nullable|string|max:255'
        ];
    }
}
