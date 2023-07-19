<?php declare(strict_types=1);

namespace Tests;

use Froala\Nova\Attachments\PendingAttachment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

final class PreserveFilenamesTest extends TestCase
{
    use UploadsHelper {
        setUp as uplaodsSetUp;
    }

    public function setUp(): void
    {
        $this->uplaodsSetUp();

        $this->app['config']->set('froala-field.preserve_file_names', true);
    }

    #[Test]
    public function save_image(): void
    {
        $response = $this->uploadPendingFile();

        $response->assertJson(['link' => Storage::disk(static::DISK)->url($this->getAttachmentLocation(true))]);

        $this->assertDatabaseHas((new PendingAttachment())->getTable(), [
            'draft_id' => $this->draftId,
            'disk' => static::DISK,
            'attachment' => $this->getAttachmentLocation(true),
        ]);

        // Assert the file was stored...
        Storage::disk(static::DISK)->assertExists($this->getAttachmentLocation(true));
    }

    #[Test]
    public function same_filename_error(): void
    {
        $this->uploadPendingFile();

        $response = $this->uploadPendingFile();

        $response->assertStatus(Response::HTTP_CONFLICT);
    }
}
