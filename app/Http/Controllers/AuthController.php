<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    // public function index()
    // {
    //     return view('user.add');
    // }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken('user login')->plainTextToken;
    }

    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'nama_user' => 'required',
            'username' => 'required',
            'password' => 'required',
            'email' => 'required',
            'no_hp' => 'required',
            'wa' => 'required',
            'pin' => 'required',
        ]);

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('public/posts', $image->hashName());

        //create post
        User::create([
            'nama_user' => $request->nama_user,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'wa' => $request->wa,
            'pin' => $request->pin,
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function logout(Request $request)
    {
        // $user->tokens()->where('id', $tokenId)->delete();
        $request->user()->currentAccessToken()->delete();
    }
}
