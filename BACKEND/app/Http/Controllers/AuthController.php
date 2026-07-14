<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;
use App\Models\FreelancerSkill;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginPage(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:160'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        if (! Auth::attempt([
            $field => $credentials['login'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            return back()->withInput($request->only('login'))->withErrors([
                'login' => 'Username/email atau password tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'login' => match ($user->account_status) {
                    User::STATUS_PENDING => 'Akun masih menunggu persetujuan admin.',
                    User::STATUS_REJECTED => 'Pendaftaran akun ditolak.',
                    default => 'Akun sedang dinonaktifkan.',
                },
            ]);
        }

        return redirect()->intended($this->homeRoute($user));
    }

    public function clientRegisterPage(): View
    {
        return view('auth.register-client');
    }

    public function registerClient(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::query()->create([
            ...$data,
            'role' => User::ROLE_CLIENT,
            'account_status' => User::STATUS_ACTIVE,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.home')->with('success', 'Akun klien berhasil dibuat.');
    }

    public function freelancerRegisterPage(): View
    {
        $categories = ServiceCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('auth.register-freelancer', compact('categories'));
    }

    public function registerFreelancer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'bio' => ['nullable', 'string', 'max:3000'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'portfolio_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,zip'],
            'skills' => ['required', 'array', 'min:1'],
            'skills.*' => ['integer', 'distinct', 'exists:service_categories,id'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $user = User::query()->create([
                'username' => $data['username'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'role' => User::ROLE_FREELANCER,
                'account_status' => User::STATUS_PENDING,
            ]);

            $portfolioPath = $request->file('portfolio_file')
                ? $request->file('portfolio_file')->store('freelancer-portfolios', 'public')
                : null;

            FreelancerProfile::query()->create([
                'user_id' => $user->id,
                'bio' => $data['bio'] ?? null,
                'experience_years' => $data['experience_years'],
                'portfolio_url' => $data['portfolio_url'] ?? null,
                'portfolio_file_path' => $portfolioPath,
                'application_status' => FreelancerProfile::STATUS_PENDING,
            ]);

            foreach ($data['skills'] as $categoryId) {
                FreelancerSkill::query()->create([
                    'freelancer_id' => $user->id,
                    'service_category_id' => $categoryId,
                    'status' => FreelancerSkill::STATUS_PENDING,
                ]);
            }
        });

        return redirect()->route('login')->with(
            'success',
            'Pendaftaran freelancer berhasil dikirim. Akun dapat digunakan setelah keahlian disetujui admin.'
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function homeRoute(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_FREELANCER => route('freelancer.dashboard'),
            default => route('client.home'),
        };
    }
}
