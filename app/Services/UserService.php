<?php

namespace App\Services;

use App\Models\User;

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
    }

    /**
     * Cập nhật hồ sơ (họ tên, SĐT, địa chỉ). Không cho đổi email.
     */
    public function update(int $id, array $data): User
    {
        // TODO: cập nhật hồ sơ và trả về User mới
    }

    /**
     * Đổi mật khẩu.
     */
    public function changePassword(int $id, string $currentPassword, string $newPassword): void
    {
        // TODO: Hash::check($currentPassword, $user->password); sai -> báo lỗi; đúng -> gán password mới (cast tự mã hóa)
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
