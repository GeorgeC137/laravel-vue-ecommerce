<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form action="{{ route('login') }}" method="POST" class="w-[400px] mx-auto p-6 my-16">
        @csrf
        <h2 class="text-2xl font-semibold text-center mb-5">
            Login to your account
        </h2>
        <p class="text-center text-gray-500 mb-6">
            or
            <a href="{{ route('register') }}" class="text-sm text-purple-700 hover:text-purple-600">create new account</a>
        </p>
        <div class="mb-4">
            <x-text-input id="loginEmail" :errors="$errors" :value="old('email')" type="email" name="email"
                placeholder="Your email address" />
        </div>
        <div class="mb-4">
            <x-text-input id="loginPassword" :errors="$errors" type="password" name="password" placeholder="Your password" />
        </div>

        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center">
                <input id="loginRememberMe" name="remember" type="checkbox"
                    class="mr-3 rounded border-gray-300 text-purple-500 focus:ring-purple-500" />
                <label for="loginRememberMe">Remember Me</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-sm text-purple-700 hover:text-purple-600">Forgot
                Password?</a>
        </div>
        <button class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 w-full">
            Login
        </button>
    </form>
</x-app-layout>
