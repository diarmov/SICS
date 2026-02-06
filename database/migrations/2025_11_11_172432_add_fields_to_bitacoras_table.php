<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBitacorasTable extends Migration
{
    public function up()
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('detalles');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down()
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent']);
        });
    }
}
