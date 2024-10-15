<?php

namespace BookStack\Exports;

use BookStack\Entities\Models\Page;
use BookStack\Exceptions\ZipExportException;
use ZipArchive;

class ZipExportBuilder
{
    protected array $data = [];

    /**
     * @throws ZipExportException
     */
    public function buildForPage(Page $page): string
    {
        $this->data['page'] = [
            'id' => $page->id,
        ];

        return $this->build();
    }

    /**
     * @throws ZipExportException
     */
    protected function build(): string
    {
        $this->data['exported_at'] = date(DATE_ATOM);
        $this->data['instance'] = [
            'version' => trim(file_get_contents(base_path('version'))),
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

        return $zipFile;
    }
}
