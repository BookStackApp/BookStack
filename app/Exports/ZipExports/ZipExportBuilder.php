<?php

namespace BookStack\Exports\ZipExports;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\ZipExportException;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use ZipArchive;

class ZipExportBuilder
{
    protected array $data = [];

    public function __construct(
        protected ZipExportFiles $files,
        protected ZipExportReferences $references,
    ) {
    }

    /**
     * @throws ZipExportException
     */
    public function buildForPage(Page $page): string
    {
        $exportPage = ZipExportPage::fromModel($page, $this->files);
        $this->data['page'] = $exportPage;

        $this->references->addPage($exportPage);

        return $this->build();
    }

    /**
     * @throws ZipExportException
     */
    public function buildForChapter(Chapter $chapter): string
    {
        $exportChapter = ZipExportChapter::fromModel($chapter, $this->files);
        $this->data['chapter'] = $exportChapter;

        $this->references->addChapter($exportChapter);

        return $this->build();
    }

    /**
     * @throws ZipExportException
     */
    public function buildForBook(Book $book): string
    {
        $exportBook = ZipExportBook::fromModel($book, $this->files);
        $this->data['book'] = $exportBook;

        $this->references->addBook($exportBook);

        return $this->build();
    }

    /**
     * @throws ZipExportException
     */
    protected function build(): string
    {
        $this->references->buildReferences($this->files);

        $this->data['exported_at'] = date(DATE_ATOM);
        $this->data['instance'] = [
            'version'       => trim(file_get_contents(base_path('version'))),
            'id_ciphertext' => encrypt('bookstack'),
        ];

        $zipFile = tempnam(sys_get_temp_dir(), 'bszip-');
        $zip = new ZipArchive();
        $opened = $zip->open($zipFile, ZipArchive::CREATE);
        if ($opened !== true) {
            throw new ZipExportException('Failed to create zip file for export.');
        }

        $zip->addFromString('data.json', json_encode($this->data));
        $zip->addEmptyDir('files');

        $toRemove = [];
        $this->files->extractEach(function ($filePath, $fileRef) use ($zip, &$toRemove) {
            $zip->addFile($filePath, "files/$fileRef");
            $toRemove[] = $filePath;
        });

        $zip->close();

        foreach ($toRemove as $file) {
            unlink($file);
        }

        return $zipFile;
    }
}
