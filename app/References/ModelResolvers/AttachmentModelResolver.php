<?php

namespace BookStack\References\ModelResolvers;

use BookStack\Uploads\Attachment;

class AttachmentModelResolver implements CrossLinkModelResolver
{
    public function resolve(string $link): ?Attachment
    {
        $pattern = '/^' . preg_quote(url('/attachments'), '/') . '\/(\d+)/';
        $matches = [];
        $match = preg_match($pattern, $link, $matches);
        if (!$match) {
            return null;
        }

        $id = intval($matches[1]);

        return Attachment::query()->find($id);
    }
}
