<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register()
    {
        $data['title'] = 'Register';
        return view('user/register', $data);
    }

    public function register_action(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:tb_user',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
            'role' => 'required|in:admin,manajer,kasir',
        ]);

        $user = new User([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        $user->save();

        return redirect()->route('login')->with('success', 'Registration success. Please login!');
    }


    public function login()
    {
        $data['title'] = 'Login';
        return view('user/login', $data);
    }

    
        public function login_action(Request $request)
        {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);
        
            // Menggunakan switch-case untuk memeriksa role
            $credentials = ['username' => $request->username, 'password' => $request->password];
            
            // Cek jika login berhasil
            if (Auth::attempt($credentials)) {
                $user = Auth::user();  // Ambil pengguna yang sedang login
        
                switch ($user->role) {
                    case 'admin':
                        // Jika role admin, redirect ke dashboard admin
                        $request->session()->regenerate();
                        return redirect()->route('admin.index');
                        
                    case 'manajer':
                        // Jika role user, redirect ke dashboard pengguna
                        $request->session()->regenerate();
                        return redirect()->route('penjualan.laporan');
                        
                    case 'kasir':
                            // Jika role user, redirect ke dashboard pengguna
                            $request->session()->regenerate();
                            return redirect()->route('keranjang.index');
                    default:
                        // Jika role tidak dikenal
                        Auth::logout();
                        return back()->withErrors([
                            'role' => 'You do not have permission to access this area',
                        ]);
                }
            }
        }
    

    public function password()
    {
        $data['title'] = 'Change Password';
        return view('user/password', $data);
    }

    public function password_action(Request $request)
    {
        $request->validate([
            'old_password' => 'required|current_password',
            'new_password' => 'required|confirmed',
        ]);
        $user = User::find(Auth::id());
        $user->password = Hash::make($request->new_password);
        $user->save();
        $request->session()->regenerate();
        return back()->with('success', 'Password changed!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }


     // Fungsi Menampilkan Daftar Pengguna
     public function index()
     {
         $users = User::all(); // Mengambil semua data pengguna
         return view('user.index', compact('users')); // Menampilkan ke view 'user.index'
     }
 
     // Fungsi Edit Pengguna
     public function edit($id)
     {
         $user = User::findOrFail($id); // Menemukan pengguna berdasarkan ID
         return response()->json($user); // Mengembalikan data pengguna dalam format JSON untuk modal
     }
 
     public function update(Request $request, $id)
     {
         $user = User::findOrFail($id);
         $user->name = $request->name;
         $user->username = $request->username;
         $user->role = $request->role;
 
         // Jika password diubah, hash password baru
         if ($request->filled('password')) {
             $user->password = Hash::make($request->password);
         }
 
         $user->save(); // Menyimpan perubahan
 
         return redirect()->route('user.index')->with('success', 'User updated successfully');
     }
 
     // Fungsi Hapus Pengguna
     public function destroy($id)
     {
         $user = User::findOrFail($id);
         $user->delete(); // Menghapus pengguna berdasarkan ID
 
         return redirect()->route('user.index')->with('success', 'User deleted successfully');
     }
}
