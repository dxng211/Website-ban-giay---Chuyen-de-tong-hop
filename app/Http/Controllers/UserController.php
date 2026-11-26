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
use Illuminate\Support\Facades\Schema;

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
        $q = trim((string) $request->query('q', $request->query('search', '')));
        $status = trim((string) $request->query('status', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $searchableCols = array_values(array_filter(
                    ['full_name', 'name', 'username', 'email', 'phone', 'phone_number'],
                    fn ($col) => Schema::hasColumn('users', $col)
                ));

                if (!empty($searchableCols)) {
                    $query->where(function ($subQuery) use ($searchableCols, $q) {
                        foreach ($searchableCols as $idx => $col) {
                            if ($idx === 0) {
                                $subQuery->where($col, 'like', "%{$q}%");
                            } else {
                                $subQuery->orWhere($col, 'like', "%{$q}%");
                            }
                        }
                    });
                }
            })
            ->when($status !== '', function ($query) use ($status) {
                $isLockedFilter = in_array(strtolower($status), ['locked', 'blocked', 'inactive', 'banned', '0'], true);

                $query->where(function ($subQuery) use ($isLockedFilter) {
                    if ($isLockedFilter) {
                        $hasCondition = false;
                        if (Schema::hasColumn('users', 'is_locked')) {
                            $subQuery->where('is_locked', 1);
                            $hasCondition = true;
                        }
                        if (Schema::hasColumn('users', 'is_active')) {
                            $hasCondition ? $subQuery->orWhere('is_active', 0) : $subQuery->where('is_active', 0);
                            $hasCondition = true;
                        }
                        if (Schema::hasColumn('users', 'status')) {
                            $vals = ['locked', 'blocked', 'inactive', 'banned', '0'];
                            $hasCondition ? $subQuery->orWhereIn('status', $vals) : $subQuery->whereIn('status', $vals);
                        }
                    } else {
                        if (Schema::hasColumn('users', 'is_locked')) {
                            $subQuery->where('is_locked', 0);
                        }
                        if (Schema::hasColumn('users', 'is_active')) {
                            $subQuery->where('is_active', 1);
                        }
                        if (Schema::hasColumn('users', 'status')) {
                            $subQuery->whereIn('status', ['active', '1']);
                        }
                    }
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'status'));
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
