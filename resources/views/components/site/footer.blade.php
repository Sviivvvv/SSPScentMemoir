<footer class="bg-[#122C4F] border-t mt-8">
    <div class="container mx-auto px-4 py-6">

        @php
            $isAdmin = auth()->check() && (auth()->user()->role ?? 'customer') === 'admin';
        @endphp

        {{-- Mobile --}}
        <div class="md:hidden flex flex-col items-center space-y-8 text-center">
            <div>
                <h4 class="font-semibold mb-3 text-lg">Contact Us</h4>
                <div class="space-y-1">
                    <p>Phone: 110-000-0001</p>
                    <p>Email: ScentMemoir@gmail.com</p>
                    <p>Address: Union Pl, Colombo</p>
                </div>
            </div>

            <div>
                <h4 class="font-semibold mb-3 text-lg">Quick Links</h4>

                {{-- ADMIN QUICK LINKS --}}
                @if($isAdmin)
                    <div class="space-y-2">
                        <a href="{{ route('admin.home') }}" class="block text-sm hover:underline">Home</a>
                        <a href="{{ route('admin.products.index') }}" class="block text-sm hover:underline">Manage
                            Products</a>
                        <a href="{{ route('admin.users.index') }}" class="block text-sm hover:underline">Manage Users</a>
                        <a href="{{ route('admin.orders.index') }}" class="block text-sm hover:underline">View Orders</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full text-left text-sm hover:underline">Logout</button>
                        </form>
                    </div>
                @else
                    {{-- CUSTOMER QUICK LINKS --}}
                    <div class="space-y-2">
                        <a href="{{ route('home') }}" class="block text-sm hover:underline">Home</a>
                        <a href="{{ route('products.index') }}" class="block text-sm hover:underline">Products</a>
                        <a href="{{ route('cart.index') }}" class="block text-sm hover:underline">
                            <span class="inline-flex items-center gap-2">
                                Cart
                                <livewire:cart.counter />
                            </span>
                        </a>
                        <a href="{{ route('subscriptions.index') }}" class="block text-sm hover:underline">Subscription</a>

                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="block w-full text-left text-sm hover:underline">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block text-sm hover:underline">Login</a>
                        @endauth
                    </div>
                @endif
            </div>

            <div>
                <h4 class="font-semibold mb-3 text-lg">Socials</h4>
                <nav class="flex justify-center space-x-5 text-[#FBF9E4]" aria-label="Social logins">
                    <a href="https://www.instagram.com/accounts/login/" target="_blank" rel="noopener noreferrer"
                        aria-label="Instagram login" class="hover:opacity-80">
                        <x-si-instagram class="w-5 h-5 align-middle fill-current" />
                    </a>
                    <a href="https://www.facebook.com/login/" target="_blank" rel="noopener noreferrer"
                        aria-label="Facebook login" class="hover:opacity-80">
                        <x-si-facebook class="w-5 h-5 align-middle fill-current" />
                    </a>
                    <a href="https://www.tiktok.com/login" target="_blank" rel="noopener noreferrer"
                        aria-label="TikTok login" class="hover:opacity-80">
                        <x-si-tiktok class="w-5 h-5 align-middle fill-current" />
                    </a>
                </nav>
            </div>

            <div class="w-full max-w-xs">
                <h4 class="font-semibold mb-3 text-lg">Newsletter</h4>
                <input type="email" placeholder="Your email" class="w-full border px-3 py-2 rounded mb-3 text-sm">
                <button class="w-full bg-[#FBF9E4] text-[#122C4F] text-sm py-2 rounded">Subscribe</button>
            </div>
        </div>

        {{-- Desktop --}}
        <div class="hidden md:grid grid-cols-4 gap-4 text-md font-semibold">
            <div>
                <h4 class="font-semibold mb-2">Contact Us</h4>
                <p>Phone: 110-000-0001</p>
                <p>Email: ScentMemoir@gmail.com</p>
                <p>Address: Union Pl, Colombo</p>
            </div>

            <div>
                <h4 class="font-semibold mb-2">Quick Links</h4>

                {{-- ADMIN QUICK LINKS --}}
                @if($isAdmin)
                    <a href="{{ route('admin.home') }}" class="block hover:underline">Home</a>
                    <a href="{{ route('admin.products.index') }}" class="block hover:underline">Manage Products</a>
                    <a href="{{ route('admin.users.index') }}" class="block hover:underline">Manage Users</a>
                    <a href="{{ route('admin.orders.index') }}" class="block hover:underline">View Orders</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button class="hover:underline">Logout</button>
                    </form>
                @else
                    {{-- CUSTOMER QUICK LINKS --}}
                    <a href="{{ route('home') }}" class="block hover:underline">Home</a>
                    <a href="{{ route('products.index') }}" class="block hover:underline">Products</a>
                    <a href="{{ route('cart.index') }}" class="block hover:underline">
                        <span class="inline-flex items-center gap-2">
                            Cart
                            <livewire:cart.counter />
                        </span>
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="block hover:underline">Subscription</a>

                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button class="hover:underline">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block hover:underline">Login</a>
                    @endauth
                @endif
            </div>

            <div>
                <h4 class="font-semibold mb-2">Socials</h4>
                <nav class="flex items-center space-x-4 text-[#FBF9E4]" aria-label="Social logins">
                    <a href="https://www.instagram.com/accounts/login/" target="_blank" rel="noopener noreferrer"
                        aria-label="Instagram login" class="hover:opacity-80">
                        <x-si-instagram class="w-5 h-5 align-middle fill-current" />
                    </a>
                    <a href="https://www.facebook.com/login/" target="_blank" rel="noopener noreferrer"
                        aria-label="Facebook login" class="hover:opacity-80">
                        <x-si-facebook class="w-5 h-5 align-middle fill-current" />
                    </a>
                    <a href="https://www.tiktok.com/login" target="_blank" rel="noopener noreferrer"
                        aria-label="TikTok login" class="hover:opacity-80">
                        <x-si-tiktok class="w-5 h-5 align-middle fill-current" />
                    </a>
                </nav>
            </div>

            <div>
                <h4 class="font-semibold mb-2">Newsletter</h4>
                <input type="email" placeholder="Your email" class="w-full border px-2 py-1 rounded mb-2 text-sm">
                <button class="w-full bg-[#FBF9E4] text-[#122C4F] text-sm py-1 rounded">Subscribe</button>
            </div>
        </div>
    </div>
</footer>