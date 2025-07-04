<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Activity\TagRepo;
use BookStack\Entities\Models\Record;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Facades\Activity;
use BookStack\Sorting\SortRule;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Http\UploadedFile;

class RecordRepo
{
    public function __construct(
        protected BaseRepo $baseRepo,
        protected TagRepo $tagRepo,
        protected ImageRepo $imageRepo,
        protected TrashCan $trashCan,
    ) {
    }

    /**
     * Create a new record in the system.
     */
    public function create(array $input): Record
    {
        $record = new Record();
        $this->baseRepo->create($record, $input);
        $this->baseRepo->updateCoverImage($record, $input['image'] ?? null);
        $this->baseRepo->updateRecordDefaultTemplate($record, intval($input['default_template_id'] ?? null));
        Activity::add(ActivityType::RECORD_CREATE, $record);
        $defaultRecordSortSetting = intval(setting('sorting-record-default', '0'));
        if ($defaultRecordSortSetting && SortRule::query()->find($defaultRecordSortSetting)) {
            $record->sort_rule_id = $defaultRecordSortSetting;
            $record->save();
        }

        return $record;
    }

    /**
     * Update the given record.
     */
    public function update(Record $record, array $input): Record
    {
        $this->baseRepo->update($record, $input);

        if (array_key_exists('default_template_id', $input)) {
            $this->baseRepo->updateRecordDefaultTemplate($record, intval($input['default_template_id']));
        }

        if (array_key_exists('image', $input)) {
            $this->baseRepo->updateCoverImage($record, $input['image'], $input['image'] === null);
        }

        Activity::add(ActivityType::RECORD_UPDATE, $record);

        return $record;
    }

    /**
     * Update the given record's cover image, or clear it.
     *
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Record $record, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        $this->baseRepo->updateCoverImage($record, $coverImage, $removeImage);
    }

    /**
     * Remove a record from the system.
     *
     * @throws Exception
     */
    public function destroy(Record $record)
    {
        $this->trashCan->softDestroyRecord($record);
        Activity::add(ActivityType::RECORD_DELETE, $record);
    }
}
