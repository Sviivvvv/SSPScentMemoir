<header>
    <div class="w-full px-8 py-4 relative">
        <div class="absolute mt-3 top-4 left-6">
            <p class="text-xl sm:text-3xl md:text-4xl font-bold">Scent Memoir</p>
        </div>
        <nav class="mt-20">
            <div class="hidden md:flex justify-end space-x-14">
                <a href="{{ route('home') }}" class="text-md font-bold hover:underline">Home</a>
                <a href="{{ route('products.index') }}" class="text-md font-bold hover:underline">Products</a>
                <li>
                    <a href="{{ route('cart.index') }}" class="font-bold hover:underline">
                        <livewire:cart.counter />
                    </a>
                </li>
                <a href="#" class="text-md font-bold hover:underline">Subscription</a>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf <button class="text-md font-bold hover:underline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-md font-bold hover:underline">Login</a>
                @endauth
            </div>
        </nav>
    </div>
</header>