<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('sku')->unique();
            $table->decimal('price', 15, 2);
            $table->decimal('compare_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->boolean('track_stock')->default(true);
            $table->boolean('status')->default(true);
            $table->boolean('featured')->default(false);
            $table->json('images')->nullable();
            $table->json('specifications')->nullable();
            $table->json('seo_meta')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->index(['status', 'featured', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}