<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBasketsTable extends Migration
{
    public function up()
    {
        Schema::create('baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_clients')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_vehicles')->constrained('vehicles')->onDelete('cascade');
            $table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('status')->default(0); // 0 = panier
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('baskets');
    }
}
