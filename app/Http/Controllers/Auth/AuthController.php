<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;

class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        if(auth()->user())
        {
            return redirect()->route('home')->with(['user'=> auth()->user()]);
        }
        return view('auth.login');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registration()
    {
        if(auth()->user())
        {
            return redirect()->route('home')->with(['user'=> auth()->user()]);
        }
        return view('auth.registration');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        $user = User::whereEncrypted('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            auth()->user()->generateCode();

            return redirect()->route('2fa.index');
        }

        if(auth()->user())
        {
            return redirect()->route('home')->with(['user'=> auth()->user()]);
        }

        return redirect("login")->withSuccess('Oppes! You have entered invalid credentials');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        Auth::login($check);

        auth()->user()->generateCode();

        return redirect("dashboard")->with(['Success' => 'Great! You have Successfully loggedin', 'user' => auth()->user()]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function dashboard()
    {
        if(Auth::check()){
            $user = User::find(auth()->user()->id);
            return view('dashboard', compact('user'));
        }

        return redirect("login")->withSuccess('Opps! You do not have access');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $user->lockForUpdate();
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable|numeric|digits:10',
            'city' => 'nullable',
            'dob' => 'nullable|date'
        ]);

        $user->fill($request->post())->save();

        $user->refresh();

        return redirect()->route('home')->with(['success','User Has Been updated successfully', 'user' => $user]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout() {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
