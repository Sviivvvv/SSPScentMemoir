<header>
    <div class="w-full px-8 py-4 relative">
        <div class="absolute mt-3 top-4 left-6">
            <p class="text-xl sm:text-3xl md:text-4xl font-bold">Scent Memoir</p>
        </div>

        @php
            $isAdmin = auth()->check() && (auth()->user()->role ?? 'customer') === 'admin';
        @endphp

        <nav class="mt-20">
            {{-- Desktop --}}
            <div class="hidden md:flex justify-end space-x-14">
                @if($isAdmin)
                    {{-- ADMIN NAV --}}
                    <a href="{{ route('admin.home') }}" class="text-md font-bold hover:underline">Home</a>
                    <a href="{{ route('admin.products.index') }}" class="text-md font-bold hover:underline">Manage
                        Products</a>
                    <a href="{{ route('admin.users.index') }}"  class="text-md font-bold hover:underline">Manage Users</a>
                    <a href="{{ route('admin.orders.index') }}"  class="text-md font-bold hover:underline">View Orders</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-md font-bold hover:underline">Logout</button>
                    </form>
                @else
                    {{-- CUSTOMER NAV --}}
                    <a href="{{ route('home') }}" class="text-md font-bold hover:underline">Home</a>
                    <a href="{{ route('products.index') }}" class="text-md font-bold hover:underline">Products</a>

                    <a href="{{ route('cart.index') }}" class="text-md font-bold hover:underline">
                        <livewire:cart.counter />
                    </a>

                    <a href="{{ route('subscriptions.index') }}" class="text-md font-bold hover:underline">Subscription</a>

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-md font-bold hover:underline">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-md font-bold hover:underline">Login</a>
                    @endauth
                @endif
            </div>
        </nav>
    </div>
</header>