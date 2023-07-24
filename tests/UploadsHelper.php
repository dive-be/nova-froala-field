<?php declare(strict_types=1);

namespace Tests;

use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/** @mixin KernelTestCase */
trait UploadsHelper
{
    protected string $draftId;

    protected File $file;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake(KernelTestCase::DISK);

        $this->draftId = (string) Str::uuid();

        $this->regenerateUpload();
    }

    protected function regenerateUpload(): void
    {
        $this->file = UploadedFile::fake()->image('picture' . rand(1, 100) . '.jpg');
    }

    protected function uploadPendingFile(): TestResponse
    {
        return $this->postJson('nova-vendor/froala/articles/attachments/content', [
            'draftId' => $this->draftId,
            'attachment' => $this->file,
        ]);
    }

    protected function storeArticle(): TestResponse
    {
        return $this->postJson('nova-api/articles', [
            'title' => 'Some title',
            'content' => 'Some content',
            'contentDraftId' => $this->draftId,
        ]);
    }

    protected function getAttachmentLocation(): string
    {
        return rtrim(KernelTestCase::PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->file->hashName();
    }
}
