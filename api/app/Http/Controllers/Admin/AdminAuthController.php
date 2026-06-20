<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AdminAuthController extends Controller {
    use ApiResponse;
    public function login(Request $request) {
        $request->validate(['email'=>'required|email','password'=>'required']);
        $user = User::where('email',$request->email)->first();
        if (!$user || !Hash::check($request->password,$user->password))
            return $this->errorResponse('Incorrect credentials.',401);
        if ($user->status !== 'active')
            return $this->errorResponse('Account disabled.',403);
        $user->tokens()->delete();
        $token = $user->createToken('admin-token',['admin'])->plainTextToken;
        return $this->successResponse(['access_token'=>$token,'token_type'=>'Bearer','user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role]],'Login successful');
    }
    public function me(Request $request) { return $this->successResponse($request->user()); }
    public function logout(Request $request) { $request->user()->currentAccessToken()->delete(); return $this->successResponse([],'Logged out'); }
    public function updatePassword(Request $request) {
        $request->validate(['current_password'=>'required','new_password'=>'required|min:8|confirmed']);
        if (!Hash::check($request->current_password,$request->user()->password))
            return $this->errorResponse('Current password incorrect',422);
        $request->user()->update(['password'=>Hash::make($request->new_password)]);
        return $this->successResponse([],'Password updated');
    }
}
