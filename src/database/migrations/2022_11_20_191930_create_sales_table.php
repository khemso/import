<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('hash', 32);
            $table->unsignedInteger('seller_id');
            $table->string('seller_firstname', 20);
            $table->string('seller_lastname', 20);
            $table->date('date_joined');
            $table->string('country', 2);
            $table->string('contact_region', 50);
            $table->date('contact_date');
            $table->string('contact_customer_fullname', 50);
            $table->string('contact_type', 10);
            $table->unsignedInteger('contact_product_type_offered_id');
            $table->string('contact_product_type_offered', 50);
            $table->double('sale_net_amount')->nullable();
            $table->double('sale_gross_amount')->nullable();
            $table->double('sale_tax_rate')->nullable();
            $table->double('sale_product_total_cost')->nullable();
            $table->timestamps();

            $table->index(['hash', 'seller_id', 'contact_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
