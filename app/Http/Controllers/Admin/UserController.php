<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $role = $request->query('role');

        $users = User::query()
            ->withCount('loans', 'transactions')
            ->when($search, fn ($q) => $q->where(fn ($q) => $q
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('telephone', 'like', "%{$search}%")))
            ->when(in_array($role, [User::ADMIN, User::CLIENT], true), fn ($q) => $q->where('role', $role))
            ->orderBy('nom')
            ->get();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'       => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:30',
            'password'  => 'required|string|min:8',
            'role'      => ['required', Rule::in([User::CLIENT, User::ADMIN])],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = User::ACTIVE;

        $user = User::create($data);

        return redirect()->route('admin.users.show', $user)->with('success', "Compte de {$user->nom} créé.");
    }

    public function show(User $user)
    {
        LoanCart::syncExpired();

        $user->load([
            'loans'        => fn ($q) => $q->with('vehicle')->latest(),
            'transactions' => fn ($q) => $q->with('vehicle')->latest(),
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nom'       => 'required|string|max:255',
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => 'nullable|string|max:30',
            'role'      => ['required', Rule::in([User::CLIENT, User::ADMIN])],
        ]);

        if ($user->is(auth()->user()) && $data['role'] !== $user->role) {
            return back()->with('error', 'Vous ne pouvez pas changer votre propre rôle.');
        }

        $user->update($data);

        return back()->with('success', 'Compte mis à jour.');
    }

    public function block(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'Vous ne pouvez pas suspendre votre propre compte.');
        }

        $user->update(['status' => $user->isBlocked() ? User::ACTIVE : User::BLOCKED]);

        return back()->with('success', $user->isBlocked() ? "Compte de {$user->nom} suspendu." : "Compte de {$user->nom} réactivé.");
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->loans()->whereIn('status', LoanCart::HOLDING)->exists()) {
            return back()->with('error', 'Ce client a une location en cours. Enregistrez le retour avant de supprimer le compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Compte supprimé.');
    }
}
