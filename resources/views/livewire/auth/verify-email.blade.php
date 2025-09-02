<div class="bg-[#FBF9E4] p-10 rounded-2xl w-full max-w-lg shadow-xl text-[#122C4F]">
    <h2 class="text-center text-3xl font-semibold mb-6">Verify Email</h2>

    <p class="mb-4">We’ve sent a verification link to your email. Didn’t receive it?</p>

    <div class="flex gap-4 justify-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="bg-[#122C4F] text-[#FBF9E4] px-6 py-2 rounded hover:shadow-xl">Resend Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="underline">Log Out</button>
        </form>
    </div>
</div>