<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Tài khoản mẫu để cả nhóm dùng chung. CHỈ DÙNG KHI PHÁT TRIỂN:
     * đổi hoặc xóa các tài khoản này (nhất là Admin) trước khi triển khai thật.
     *
     * Dùng User::updateOrCreate (không dùng DB::table) để cast 'hashed' tự mã hóa mật khẩu.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@hucestore.com',
                'full_name' => 'Quản trị viên',
                'password' => 'Admin@123',
                'phone' => '0900000001',
                'default_address' => 'Hà Nội, Việt Nam',
                'role' => Role::Admin,
                'is_locked' => false,
            ],
            [
                'email' => 'khach1@example.com',
                'full_name' => 'Nguyễn Văn An',
                'password' => 'Customer@123',
                'phone' => '0900000002',
                'default_address' => '12 Nguyễn Trãi, Thanh Xuân, Hà Nội',
                'role' => Role::Customer,
                'is_locked' => false,
            ],
            [
                'email' => 'khach2@example.com',
                'full_name' => 'Trần Thị Bình',
                'password' => 'Customer@123',
                'phone' => '0900000003',
                'default_address' => '45 Kim Mã, Ba Đình, Hà Nội',
                'role' => Role::Customer,
                'is_locked' => false,
            ],
            [
                // Dùng để thử luồng "tài khoản bị khóa" (UC01, middleware not.locked)
                'email' => 'khoa@example.com',
                'full_name' => 'Tài khoản bị khóa',
                'password' => 'Customer@123',
                'phone' => '0900000004',
                'default_address' => null,
                'role' => Role::Customer,
                'is_locked' => true,
            ],
        ];

        foreach ($users as $data) {
            $email = $data['email'];
            unset($data['email']);

            User::updateOrCreate(['email' => $email], $data);
        }
    }
}
