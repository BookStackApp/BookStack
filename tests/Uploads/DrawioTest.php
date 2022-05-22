<?php

namespace Tests\Uploads;

use BookStack\Entities\Models\Page;
use BookStack\Uploads\Image;
use Tests\TestCase;

class DrawioTest extends TestCase
{
    use UsesImages;

    public function test_get_image_as_base64_with_png_content()
    {
        $page = Page::first();
        $this->asAdmin();
        $imageName = 'first-image.png';

        $this->uploadImage($imageName, $page->id);
        $image = Image::first();
        $image->type = 'drawio';
        $image->save();

        $imageGet = $this->getJson("/images/drawio/base64/{$image->id}");
        $imageGet->assertJson([
            'content' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAIAAAACDbGyAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4gEcDCo5iYNs+gAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAFElEQVQI12O0jN/KgASYGFABqXwAZtoBV6Sl3hIAAAAASUVORK5CYII=',
        ]);
    }

    public function test_get_image_as_base64_with_svg_content()
    {
        $page = Page::first();
        $this->asAdmin();

        $this->uploadImage('my-drawing.svg', $page->id, 'image/svg+xml', 'diagram.svg');
        $image = Image::first();
        $image->type = 'drawio';
        $image->save();

        $imageGet = $this->getJson("/images/drawio/base64/{$image->id}");
        $imageGet->assertJson([
            'content' => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($this->getTestImageFilePath('diagram.svg'))),
        ]);
    }

    public function test_drawing_base64_upload_with_png()
    {
        $page = Page::first();
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $upload = $this->postJson('images/drawio', [
            'uploaded_to' => $page->id,
            'image'       => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAIAAAACDbGyAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4gEcDCo5iYNs+gAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAFElEQVQI12O0jN/KgASYGFABqXwAZtoBV6Sl3hIAAAAASUVORK5CYII=',
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

        $testImageData = file_get_contents($this->getTestImageFilePath());
        $uploadedImageData = file_get_contents(public_path($image->path));
        $this->assertTrue($testImageData === $uploadedImageData, 'Uploaded image file data does not match our test image as expected');
    }

    public function test_drawing_base64_upload_with_svg()
    {
        $page = Page::first();
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $upload = $this->postJson('images/drawio', [
            'uploaded_to' => $page->id,
            'image'       => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($this->getTestImageFilePath('diagram.svg'))),
        ]);

        $upload->assertStatus(200);
        $upload->assertJson([
            'type'        => 'drawio',
            'uploaded_to' => $page->id,
            'created_by'  => $editor->id,
            'updated_by'  => $editor->id,
        ]);

        $image = Image::where('type', '=', 'drawio')->first();
        $this->assertStringEndsWith('.svg', $image->path);
        $this->assertTrue(file_exists(public_path($image->path)), 'Uploaded image not found at path: ' . public_path($image->path));

        $testImageData = file_get_contents($this->getTestImageFilePath('diagram.svg'));
        $uploadedImageData = file_get_contents(public_path($image->path));
        $this->assertTrue($testImageData === $uploadedImageData, 'Uploaded image file data does not match our test image as expected');
    }

    public function test_drawio_url_can_be_configured()
    {
        config()->set('services.drawio', 'http://cats.com?dog=tree');
        $page = Page::first();
        $editor = $this->getEditor();

        $resp = $this->actingAs($editor)->get($page->getUrl('/edit'));
        $resp->assertSee('drawio-url="http://cats.com?dog=tree"', false);
    }

    public function test_drawio_url_can_be_disabled()
    {
        config()->set('services.drawio', true);
        $page = Page::first();
        $editor = $this->getEditor();

        $resp = $this->actingAs($editor)->get($page->getUrl('/edit'));
        $resp->assertSee('drawio-url="https://embed.diagrams.net/?embed=1&amp;proto=json&amp;spin=1&amp;configure=1"', false);

        config()->set('services.drawio', false);
        $resp = $this->actingAs($editor)->get($page->getUrl('/edit'));
        $resp->assertDontSee('drawio-url', false);
    }
}
