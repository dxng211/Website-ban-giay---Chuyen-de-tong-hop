<?php

namespace Database\Seeders;

use App\Enums\CategoryGroupType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Giày: một danh mục, phân loại theo giới tính ở bảng products
            ['name' => 'Giày', 'slug' => 'giay', 'group_type' => CategoryGroupType::Shoe, 'size_chart_key' => 'shoe_vn'],

            // Phụ kiện (menu lọc "loại sản phẩm" ở trang phụ kiện)
            ['name' => 'Tất', 'slug' => 'tat', 'group_type' => CategoryGroupType::Accessory, 'size_chart_key' => 'sock_vn'],
            ['name' => 'Lót giày', 'slug' => 'lot-giay', 'group_type' => CategoryGroupType::Accessory, 'size_chart_key' => 'shoe_vn'],
            ['name' => 'Vệ sinh giày', 'slug' => 've-sinh-giay', 'group_type' => CategoryGroupType::Accessory, 'size_chart_key' => null],
            ['name' => 'Dây giày', 'slug' => 'day-giay', 'group_type' => CategoryGroupType::Accessory, 'size_chart_key' => null],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
