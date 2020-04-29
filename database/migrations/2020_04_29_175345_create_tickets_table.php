<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user', false, true);
            $table->string('subject');
            $table->mediumText('content');
            $table->string('attachment')->nullable()->default(null);
            $table->integer('manager', false, true)->nullable()->default(null);
            $table->mediumText('reply')->nullable()->default(null);
            $table->timestamp('replied_at')->nullable()->default(null);
            $table->boolean('checked')->default(false);
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
