<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {

            $table->string('company_domain')->nullable()->after('logo');

            $table->string('verification_code')->nullable();
            $table->timestamp('verification_expiry')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->string('verification_status')->default('pending');

        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {

            $table->dropColumn([
                'company_domain',
                'verification_code',
                'verification_expiry',
                'is_verified',
                'verification_status'
            ]);

        });
    }
};