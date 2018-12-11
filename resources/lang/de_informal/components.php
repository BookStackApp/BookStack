<?php
$de_formal = (include resource_path() . '/lang/de/' . basename(__FILE__));

$de_informal = [
    /**
     * Image Manager
     */
    'image_delete_confirm' => 'Bitte klicke erneut auf löschen, wenn Du dieses Bild wirklich entfernen möchtest.',
    'image_dropzone' => 'Ziehe Bilder hierher oder klicke hier, um ein Bild auszuwählen',
    /**
     * Code editor
     */
];

return array_replace($de_formal, $de_informal);