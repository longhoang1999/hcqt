<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jb_sc_id_index')->nullable();
            $table->string('conpany')->nullable();
            $table->string('nr')->nullable();
            $table->string('padmin')->nullable();
            $table->date('dateopenm')->nullable();
            $table->string('dateopen')->nullable();
            $table->text('description')->nullable();
            $table->integer('jpaytype')->nullable();
            $table->string('jstatus')->nullable();
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
        Schema::drop('jobs');
    }
}
