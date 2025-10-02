<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Single Livewire table view
        return view('admin.users.index');
    }

    public function edit(User $user)
    {
        // Only allow editing customers (not admins)
        abort_if($user->role !== 'customer', 403);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role !== 'customer', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            // we DO NOT let them escalate role to admin from here
        ]);

        $user->update($data);

        return to_route('admin.users.index')->with('status', 'Customer updated.');
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'customer', 403);

        $user->delete();

        return to_route('admin.users.index')->with('status', 'Customer deleted.');
    }
}
