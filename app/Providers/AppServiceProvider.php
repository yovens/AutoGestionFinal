<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Support\Agency;
use App\Support\Audit;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // @money($montant) → "12 500 $"
        Blade::directive('money', fn ($expr) => "<?php echo e(\\App\\Providers\\AppServiceProvider::money($expr)); ?>");

        // Enfòmasyon ajans lan disponib nan tout vues yo: $agency['phone'], $agency['address']...
        View::composer('*', fn ($view) => $view->with('agency', Agency::all()));

        // Jounal: koneksyon anplwaye yo
        Event::listen(Login::class, function (Login $event) {
            if (method_exists($event->user, 'isStaff') && $event->user->isStaff()) {
                Audit::log('auth.login', "Connexion de {$event->user->nom}", $event->user);
            }
        });

        // Badj nan meni admin nan: sa k ap tann yon aksyon.
        View::composer('layouts.admin', function ($view) {
            $view->with('nav', [
                'loans'    => LoanCart::whereIn('status', [LoanCart::PENDING, LoanCart::PENDING_RENEWAL])->count(),
                'counter'  => LoanCart::where('status', LoanCart::READY)->count()
                              + LoanCart::where('status', LoanCart::EXPIRED)->count(),
                'payments' => Transaction::where('status', Transaction::PENDING)->count(),
                'messages' => Contact::where('is_read', false)->count(),
            ]);
        });

        View::composer('layouts.client', function ($view) {
            $user = auth()->user();
            $view->with('nav', [
                'cart' => $user ? $user->cartItems()->count() : 0,
            ]);
        });
    }

    public static function money($amount): string
    {
        return number_format((float) $amount, 0, ',', "\u{202F}") . "\u{00A0}" . config('app.currency_symbol', '$');
    }
}
