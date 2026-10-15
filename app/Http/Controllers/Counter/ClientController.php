<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $clients = User::where('role', User::CLIENT)
            ->withCount(['loans', 'loans as active_count' => fn ($q) => $q->whereIn('status', LoanCart::HOLDING)])
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('telephone', 'like', "%{$search}%")))
            ->orderBy('nom')->take(200)->get();

        return view('counter.clients.index', compact('clients', 'search'));
    }

    /**
     * Kont pou yon kliyan ki vin nan ajans lan san kont.
     * Yon modpas pwovizwa jenere; kliyan an ka chanje l pita oswa konekte ak Google.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'       => 'required|string|max:255',
            'telephone' => 'required|string|max:30',
            'email'     => 'nullable|email|max:255|unique:users',
        ], [], ['telephone' => 'téléphone']);

        $password = Str::upper(Str::random(4)) . random_int(1000, 9999);

        $client = User::create([
            'nom'       => $data['nom'],
            'telephone' => $data['telephone'],
            // E-mail obligatwa nan tab la: si kliyan an pa genyen, nou kreye yon adrès entèn.
            'email'     => $data['email'] ?? 'client' . now()->format('ymdHis') . random_int(10, 99) . '@comptoir.autogestion.local',
            'password'  => Hash::make($password),
            'role'      => User::CLIENT,
            'status'    => User::ACTIVE,
        ]);

        $back = $request->input('return_to') === 'loan'
            ? redirect()->route('counter.loans.create', ['client' => $client->id])
            : redirect()->route('counter.clients.show', $client);

        $message = "Client {$client->nom} enregistré.";
        if (!empty($data['email'])) {
            $message .= " Mot de passe provisoire à lui communiquer : {$password}";
        }

        return $back->with('success', $message);
    }

    public function show(User $user)
    {
        abort_unless($user->role === User::CLIENT, 404);

        LoanCart::syncExpired();

        $user->load([
            'loans'        => fn ($q) => $q->with('vehicle')->latest(),
            'transactions' => fn ($q) => $q->with('vehicle')->latest(),
        ]);

        return view('counter.clients.show', ['client' => $user]);
    }
}
