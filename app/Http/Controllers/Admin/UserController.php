<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function index() {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create() { return view('admin.users.create'); }



public function store(Request $request) {
    $request->validate([
        'nom' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
        'role' => 'required|in:CLIENT,ADMIN',
    ]);

    User::create([
        'nom' => $request->nom,
        'email' => $request->email,
        'password' => Hash::make($request->password), // 🔑 hash du mot de passe
        'role' => $request->role,
        'status' => 'ACTIVE'
    ]);

    return redirect()->route('admin.users.index')->with('success','Utilisateur créé');
}

    public function edit(User $user) { return view('admin.users.edit', compact('user')); }

    public function update(Request $request, User $user) {
        $user->update($request->only('nom','email','role','status'));
        return redirect()->route('admin.users.index')->with('success','Utilisateur mis à jour');
    }
public function block($id)
{
    $user = User::findOrFail($id);

    // Toggle status
    $user->status = $user->status === 'ACTIVE' ? 'BLOCKED' : 'ACTIVE';
    $user->save();

    return redirect()->back()->with('success', 'Statut utilisateur mis à jour !');
}

    public function destroy(User $user) {
        $user->delete();
        return back()->with('success','Utilisateur supprimé');
    }
    public function show(User $user)
{
    $user->load([
        'transactions',
        'loans.vehicle'
    ]);

    return view('admin.users.show', compact('user'));
}
}




