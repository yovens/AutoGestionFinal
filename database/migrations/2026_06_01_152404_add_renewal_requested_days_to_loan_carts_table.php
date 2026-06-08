<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function up()
{
    Schema::table('loan_carts', function (Blueprint $table) {
        $table->integer('renewal_requested_days')->nullable();
    });
}

public function down()
{
    Schema::table('loan_carts', function (Blueprint $table) {
        $table->dropColumn('renewal_requested_days');
    });
}
};
