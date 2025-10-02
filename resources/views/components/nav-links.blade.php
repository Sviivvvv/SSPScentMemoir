@props([
  'isAdmin' => false,            // bool
  'layout' => 'inline',          // 'inline' | 'stacked'
  'linkClass' => '',             // tailwind classes applied to <a> or <button>
  'gapClass' => '',              // optional gap override (defaults below)
  'showCartCounter' => true,     // show Livewire cart counter (customer only)
  'showAuth' => true,            // include Login/Logout
])

@php
  $wrapClass = $layout === 'inline'
      ? 'flex items-center '.($gapClass ?: 'space-x-14')
      : 'flex flex-col '.($gapClass ?: 'space-y-2');

  $asBlock = $layout === 'stacked' ? 'block' : '';
  $a = trim("$asBlock $linkClass");
@endphp

<div {{ $attributes->merge(['class' => $wrapClass]) }}>
  @if($isAdmin)
    {{-- ADMIN LINKS --}}
    <a href="{{ route('admin.home') }}" class="{{ $a }}">Home</a>
    <a href="{{ route('admin.products.index') }}" class="{{ $a }}">Manage Products</a>
    <a href="{{ route('admin.users.index') }}" class="{{ $a }}">Manage Users</a>
    <a href="{{ route('admin.orders.index') }}" class="{{ $a }}">View Orders</a>

    @if($showAuth)
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="{{ $a }}">Logout</button>
      </form>
    @endif

  @else
    {{-- CUSTOMER LINKS --}}
    <a href="{{ route('home') }}" class="{{ $a }}">Home</a>
    <a href="{{ route('products.index') }}" class="{{ $a }}">Products</a>

    <a href="{{ route('cart.index') }}" class="{{ $a }}">
      @if($showCartCounter)
        <span class="inline-flex items-center gap-2">
          Cart <livewire:cart.counter />
        </span>
      @else
        Cart
      @endif
    </a>

    <a href="{{ route('subscriptions.index') }}" class="{{ $a }}">Subscription</a>

    @if($showAuth)
      @auth
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="{{ $a }}">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="{{ $a }}">Login</a>
      @endauth
    @endif
  @endif
</div>
