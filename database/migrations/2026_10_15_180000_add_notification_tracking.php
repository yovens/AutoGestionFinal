<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pou pa voye menm rapèl la de fwa.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_carts', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('late_notice_sent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('loan_carts', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'late_notice_sent_at']);
        });
    }
};
