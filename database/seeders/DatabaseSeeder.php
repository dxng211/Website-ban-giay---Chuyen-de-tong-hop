<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Thứ tự quan trọng vì có khóa ngoại:
     * người dùng -> bảng size -> danh mục (cần bảng size) -> sản phẩm (cần danh mục) -> đánh giá (cần sản phẩm, người dùng)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SizeChartSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
