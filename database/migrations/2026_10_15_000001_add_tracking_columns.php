<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lyen ant yon tranzaksyon ak lokasyon li soti (peman lokasyon + prolongasyon).
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('loan_cart_id')->nullable()->after('vehicle_id')
                ->constrained('loan_carts')->nullOnDelete();
        });

        // Dat machin nan retounen nan pak la (remèt li nan stock).
        Schema::table('loan_carts', function (Blueprint $table) {
            $table->timestamp('returned_at')->nullable()->after('end_date');
        });

        // Pou "Rester connecté" sou paj koneksyon an.
        if (!Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->rememberToken();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loan_cart_id');
        });

        Schema::table('loan_carts', function (Blueprint $table) {
            $table->dropColumn('returned_at');
        });

        if (Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('remember_token');
            });
        }
    }
};
