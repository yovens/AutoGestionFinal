<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Modil comptoir (sekretè):
 *  - wòl yo vin yon tèks lib (CLIENT, ADMIN, SECRETAIRE, epi pita SUPER_ADMIN)
 *  - lokasyon yo gen kòd retrè + enfòmasyon remiz kle / retou
 *  - tranzaksyon yo gen mòd peman + kiyès ki ankese
 *  - tikè (bon de sortie, bon de retour, bon de vente)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('CLIENT')->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('type', 20)->change();
            $table->string('method', 20)->nullable()->after('status');
            $table->foreignId('recorded_by')->nullable()->after('method')->constrained('users')->nullOnDelete();
            $table->string('note')->nullable()->after('recorded_by');
        });

        Schema::table('loan_carts', function (Blueprint $table) {
            $table->string('pickup_code', 12)->nullable()->unique()->after('status');
            $table->timestamp('picked_up_at')->nullable()->after('end_date');
            $table->foreignId('checkout_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checkin_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('id_document', 60)->nullable();
            $table->unsignedInteger('km_out')->nullable();
            $table->unsignedInteger('km_in')->nullable();
            $table->unsignedTinyInteger('fuel_out')->nullable(); // 0 à 4 quarts
            $table->unsignedTinyInteger('fuel_in')->nullable();
            $table->text('condition_out')->nullable();
            $table->text('condition_in')->nullable();
            $table->unsignedInteger('deposit')->default(0);
            $table->boolean('deposit_returned')->default(false);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20);                 // sortie, retour, vente
            $table->foreignId('loan_cart_id')->nullable()->constrained('loan_carts')->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();      // kliyan an
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();    // anplwaye a
            $table->unsignedInteger('amount')->default(0);
            $table->json('data')->nullable();           // foto enfòmasyon yo nan moman an (km, kabiran, liy montan...)
            $table->timestamps();
            $table->index(['created_at', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');

        Schema::table('loan_carts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checkout_by');
            $table->dropConstrainedForeignId('checkin_by');
            $table->dropUnique(['pickup_code']);
            $table->dropColumn([
                'pickup_code', 'picked_up_at', 'id_document', 'km_out', 'km_in', 'fuel_out', 'fuel_in',
                'condition_out', 'condition_in', 'deposit', 'deposit_returned',
            ]);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recorded_by');
            $table->dropColumn(['method', 'note']);
        });
    }
};
