<?php

declare(strict_types=1);

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\EntityPageData;
use BookStack\Entities\Tools\PageEditorType;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class UpdatePageEditorCommand extends Command
{
    protected $signature = 'bookstack:update-page-editor';

    protected $description = 'Update the default page editor in use';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get and report the existing editor usage
        $editorOptions = PageEditorType::cases();
        $pageCountByEditor = $this->getPageCountByEditor();
        $activeDefault = PageEditorType::getSystemDefault();
        $this->reportExistingUse($activeDefault, $editorOptions, $pageCountByEditor);

        // Ask the user which editor they'd like to use
        $editorLabels = array_map(fn(PageEditorType $type) => $type->getLabel(), $editorOptions);
        $newEditorLabel = $this->choice(
            "What editor would you like to set as the default?",
            ['Exit with no change', ...$editorLabels],
            0,
            3,
            false,
        );

        if ($newEditorLabel === 'Exit with no change') {
            return self::SUCCESS;
        }

        // Update editor choice
        $newEditorChoice = $editorOptions[array_search($newEditorLabel, $editorLabels, true)];
        setting()->put('app-editor', $newEditorChoice->value);
        $this->line("Default page editor option updated!");

        // If selecting a HTML based editor, offer to migrate pages using the other HTML editor
        if ($newEditorChoice->isHtmlBased()) {
            $this->offerMigration($editorOptions, $newEditorChoice, $pageCountByEditor);
        }

        return self::SUCCESS;
    }

    /**
     * @return Collection<string, int>
     */
    protected function getPageCountByEditor(): Collection
    {
        return EntityPageData::query()
            ->toBase()
            ->select('editor')
            ->selectExpression('count(page_id)', 'count')
            ->groupBy('editor')
            ->pluck('count', 'editor');
    }

    /**
     * @param PageEditorType[] $editorOptions
     * @param Collection<string, int> $pageCountByEditor
     */
    public function reportExistingUse(PageEditorType $activeDefault, array $editorOptions, Collection $pageCountByEditor): void
    {
        $this->line("Current Default: {$activeDefault->getLabel()}");
        $this->line("Editor Use:");
        foreach ($editorOptions as $editor) {
            $count = $pageCountByEditor->get($editor->value, 0);
            $lineLabel = str_pad("- {$editor->getLabel()}:", 19);
            $this->line("{$lineLabel} {$count} pages");
        }
    }

    /**
     * @param PageEditorType[] $editorOptions
     * @param Collection<string, int> $pageCountByEditor
     */
    public function offerMigration(array $editorOptions, PageEditorType $newEditorChoice, Collection $pageCountByEditor): void
    {
        $toMigrate = array_find(
            $editorOptions,
            fn(PageEditorType $type) => $type->isHtmlBased() && $type !== $newEditorChoice,
        );

        $pageCount = $pageCountByEditor->get($toMigrate->value, 0);
        if ($pageCount === 0) {
            return;
        }

        $this->line("");
        $this->line("There are {$pageCount} pages still using the {$toMigrate->getLabel()} editor.");
        $this->line("These pages could be updated to use the {$newEditorChoice->getLabel()} editor instead.");
        $this->warn(
            "WARNING: No page content will be altered right now, but changing the editor for a page can lead to formatting/content loss when a page is opened for editing."
        );

        $update = $this->confirm("Update these pages to use your new editor choice?", false);
        if ($update) {
            $updates = EntityPageData::query()
                ->where('editor', '=', $toMigrate->value)
                ->update(['editor' => $newEditorChoice->value]);
            $this->line("{$updates} pages have been updated to use the {$newEditorChoice->getLabel()} editor!");
        }
    }
}
