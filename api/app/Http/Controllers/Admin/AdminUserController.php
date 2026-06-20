<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller {
    use ApiResponse;

    public function index() {
        $users = User::orderBy('created_at', 'desc')
            ->get(['id','name','email','role','status','created_at']);
        return $this->successResponse($users);
    }

    public function store(Request $request) {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'role'                  => ['required', Rule::in(['super','editor'])],
            'status'                => ['required', Rule::in(['active','disabled'])],
        ]);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => $request->status,
        ]);
        return $this->successResponse(
            $user->only(['id','name','email','role','status','created_at']),
            'User created successfully'
        );
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'role'                  => ['required', Rule::in(['super','editor'])],
            'status'                => ['required', Rule::in(['active','disabled'])],
            'password'              => 'nullable|min:8|confirmed',
        ]);
        $data = $request->only(['name','email','role','status']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return $this->successResponse(
            $user->fresh()->only(['id','name','email','role','status','created_at']),
            'User updated'
        );
    }

    public function destroy(Request $request, User $user) {
        if ($request->user()->id === $user->id) {
            return $this->errorResponse('Cannot delete your own account.', 422);
        }
        $user->tokens()->delete();
        $user->delete();
        return $this->successResponse([], 'User deleted');
    }
}
