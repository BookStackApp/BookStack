<?php

namespace Tests\Uploads;

use BookStack\Uploads\ImageStorage;
use Tests\TestCase;

class ImageStorageTest extends TestCase
{
    public function test_local_image_storage_sets_755_directory_permissions()
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->markTestSkipped('Test only works on Linux');
        }

        config()->set('filesystems.default', 'local');
        $storage = $this->app->make(ImageStorage::class);
        $dirToCheck = 'test-dir-perms-' . substr(md5(random_bytes(16)), 0, 6);

        $disk = $storage->getDisk('gallery');
        $disk->put("{$dirToCheck}/image.png", 'abc', true);

        $expectedPath = public_path("uploads/images/{$dirToCheck}");
        $permissionsApplied = substr(sprintf('%o', fileperms($expectedPath)), -4);
        $this->assertEquals('0755', $permissionsApplied);

        @unlink("{$expectedPath}/image.png");
        @rmdir($expectedPath);
    }
}
