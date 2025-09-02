<footer class="bg-[#122C4F] border-t mt-8">
    <div class="container mx-auto px-4 py-6">

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
                <div class="space-y-2">
                    <a href="{{ route('home') }}" class="block text-sm hover:underline">Home</a>
                    <a href="#" class="block text-sm hover:underline">Products</a>
                    <a href="#" class="block text-sm hover:underline">Cart</a>
                    <a href="#" class="block text-sm hover:underline">Subscription</a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-lg">Socials</h4>
                <div class="flex justify-center space-x-6">
                    <a href="https://instagram.com" target="_blank"><img src="/src/otherPics/instaIcon.png"
                            class="w-9 h-9"></a>
                    <a href="https://facebook.com" target="_blank"><img src="/src/otherPics/FbIcon.png"
                            class="w-9 h-9"></a>
                    <a href="https://tiktok.com" target="_blank"><img src="/src/otherPics/TiktokIcon.png"
                            class="w-9 h-9"></a>
                </div>
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
                <a href="{{ route('home') }}" class="block hover:underline">Home</a>
                <a href="#" class="block hover:underline">Products</a>
                <a href="#" class="block hover:underline">Cart</a>
                <a href="#" class="block hover:underline">Subscription</a>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Socials</h4>
                <div class="flex space-x-4">
                    <a href="https://instagram.com" target="_blank"><img src="/src/otherPics/instaIcon.png"
                            class="w-8 h-8"></a>
                    <a href="https://facebook.com" target="_blank"><img src="/src/otherPics/FbIcon.png"
                            class="w-8 h-8"></a>
                    <a href="https://tiktok.com" target="_blank"><img src="/src/otherPics/TiktokIcon.png"
                            class="w-8 h-8"></a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Newsletter</h4>
                <input type="email" placeholder="Your email" class="w-full border px-2 py-1 rounded mb-2 text-sm">
                <button class="w-full bg-[#FBF9E4] text-[#122C4F] text-sm py-1 rounded">Subscribe</button>
            </div>
        </div>
    </div>
</footer>