<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Phụ thuộc: User (Model, truy cập trực tiếp).
 */
class UserService
{
    /**
     * Lấy hồ sơ người dùng.
     */
    public function getById(int $id): User
    {
        // TODO: User::findOrFail($id)
        return User::query()->findOrFail($id);
    }

    /**
     * Cập nhật hồ sơ (họ tên, SĐT, địa chỉ). Không cho đổi email.
     */
    public function update(int $id, array $data): User
    {
        // TODO: cập nhật hồ sơ và trả về User mới
        $user = User::query()->findOrFail($id);

        $user->update([
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? null,
            'default_address' => $data['default_address'] ?? null,
        ]);

        return $user->refresh();
    }

    /**
     * Đổi mật khẩu.
     */
    public function changePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    /**
     * Khóa tài khoản (Admin).
     */
    public function lockAccount(int $id): void
    {
        // TODO: is_locked = true; không cho Admin tự khóa chính mình
    }

    /**
     * Mở khóa tài khoản (Admin).
     */
    public function unlockAccount(int $id): void
    {
        // TODO: is_locked = false
    }
}
