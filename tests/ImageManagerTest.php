<?php declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

final class ImageManagerTest extends KernelTestCase
{
    use UploadsHelper;

    #[Test]
    public function get_images(): void
    {
        $images = [];

        for ($i = 0; $i <= 10; $i++) {
            $this->uploadPendingFile();

            $url = Storage::disk(KernelTestCase::DISK)->url($this->getAttachmentLocation());

            $images[] = [
                'url' => $url,
                'thumb' => $url,
            ];

            $this->regenerateUpload();
        }

        $response = $this->get('nova-vendor/froala/articles/image-manager?field=content');

        usort($images, function ($a, $b) {
            return strcasecmp($a['url'], $b['url']);
        });

        foreach ($images as $image) {
            $response->assertJsonFragment($image);
        }

        $response->assertJsonCount(count($images));
    }

    #[Test]
    public function destroy_image(): void
    {
        $src = $this->uploadPendingFile()->json('link');

        $this->storeArticle();

        $this->deleteJson('nova-vendor/froala/articles/image-manager', ['src' => $src, 'field' => 'content']);

        Storage::disk(self::DISK)->assertMissing($this->getAttachmentLocation());
    }
}
