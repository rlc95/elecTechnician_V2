<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username',50)->unique()->nullable();
            $table->string('email',191)->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('picture')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('provinces')->nullable();
            $table->string('districts')->nullable();
            $table->string('cities')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('verified')->default(0);
            $table->enum('status', ['Pending', 'Active'])->default('Pending');
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
        Schema::dropIfExists('clients');
    }
}
