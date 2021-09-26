<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ConvertRequestItem;

class CreateConvertRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convert_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('convert_request_id')->comment('REQUIRED: convert_requests->id');
            $table->uuid('file_id');
            $table->string('file_type')->comment('REQUIRED: mp3, mp4');
            $table->string('quality')->comment('REQUIRED: 480p, 720p, 1080p, best');
            $table->string('format_id')->comment('Required: 155+140, 140');
            $table->integer('status')->default(ConvertRequestItem::STATUS_PROCESSING)->comment('REQUIRED: 0 = processing, 1 = downloaded, 2 = converted, 400 = bad request');
            $table->json('details')->nullable();
            $table->text('path')->nullable();
            $table->text('url')->nullable();
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
        Schema::dropIfExists('convert_items');
    }
}
