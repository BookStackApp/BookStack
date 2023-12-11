<?php

namespace Tests\Uploads;

use BookStack\Uploads\Image;
use Tests\TestCase;

class DrawioTest extends TestCase
{
    public function test_get_image_as_base64()
    {
        $page = $this->entities->page();
        $this->asAdmin();
        $imageName = 'first-image.png';

        $this->files->uploadGalleryImage($this, $imageName, $page->id);
        /** @var Image $image */
        $image = Image::query()->first();
        $image->type = 'drawio';
        $image->save();

        $imageGet = $this->getJson("/images/drawio/base64/{$image->id}");
        $imageGet->assertJson([
            'content' => 'iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAIAAAACDbGyAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4gEcDCo5iYNs+gAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAFElEQVQI12O0jN/KgASYGFABqXwAZtoBV6Sl3hIAAAAASUVORK5CYII=',
        ]);
    }

    public function test_non_accessible_image_returns_404_error_and_message()
    {
        $page = $this->entities->page();
        $this->asEditor();
        $imageName = 'non-accessible-image.png';

        $this->files->uploadGalleryImage($this, $imageName, $page->id);
        /** @var Image $image */
        $image = Image::query()->first();
        $image->type = 'drawio';
        $image->save();
        $this->permissions->disableEntityInheritedPermissions($page);

        $imageGet = $this->getJson("/images/drawio/base64/{$image->id}");
        $imageGet->assertNotFound();
        $imageGet->assertJson([
            'message' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',
        ]);
    }

    public function test_drawing_base64_upload()
    {
        $page = $this->entities->page();
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $upload = $this->postJson('images/drawio', [
            'uploaded_to' => $page->id,
            'image'       => 'image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAIAAAACDbGyAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4gEcDCo5iYNs+gAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAFElEQVQI12O0jN/KgASYGFABqXwAZtoBV6Sl3hIAAAAASUVORK5CYII=',
        ]);

        $upload->assertStatus(200);
        $upload->assertJson([
            'type'        => 'drawio',
            'uploaded_to' => $page->id,
            'created_by'  => $editor->id,
            'updated_by'  => $editor->id,
        ]);

        $image = Image::where('type', '=', 'drawio')->first();
        $this->assertTrue(file_exists(public_path($image->path)), 'Uploaded image not found at path: ' . public_path($image->path));

        $testImageData = $this->files->pngImageData();
        $uploadedImageData = file_get_contents(public_path($image->path));
        $this->assertTrue($testImageData === $uploadedImageData, 'Uploaded image file data does not match our test image as expected');
    }

    public function test_drawio_url_can_be_configured()
    {
        config()->set('services.drawio', 'http://cats.com?dog=tree');
        $page = $this->entities->page();
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get($page->getUrl('/edit'));
        $resp->assertSee('drawio-url="http://cats.com?dog=tree"', false);
    }

    public function test_drawio_url_can_be_disabled()
    {
        config()->set('services.drawio', true);
        $page = $this->entities->page();
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get($page->getUrl('/edit'));
        $resp->assertSee('drawio-url="https://embed.diagrams.net/?embed=1&amp;proto=json&amp;spin=1&amp;configure=1"', false);

        config()->set('services.drawio', false);
        $resp = $this->actingAs($editor)->get($page->getUrl('/edit'));
        $resp->assertDontSee('drawio-url', false);
    }
}
