<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
    public function __construct(
        private readonly UserService $userService,
    ) {}


    public function getById(Request $request): View
    {
        if (!Auth::check()) {
            return view('account.index');
        }

        $data = ['user' => Auth::user()];

        return view('account.index', $data);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['email']);

        $this->userService->update(Auth::id(), $data);

        return back()->with('success', 'Cập nhật thông tin cá nhân thành công.');
    }


    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $currentPassword = $validated['current_password'] ?? $request->input('current_password');
        $newPassword = $validated['new_password'] ?? $validated['password'] ?? $request->input('new_password');

        $this->userService->changePassword(Auth::id(), $currentPassword, $newPassword);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }

    public function list(Request $request): View
    {
        $q = $request->query('q');
        $users = User::query()
            ->when($q, function ($query, $q) {
                $query->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function lockAccount(int $id): RedirectResponse
    {
        $this->userService->lockAccount($id);

        return back()->with('success', 'Đã khóa tài khoản người dùng.');
    }


    public function unlockAccount(int $id): RedirectResponse
    {
        $this->userService->unlockAccount($id);

        return back()->with('success', 'Đã mở khóa tài khoản người dùng.');
    }
}
