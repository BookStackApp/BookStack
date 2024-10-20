<?php

namespace BookStack\Exports;

use BookStack\Entities\Models\Page;
use BookStack\Exceptions\ZipExportException;
use BookStack\Exports\ZipExportModels\ZipExportPage;
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
    protected function build(): string
    {
        $this->references->buildReferences();

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
