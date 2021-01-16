<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('firstname');
            $table->string('middlename');
            $table->string('surname');
            $table->string('address');
            $table->string('contactNumber');
            $table->string('gender');
            $table->string('civilStatus');
            $table->date('birthday');
            $table->string('birthplace');
            $table->string('batch');
            $table->string('wiresign');
            $table->date('dateEmployed');
            $table->string('licenseNumber')->unique();
            $table->date('ATCLicenseExpiry');
            $table->date('medicalLicenseExpiry');
            $table->text('facility');
            $table->string('membership_status');
            $table->unsignedBigInteger('user_id')->index();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
