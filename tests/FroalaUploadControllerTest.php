<?php declare(strict_types=1);

namespace Tests;

use Froala\Nova\Attachments\Attachment;
use Froala\Nova\Attachments\PendingAttachment;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\Article;

final class FroalaUploadControllerTest extends TestCase
{
    use UploadsHelper;

    #[Test]
    public function store_pending_attachment(): void
    {
        $response = $this->uploadPendingFile();

        $response->assertJson(['link' => Storage::disk(static::DISK)->url($this->getAttachmentLocation())]);

        $this->assertDatabaseHas((new PendingAttachment())->getTable(), [
            'draft_id' => $this->draftId,
            'disk' => static::DISK,
            'attachment' => $this->getAttachmentLocation(),
        ]);

        // Assert the file was stored...
        Storage::disk(static::DISK)->assertExists($this->getAttachmentLocation());

        // Assert a file does not exist...
        Storage::disk(static::DISK)->assertMissing('missing.jpg');
    }

    #[Test]
    public function store_attachment(): void
    {
        $this->uploadPendingFile();

        $response = $this->storeArticle();

        $response->assertJson([
            'resource' => [
                'title' => 'Some title',
                'content' => 'Some content',
            ],
        ]);

        $this->assertDatabaseHas((new Attachment())->getTable(), [
            'disk' => static::DISK,
            'attachment' => $this->getAttachmentLocation(),
            'url' => Storage::disk(static::DISK)->url($this->getAttachmentLocation()),
            'attachable_id' => $response->json('id'),
            'attachable_type' => Article::class,
        ]);
    }

    #[Test]
    public function detach_attachment(): void
    {
        $src = $this->uploadPendingFile()->json('link');

        $this->storeArticle();

        Storage::disk(static::DISK)->assertExists($this->getAttachmentLocation());

        $this->json('DELETE', 'nova-vendor/froala-field/articles/attachments/content', [
            'src' => $src,
        ]);

        Storage::disk(static::DISK)->assertMissing($this->getAttachmentLocation());
    }

    #[Test]
    public function discard_pending_attachments(): void
    {
        $fileNames = [];

        for ($i = 0; $i <= 3; $i++) {
            $this->uploadPendingFile();

            $fileNames[] = $this->getAttachmentLocation();

            $this->regenerateUpload();
        }

        foreach ($fileNames as $fileName) {
            Storage::disk(static::DISK)->assertExists($fileName);
        }

        $this->json('DELETE', 'nova-vendor/froala-field/articles/attachments/content/' . $this->draftId);

        foreach ($fileNames as $fileName) {
            Storage::disk(static::DISK)->assertMissing($fileName);
        }
    }

    #[Test]
    public function delete_all_related_attachments(): void
    {
        $fileNames = [];

        for ($i = 0; $i <= 5; $i++) {
            $this->uploadPendingFile();

            $fileNames[] = $this->getAttachmentLocation();

            $this->regenerateUpload();
        }

        foreach ($fileNames as $fileName) {
            Storage::disk(static::DISK)->assertExists($fileName);
        }

        $articleResponse = $this->storeArticle();

        $this->json('DELETE', 'nova-api/articles', [
            'resources' => [(int) $articleResponse->json('id')],
        ]);

        foreach ($fileNames as $fileName) {
            Storage::disk(static::DISK)->assertMissing($fileName);
        }
    }
}
