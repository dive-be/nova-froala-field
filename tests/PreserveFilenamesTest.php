<?php declare(strict_types=1);

namespace Tests;

use Froala\Nova\Attachments\PendingAttachment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

final class PreserveFilenamesTest extends KernelTestCase
{
    use UploadsHelper;

    #[Test]
    public function save_image(): void
    {
        $this->app['config']->set('froala.preserve_file_names', true);
        $response = $this->uploadPendingFile();

        $response->assertJson(['link' => Storage::disk(self::DISK)->url($this->getAttachmentLocation(true))]);

        $this->assertDatabaseHas((new PendingAttachment())->getTable(), [
            'draft_id' => $this->draftId,
            'disk' => self::DISK,
            'attachment' => $this->getAttachmentLocation(true),
        ]);

        // Assert the file was stored...
        Storage::disk(self::DISK)->assertExists($this->getAttachmentLocation(true));
    }

    #[Test]
    public function same_filename_error(): void
    {
        $this->app['config']->set('froala.preserve_file_names', true);
        $this->uploadPendingFile();

        $response = $this->uploadPendingFile();

        $response->assertStatus(Response::HTTP_CONFLICT);
    }
}
