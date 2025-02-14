<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\OtpMail;

class RegisterController extends Controller
{
    // Tampilkan form registrasi
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Proses registrasi
    public function register(Request $request)
{
    // Validasi input pengguna
    $validatedData = $request->validate([
        'fullname' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Simpan data pengguna ke database
    $user = User::create([
        'fullname' => $validatedData['fullname'],
        'username' => $validatedData['username'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
        'role' => 'user',
        'status' => 'non-verified',
    ]);

    // Generate OTP
    $otp = rand(100000, 999999); // Generate OTP secara acak

    // Simpan OTP ke dalam database
    $user->update(['otp' => $otp]);

    // Kirim OTP via email
    Mail::to($user->email)->send(new OtpMail($user, $otp));

    // Redirect ke halaman OTP untuk validasi
    return redirect()->route('validate.otp', ['id' => $user->id]);
}

    public function sendOtp(Request $request, $id)
{
    $user = User::findOrFail($id);
    $otp = rand(100000, 999999); // Generate OTP secara acak

    // Simpan OTP ke dalam database atau sementara
    $user->update(['otp' => $otp]);

    // Kirim email dengan Mailable
    Mail::to($user->email)->send(new OtpMail($user, $otp));

    return back()->with('success', 'OTP telah dikirim ke email Anda!');
}

    // Tampilkan form validasi OTP
    public function showOtpForm($id)
    {
        return view('auth.validate-otp', ['id' => $id]);
    }

    // Validasi kode OTP
    public function validateOtp(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::findOrFail($id);

        // Periksa kode OTP
        if ($user->otp == $request->otp) {
            // Jika OTP valid, ubah status user menjadi verified
            $user->update([
                'status' => 'verified',
                'otp' => null, // Hapus OTP setelah diverifikasi
            ]);

            return redirect()->route('login')->with('success', 'Akun Anda telah diverifikasi! Silakan login.');
        }

        return back()->with('error', 'Kode OTP tidak valid.');
    }
}
