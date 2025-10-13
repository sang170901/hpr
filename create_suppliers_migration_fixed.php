<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->json('payment_terms')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}