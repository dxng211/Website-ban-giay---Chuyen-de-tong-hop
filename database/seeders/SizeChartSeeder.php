<?php

namespace Database\Seeders;

use App\Models\SizeChart;
use App\Models\SizeChartGroup;
use Illuminate\Database\Seeder;

class SizeChartSeeder extends Seeder
{
    /**
     * LƯU Ý: số liệu chỉ để chạy thử, cần thay bằng bảng size thật của cửa hàng.
     *
     * Quy ước khoảng chiều dài chân (shoe_vn): mỗi size rộng 0,5 cm, các size cách nhau 0,6 cm,
     * nên không bị chồng lấn. Số đo nhập vào cần làm tròn 1 chữ số thập phân (chatbot_logs.foot_length_cm).
     * Ví dụ: 24,8 cm nằm trong khoảng 24,6 - 25,1 nên ra size 40.
     */
    public function run(): void
    {
        // ---- Bảng size giày (dùng cho danh mục Giày và Lót giày) ----
        SizeChartGroup::updateOrCreate(['chart_key' => 'shoe_vn'], [
            'chart_name' => 'Bảng size giày (VN)',
            'size_column_label' => 'Size',
            'reference_column_label' => 'Chiều dài chân (cm)',
            'unit' => 'cm',
        ]);

        $shoe = [36 => 22.2, 37 => 22.8, 38 => 23.4, 39 => 24.0, 40 => 24.6, 41 => 25.2, 42 => 25.8, 43 => 26.4, 44 => 27.0];
        $order = 1;
        foreach ($shoe as $size => $min) {
            SizeChart::updateOrCreate(
                ['chart_key' => 'shoe_vn', 'size_label' => (string) $size],
                [
                    'reference_value' => null, // NULL = tự sinh từ min - max + đơn vị (accessor display_value)
                    'reference_min' => $min,
                    'reference_max' => round($min + 0.5, 1),
                    'sort_order' => $order++,
                ]
            );
        }

        // ---- Bảng size tất (S, M, L theo cỡ giày) ----
        SizeChartGroup::updateOrCreate(['chart_key' => 'sock_vn'], [
            'chart_name' => 'Bảng size tất',
            'size_column_label' => 'Size',
            'reference_column_label' => 'Cỡ giày (size)',
            'unit' => null,
        ]);

        $socks = [
            ['S', '36 - 38', 36, 38],
            ['M', '39 - 41', 39, 41],
            ['L', '42 - 44', 42, 44],
        ];
        foreach ($socks as $i => [$label, $text, $min, $max]) {
            SizeChart::updateOrCreate(
                ['chart_key' => 'sock_vn', 'size_label' => $label],
                [
                    'reference_value' => $text,
                    'reference_min' => $min,
                    'reference_max' => $max,
                    'sort_order' => $i + 1,
                ]
            );
        }
    }
}
