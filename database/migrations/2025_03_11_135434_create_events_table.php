<?php

use App\Enum\EventStatusEnum;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->integer('capacity')->default(0);
            $table->integer('waitListCapacity')->default(0);
            $table->enum('status', array_column(EventStatusEnum::cases(), 'value'))->default(EventStatusEnum::DRAFT->value);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
