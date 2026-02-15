<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

        public function showLoginForm(){
            return view('auth.login');
        }

    public function login(Request $request){
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable',
        ]);

        $remember = (bool) $request->boolean('remember');
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $remember)) {
            
        $request->session()->regenerate();
            $role = Auth::user()->role;
            
            if ($role === 'family') {
              return redirect()->route('family.index'); 
            } elseif ($role === 'teacher') {
                return redirect()->route('teacher.index');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.index');
            }
            
            return redirect()->route('index');
        }
        return back()->withErrors(['Invalid email or password.'])->withInput(['email' => $validated['email']]);
    }
    
        public function showRegisterForm(){
        return redirect()->route('login');
    }

    public function index()
    {
    $user = Auth::user();
	if (!$user) { return redirect()->route('login'); }
	if ($user->role === 'admin') { return redirect()->route('admin.index'); }
	if ($user->role === 'teacher') { return redirect()->route('teacher.index'); }
	if ($user->role === 'family') { return redirect()->route('family.index'); }
	return redirect()->route('login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Registration disabled
        return redirect()->route('login');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
