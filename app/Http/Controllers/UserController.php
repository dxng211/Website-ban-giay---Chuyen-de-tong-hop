<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $orders = collect();

        if ($user) {
            $orders = $user->orders()
                ->with('items')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('account.index', [
            'user' => $user,
            'orders' => $orders,
            'hasIntendedCheckout' => str_contains((string) $request->session()->get('url.intended'), '/checkout'),
        ]);
    }

    /**
     * PUT /account
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        // TODO: gọi $this->userService->update(Auth::id(), $request->validated()); email không cho đổi (BR2-UC02)
        $this->userService->update((int) $request->user()->id, $request->validated());

        return redirect()->route('account')->with('success', 'Đã lưu thông tin cá nhân.');
    }


    /**
     * PUT /account/password
     */
    public function changePassword(ChangePasswordRequest $request, UserService $userService): RedirectResponse
    {
        $userService->changePassword($request->user(), $request->string('password')->toString());

        return redirect()->route('account')->with('success', 'Đổi mật khẩu thành công.');
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
