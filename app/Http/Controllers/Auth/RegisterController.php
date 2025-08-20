<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(Request $request)
    // {
    //     $user_id = '';
    //     // $this->validator($data->all())->validate();
    //     event(new Registered(User::create($request->all())));

    //     return redirect($this->redirectPath())->with('message', 'Your message');

    //     // return User::create([
    //     //     'nama_user' => $data['name'],
    //     //     'username' => $data['username'],
    //     //     'email' => $data['email'],
    //     //     'password' => Hash::make($data['password']),
    //     //     'no_hp' => $data['no_hp'],
    //     //     'wa' => $data['wa'],
    //     //     'pin' => $data['pin'],
    //     //     'status_user' => 1,
    //     // ]);
    // }
    // public function register(Request $request)
    // {
    //     $this->validator($request->all())->validate();

    //     event(new Registered($user = $this->create($request->all())));

    //     // $this->guard()->login($user);
    //     //this commented to avoid register user being auto logged in

    //     return $this->registered($request, $user)
    //         ?: redirect($this->redirectPath());
    // }

    protected function create(array $data)
    {
        return User::create([
            'nama_user' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'no_hp' => $data['no_hp'],
            'wa' => $data['wa'],
            'pin' => $data['pin'],
            'status_user' => 1,
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }
}
