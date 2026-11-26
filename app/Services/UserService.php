<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
        return User::findOrFail($id);
    }

    /**
     * Cập nhật hồ sơ (họ tên, SĐT, địa chỉ). Không cho đổi email.
     */
    public function update(int $id, array $data): User
    {
        $user = $this->getById($id);

        unset($data['email']);

        $user->fill(array_intersect_key($data, array_flip([
            'name',
            'phone',
            'address',
        ])));
        $user->save();

        return $user->fresh();
    }

    /**
     * Đổi mật khẩu.
     */
    public function changePassword(int $id, string $currentPassword, string $newPassword): void
    {
        $user = $this->getById($id);

        if (!Hash::check($currentPassword, (string) $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Mật khẩu hiện tại không chính xác.',
            ]);
        }

        $user->password = $newPassword;
        $user->save();
    }

    /**
     * Khóa tài khoản (Admin).
     */
    public function lockAccount(int $id): void
    {
        if (Auth::check() && (int) Auth::id() === $id) {
            throw ValidationException::withMessages([
                'user' => 'Không thể tự khóa tài khoản của chính mình.',
            ]);
        }

        $user = $this->getById($id);
        $user->is_locked = true;
        $user->save();
    }

    /**
     * Mở khóa tài khoản (Admin).
     */
    public function unlockAccount(int $id): void
    {
        $user = $this->getById($id);
        $user->is_locked = false;
        $user->save();
    }
}
