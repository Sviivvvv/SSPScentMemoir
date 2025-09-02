<x-layouts.site>
    <main class="bg-[#122C4F] text-[#FBF9E4] px-6 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-3 gap-8">
            {{-- Order summary --}}
            <aside class="lg:col-span-1">
                <h2 class="text-2xl font-bold mb-4">Cart Summary</h2>
                <div class="bg-[#FBF9E4] text-[#122C4F] rounded-2xl p-4">
                    <div class="space-y-3 mb-3">
                        @foreach($rows as $row)
                            <div class="flex justify-between">
                                <span class="w-2/3 truncate">{{ $row['product']->name }}</span>
                                <span class="w-1/3 text-right">
                                    LKR {{ number_format($row['subtotal'], 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between border-t pt-3 font-bold">
                        <span>Total</span>
                        <span>LKR {{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </aside>

            {{-- Billing + Payment form --}}
            <section class="lg:col-span-2">
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-8">
                    @csrf

                    {{-- Billing --}}
                    <div>
                        <h2 class="text-2xl font-bold mb-4">Billing Information</h2>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <input name="billing_first_name" value="{{ old('billing_first_name') }}"
                                       placeholder="First Name"
                                       class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                @error('billing_first_name') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <input name="billing_last_name" value="{{ old('billing_last_name') }}"
                                       placeholder="Last Name"
                                       class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                @error('billing_last_name') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <input name="billing_email" value="{{ old('billing_email', auth()->user()->email ?? '') }}"
                                       type="email" placeholder="Email Address"
                                       class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                @error('billing_email') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <input name="billing_phone" value="{{ old('billing_phone') }}"
                                       placeholder="Phone Number"
                                       class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                @error('billing_phone') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <input name="billing_address" value="{{ old('billing_address') }}"
                                       placeholder="Street Address"
                                       class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                @error('billing_address') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <input name="billing_city" value="{{ old('billing_city') }}"
                                       placeholder="Town/City"
                                       class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                @error('billing_city') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <input name="billing_zip" value="{{ old('billing_zip') }}"
                                       placeholder="Postal code/Zip"
                                       class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                @error('billing_zip') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Payment --}}
                    <div x-data="{ method: '{{ old('payment_method','card') }}' }">
                        <h2 class="text-2xl font-bold mb-4">Payment</h2>

                        <div class="flex items-center gap-6 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment_method" value="card"
                                       @change="method='card'" {{ old('payment_method','card') === 'card' ? 'checked' : '' }}>
                                <span>Credit/Debit Card</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment_method" value="cod"
                                       @change="method='cod'" {{ old('payment_method') === 'cod' ? 'checked' : '' }}>
                                <span>Cash on Delivery</span>
                            </label>
                        </div>

                        {{-- Card fields (required only when method=card) --}}
                        <div x-show="method === 'card'" x-transition>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <input name="card_name" value="{{ old('card_name') }}"
                                           placeholder="Cardholder's Name"
                                           class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                    @error('card_name') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <input name="card_number" value="{{ old('card_number') }}"
                                           placeholder="Card Number"
                                           class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                    @error('card_number') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <input name="exp_month" value="{{ old('exp_month') }}" placeholder="Exp. Month (MM)"
                                           class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                    @error('exp_month') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <input name="exp_year" value="{{ old('exp_year') }}" placeholder="Exp. Year (YYYY)"
                                           class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                    @error('exp_year') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <input name="cvv" value="{{ old('cvv') }}" placeholder="CVV"
                                           class="w-full p-2 bg-[#FBF9E4] text-[#122C4F] rounded">
                                    @error('cvv') <div class="text-red-300 text-sm mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full md:w-auto px-6 py-3 bg-[#FBF9E4] text-[#122C4F] font-semibold rounded hover:bg-[#f1edd9]">
                        Place Order
                    </button>
                </form>
            </section>
        </div>
    </main>
</x-layouts.site>
