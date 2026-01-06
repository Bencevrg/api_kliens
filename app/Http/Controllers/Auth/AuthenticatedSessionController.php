<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. API hívás a backend felé (/users/login)
        try {
            $response = Http::api()->post('/users/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            // 2. Ha sikeres a válasz (200 OK)
            if ($response->successful()) {
                $data = $response->json();
                
                // Kinyerjük a tokent és a user adatait
                $token = $data['token'] ?? null;
                $user = $data['user'] ?? null;

                if ($token) {
                    // 3. Eltároljuk a session-ben a kliens oldalon
                    session([
                        'api_token' => $token,
                        'user_name' => $user['name'] ?? 'Felhasználó',
                        'user_email' => $user['email'] ?? '',
                    ]);

                    // Session ID megújítása biztonsági okokból
                    $request->session()->regenerate();

                    // Visszairányítás a főoldalra
                    return redirect()->route('writers.index');
                }
            }

            // Ha az API hibát dobott (pl. rossz jelszó)
            return back()->withErrors([
                'email' => 'Helytelen email cím vagy jelszó.',
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Szerver hiba történt: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Kijelentkezéskor töröljük a session adatait
        session()->forget(['api_token', 'user_name', 'user_email']);
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}