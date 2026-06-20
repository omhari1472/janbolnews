<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminProfileController extends Controller {
    use ApiResponse;

    public function __construct(private FileUploadService $uploader) {}

    public function update(Request $request) {
        $request->validate([
            'name'   => 'required|string|max:100',
            'avatar' => 'nullable|image|max:2048',
        ]);
        $user = $request->user();
        $data = ['name' => $request->name];
        if ($request->hasFile('avatar')) {
            $this->uploader->delete($user->avatar_path);
            $data['avatar_path'] = $this->uploader->upload($request->file('avatar'), 'avatars');
        }
        $user->update($data);
        $fresh = $user->fresh();
        return $this->successResponse([
            'id'          => $fresh->id,
            'name'        => $fresh->name,
            'email'       => $fresh->email,
            'role'        => $fresh->role,
            'permissions' => $fresh->permissions,
            'avatar_url'  => $fresh->avatar_url,
        ], 'Profile updated');
    }
}
