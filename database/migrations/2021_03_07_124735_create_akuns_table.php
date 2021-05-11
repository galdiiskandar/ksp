<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAkunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->id('noAkun');
            $table->string('nama', 50);
            $table->enum('tipe', ['Aktiva Lancar', 'Aktiva Tetap', 'Harta Tak Berwujud', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban']);
            $table->bigInteger('saldo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('akun');
    }
}
