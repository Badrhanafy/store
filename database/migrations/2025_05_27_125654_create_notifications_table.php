<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Laravel's default notification columns
            $table->uuid('id')->primary();
            $table->string('type');  // Stores the notification class name (e.g., App\Notifications\NewUserNotification)
            $table->morphs('notifiable'); // Creates notifiable_id and notifiable_type columns
            $table->text('data');    // JSON data including your custom message and details
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Add index for better performance
            $table->index(['notifiable_id', 'notifiable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}