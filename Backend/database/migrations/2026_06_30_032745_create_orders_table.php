<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('table_number')->nullable();
            
            // Billing info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('alt_address')->nullable();
            $table->string('province');
            $table->string('city');
            $table->string('postal_code');
            
            // Order and payment details
            $table->string('invoice_number')->unique();
            $table->string('payment_method')->default('Cash on Delivery');
            $table->string('payment_status')->default('unpaid');
            $table->integer('shipping_cost')->default(0);
            $table->integer('total_price');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
