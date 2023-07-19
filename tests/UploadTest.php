<?php declare(strict_types=1);

namespace Tests;

use Froala\Nova\Attachments\Attachment;
use Froala\Nova\Attachments\PendingAttachment;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

final class UploadTest extends KernelTestCase
{
    use UploadsHelper;

    #[Test]
    public function store_pending_attachment(): void
    {
        $response = $this->uploadPendingFile();

        $response->assertJson(['link' => Storage::disk(self::DISK)->url($this->getAttachmentLocation())]);

        $this->assertDatabaseHas(PendingAttachment::class, [
            'draft_id' => $this->draftId,
            'disk' => self::DISK,
            'attachment' => $this->getAttachmentLocation(),
        ]);

        // Assert the file was stored...
        Storage::disk(self::DISK)->assertExists($this->getAttachmentLocation());

        // Assert a file does not exist...
        Storage::disk(self::DISK)->assertMissing('missing.jpg');
    }

    #[Test]
    public function store_attachment(): void
    {
        $this->uploadPendingFile();

        $response = $this->storeArticle();

        $response->assertJson(['resource' => ['title' => 'Some title', 'content' => 'Some content']]);
        $this->assertDatabaseHas(Attachment::class, [
            'attachable_id' => $response->json('id'),
            'attachable_type' => Article::class,
            'attachment' => $this->getAttachmentLocation(),
            'disk' => self::DISK,
            'url' => Storage::disk(self::DISK)->url($this->getAttachmentLocation()),
        ]);
    }

    #[Test]
    public function detach_attachment(): void
    {
        $src = $this->uploadPendingFile()->json('link');
        $this->storeArticle();

        Storage::disk(self::DISK)->assertExists($this->getAttachmentLocation());

        $this->deleteJson('nova-vendor/froala/articles/attachments/content', ['src' => $src]);

        Storage::disk(self::DISK)->assertMissing($this->getAttachmentLocation());
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
            Storage::disk(self::DISK)->assertExists($fileName);
        }

        $this->deleteJson("nova-vendor/froala/articles/attachments/content/{$this->draftId}");

        foreach ($fileNames as $fileName) {
            Storage::disk(self::DISK)->assertMissing($fileName);
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
            Storage::disk(self::DISK)->assertExists($fileName);
        }

        $articleResponse = $this->storeArticle();
        $this->deleteJson('nova-api/articles', ['resources' => [$articleResponse->json('id')]]);

        foreach ($fileNames as $fileName) {
            Storage::disk(self::DISK)->assertMissing($fileName);
        }
    }
}
