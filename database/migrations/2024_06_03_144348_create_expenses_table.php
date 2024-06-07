<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_code');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->unsignedBigInteger('reason_id');
            $table->string('voutcher')->nullable();
            $table->string('note')->nullable();
            $table->date('date_paid')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reason_id')->references('id')->on('reasons');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
