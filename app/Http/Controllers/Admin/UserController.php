<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Kont yo. Règ:
 *  - admin: jere kliyan ak sekretè sèlman
 *  - super admin: jere tout kont, enkli admin ak lòt super admin
 *  - pèsonn pa ka modifye pwòp wòl li oswa bloke/efase pwòp kont li
 *  - toujou dwe gen omwen yon super admin aktif
 */
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
            ->when($role === 'staff', fn ($q) => $q->whereIn('role', User::STAFF))
            ->when(array_key_exists((string) $role, User::ROLE_LABELS), fn ($q) => $q->where('role', $role))
            ->orderByRaw("CASE role WHEN 'SUPER_ADMIN' THEN 0 WHEN 'ADMIN' THEN 1 WHEN 'SECRETAIRE' THEN 2 ELSE 3 END")
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
            'role'      => ['required', Rule::in(array_keys($request->user()->assignableRoles()))],
        ], ['role.in' => 'Vous ne pouvez pas créer un compte avec ce rôle.']);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = User::ACTIVE;

        $user = User::create($data);
        Audit::log('user.create', "Compte créé : {$user->nom} ({$user->role_label})", $user);

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
        $me = $request->user();

        if (!$me->is($user) && !$me->canManage($user)) {
            return back()->with('error', 'Seul le super administrateur peut modifier un compte administrateur.');
        }

        $data = $request->validate([
            'nom'       => 'required|string|max:255',
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => 'nullable|string|max:30',
            'role'      => ['required', Rule::in(array_keys(User::ROLE_LABELS))],
        ]);

        if ($data['role'] !== $user->role) {
            if ($me->is($user)) {
                return back()->with('error', 'Vous ne pouvez pas changer votre propre rôle.');
            }
            if (!array_key_exists($data['role'], $me->assignableRoles())) {
                return back()->with('error', 'Vous ne pouvez pas attribuer ce rôle.');
            }
            if ($this->isLastSuperAdmin($user)) {
                return back()->with('error', 'Il doit rester au moins un super administrateur actif.');
            }
        }

        $oldRole = $user->role_label;
        $user->update($data);

        if ($user->wasChanged('role')) {
            Audit::log('user.role', "Rôle de {$user->nom} : {$oldRole} → {$user->role_label}", $user);
        } elseif ($user->wasChanged()) {
            Audit::log('user.update', "Compte de {$user->nom} modifié", $user, ['champs' => array_keys($user->getChanges())]);
        }

        return back()->with('success', 'Compte mis à jour.');
    }

    public function block(Request $request, User $user)
    {
        if (!$request->user()->canManage($user)) {
            return back()->with('error', 'Vous ne pouvez pas suspendre ce compte.');
        }

        if (!$user->isBlocked() && $this->isLastSuperAdmin($user)) {
            return back()->with('error', 'Il doit rester au moins un super administrateur actif.');
        }

        $user->update(['status' => $user->isBlocked() ? User::ACTIVE : User::BLOCKED]);
        Audit::log($user->isBlocked() ? 'user.block' : 'user.unblock', ($user->isBlocked() ? 'Compte suspendu : ' : 'Compte réactivé : ') . $user->nom, $user);

        return back()->with('success', $user->isBlocked() ? "Compte de {$user->nom} suspendu." : "Compte de {$user->nom} réactivé.");
    }

    public function destroy(Request $request, User $user)
    {
        if (!$request->user()->canManage($user)) {
            return back()->with('error', 'Vous ne pouvez pas supprimer ce compte.');
        }

        if ($this->isLastSuperAdmin($user)) {
            return back()->with('error', 'Il doit rester au moins un super administrateur actif.');
        }

        if ($user->loans()->whereIn('status', LoanCart::HOLDING)->exists()) {
            return back()->with('error', 'Ce client a une location en cours. Enregistrez le retour avant de supprimer le compte.');
        }

        Audit::log('user.delete', "Compte supprimé : {$user->nom} ({$user->role_label}, {$user->email})");
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Compte supprimé.');
    }

    private function isLastSuperAdmin(User $user): bool
    {
        return $user->isSuperAdmin()
            && User::where('role', User::SUPER_ADMIN)->where('status', User::ACTIVE)->whereKeyNot($user->id)->doesntExist();
    }
}
