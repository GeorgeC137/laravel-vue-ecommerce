<x-app-layout>
    <form action="{{ route('register') }}" method="POST" class="w-[400px] mx-auto p-6 my-16">
        @csrf
        <h2 class="text-2xl font-semibold text-center mb-4">Create an account</h2>
        <p class="text-center text-gray-500 mb-3">
            or
            <a href="{{ route('login') }}" class="text-sm text-purple-700 hover:text-purple-600">login with existing
                account</a>
        </p>
        <div class="mb-4">
            <x-text-input placeholder="Your name" :value="old('name')" type="text" name="name" />

        </div>
        </p>
        <div class="mb-4">
            <x-text-input placeholder="Your Email" :value="old('email')" type="email" name="email" />

        </div>
        <div class="mb-4">
            <x-text-input placeholder="Password" type="password" name="password" />

        </div>
        </div>
        <div class="mb-4">
            <x-text-input placeholder="Repeat Password" type="password" name="password_confirmation" />

        </div>

        <button class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 w-full">
            Signup
        </button>
    </form>
</x-app-layout>
