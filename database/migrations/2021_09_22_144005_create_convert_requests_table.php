<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ConvertRequest;

class CreateConvertRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convert_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id');
            $table->text('url');
            $table->string('type')->default(ConvertRequest::TYPE_YOUTUBE)->comment('youtube,facebook,instagram,pornhub,pornhd');
            $table->string('method')->default(ConvertRequest::METHOD_CONVERSION)->comment('conversion,direct');
            $table->json('details');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convert_requests');
    }
}
