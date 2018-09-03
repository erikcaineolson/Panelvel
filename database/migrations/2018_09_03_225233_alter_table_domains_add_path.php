<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDomainsAddPath extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function(Blueprint $table) {
            $table->string('path')->nullable();
            $table->string('is_secure')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table){
            $table->dropColumn('path');
            $table->dropColumn('is_secure');
        });
    }
}
