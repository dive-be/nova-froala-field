<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('pending_froala_attachments', static function (Blueprint $table) {
            $table->id();
            $table->string('draft_id')->index();
            $table->string('attachment');
            $table->string('disk');
            $table->timestamps();
        });

        Schema::create('froala_attachments', static function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');
            $table->string('attachment');
            $table->string('disk');
            $table->string('url')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_froala_attachments');
        Schema::dropIfExists('froala_attachments');
    }
};
