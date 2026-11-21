<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * GET /account (route công khai)
     */
    public function getById(Request $request): View
    {
        // TODO: Guest (!Auth::check()): trả view trang Tài khoản có form đăng nhập/đăng ký
        // TODO: đã đăng nhập: $this->userService->getById(Auth::id()) và trả view thông tin cá nhân
    }

    /**
     * PUT /account
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        // TODO: gọi $this->userService->update(Auth::id(), $request->validated()); email không cho đổi (BR2-UC02)
    }

    /**
     * PUT /account/password
     */
    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        // TODO: gọi $this->userService->changePassword(Auth::id(), mật khẩu cũ, mật khẩu mới)
    }

    /**
     * PATCH /admin/users/{id}/lock
     */
    public function lockAccount(int $id): RedirectResponse
    {
        // TODO: gọi $this->userService->lockAccount($id)
    }

    /**
     * PATCH /admin/users/{id}/unlock
     */
    public function unlockAccount(int $id): RedirectResponse
    {
        // TODO: gọi $this->userService->unlockAccount($id)
    }
}
