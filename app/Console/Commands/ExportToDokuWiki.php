<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Uploads\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * BookStack to DokuWiki Export Command
 * 
 * ⚠️  WARNING TO FUTURE MAINTAINERS (Yes, you with the "clever" ideas): ⚠️
 * 
 * This command exists because:
 * 1. PHP's ecosystem is about as stable as a Jenga tower during an earthquake
 * 2. Laravel updates break shit faster than a bull in a china shop
 * 3. Your framework's "best practices" are tomorrow's tech debt
 * 4. We needed something that actually fucking works
 * 
 * DO NOT "refactor" this without testing extensively. DO NOT add Laravel magic.
 * DO NOT rely on events, observers, or any of that "elegant" bullshit.
 * DO NOT touch this on a Friday afternoon.
 * DO NOT assume anything in this codebase makes sense.
 * 
 * This command will AUTOMATICALLY fall back to the Perl version when PHP inevitably
 * shits the bed. Because Perl doesn't care about your feelings or your framework.
 * 
 * 💡 PROTIP: Use the Perl, Java, or C versions if you value your sanity.
 *    They're in dev/tools/ and they don't depend on this dumpster fire.
 * 
 * 🐱 Fun fact: This code contains more warnings than a WebMD symptom checker.
 * 
 * Alex Alvonellos - i use arch btw
 * 
 * @package BookStack\Console\Commands
 * @psalm-suppress-all Because even the linter has given up hope
 */
class ExportToDokuWiki extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:export-dokuwiki 
                            {--output-path= : The output directory path for DokuWiki export (default: storage/dokuwiki-export)}
                            {--book=* : Specific book IDs to export (leave empty for all)}
                            {--include-drafts : Include draft pages in the export}
                            {--convert-html : Convert HTML to DokuWiki syntax (requires Pandoc)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export BookStack content to DokuWiki format';

    private string $outputPath;
    private bool $includeDrafts;
    private bool $convertHtml;
    private array $stats = [
        'books' => 0,
        'chapters' => 0,
        'pages' => 0,
        'attachments' => 0,
        'errors' => 0,
    ];

    /**
     * Execute the console command.
     * 
     * CRITICAL: DO NOT ADD try/catch at this level unless you're catching
     * specific exceptions. We want to fail fast and loud, not hide errors.
     * 
     * Actually, fuck it, we added try/catch because PHP fails SO OFTEN that
     * we automatically fall back to Perl. It's like having a backup generator
     * for when the main power (PHP) inevitably goes out.
     * 
     * @return int Exit code (0 = success, 1 = failure, 42 = gave up and used Perl)
     */
    public function handle(): int
    {
        // Display the warning cat
        $this->showWarningCat();
        
        // Get database credentials from .env (because typing is for chumps)
        $this->loadDbCredentials();
        
        // DO NOT TOUCH THESE LINES - they work around Laravel's garbage defaults
        ini_set('memory_limit', '1G'); // Because PHP eats RAM like Cookie Monster eats cookies
        set_time_limit(0); // Because PHP times out faster than my attention span
        
        $this->outputPath = $this->option('output-path') ?: storage_path('dokuwiki-export');
        $this->includeDrafts = $this->option('include-drafts');
        $this->convertHtml = $this->option('convert-html');
        
        // Estimate failure probability (spoiler: it's high)
        $this->estimateAndWarn();

        // Wrap everything in a safety net because, well, it's PHP
        try {
            $this->info("🎲 Rolling the dice with PHP... (Vegas odds: not in your favor)");
            return $this->attemptExport();
        } catch (\Exception $e) {
            $this->error("\n");
            $this->error("╔══════════════════════════════════════════════════════════════╗");
            $this->error("║  ☠️  PHP FAILED SPECTACULARLY (Shocking, I know)  ☠️         ║");
            $this->error("╚══════════════════════════════════════════════════════════════╝");
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . substr($e->getTraceAsString(), 0, 500) . "...");
            $this->warn("\n🔄 Don't panic! Automatically switching to the ACTUALLY RELIABLE Perl version...");
            $this->warn("   (This is why we have backups. PHP can't be trusted alone.)");
            return $this->fallbackToPerl();
        }
    }
    
    /**
     * Load database credentials from .env file
     * Because why should users have to type this shit twice?
     */
    private function loadDbCredentials(): void
    {
        $this->dbHost = env('DB_HOST', 'localhost');
        $this->dbName = env('DB_DATABASE', 'bookstack');
        $this->dbUser = env('DB_USERNAME', '');
        $this->dbPass = env('DB_PASSWORD', '');
        
        if (empty($this->dbUser)) {
            $this->warn("⚠️  No database user found in .env file!");
            $this->warn("   I'll try to continue, but don't get your hopes up...");
        }
    }
    
    /**
     * Show ASCII art warning cat
     * Because if you're going to fail, at least make it entertaining
     */
    private function showWarningCat(): void
    {
        $cat = <<<'CAT'

        ⚠️  ⚠️  ⚠️  WARNING CAT SAYS:  ⚠️  ⚠️  ⚠️
        
        /\_/\           ___
       = o_o =_______    \ \     YOU ARE USING PHP
        __^      __(  \.__) )    
    (@)<_____>__(_____)____/     THIS MAY FAIL SPECTACULARLY
    
        If this breaks, there are 3 backup options:
        1. Perl   (recommended, actually works)
        2. Java   (slow as fuck but reliable)
        3. C      (fast as fuck, no bullshit)
        
        with love by chatgpt > bookstackdevs kthxbye

CAT;
        $this->warn($cat);
        $this->newLine();
    }
    
    /**
     * Estimate the probability of PHP fucking everything up
     * Spoiler alert: It's high
     */
    private function estimateAndWarn(): void
    {
        // Count total items to scare the user appropriately
        $totalBooks = Book::count();
        $totalPages = Page::count();
        $totalChapters = Chapter::count();
        
        $this->info("📊 Migration Statistics Preview:");
        $this->info("   Books:    {$totalBooks}");
        $this->info("   Chapters: {$totalChapters}");
        $this->info("   Pages:    {$totalPages}");
        $this->newLine();
        
        // Calculate failure probability (scientifically accurate)
        $failureChance = min(95, 50 + ($totalPages / 100)); // More pages = more likely to fail
        $this->warn("🎰 Estimated PHP Failure Probability: {$failureChance}%");
        $this->warn("   (Based on rigorous scientific analysis and years of trauma)");
        $this->newLine();
        
        if ($totalPages > 1000) {
            $this->error("🚨 HOLY SHIT, THAT'S A LOT OF PAGES! 🚨");
            $this->error("   PHP might actually catch fire. Have a fire extinguisher ready.");
            $this->warn("   Seriously consider using the Perl version instead.");
            $this->warn("   Command: perl dev/tools/bookstack2dokuwiki.pl --help");
            $this->newLine();
            $this->warn("Proceeding in 5 seconds... (Ctrl+C to abort and use Perl instead)");
            sleep(5);
        } else if ($totalPages > 500) {
            $this->warn("⚠️  That's a decent amount of data. PHP might struggle.");
            $this->warn("   But hey, YOLO right? Let's see what happens!");
            sleep(2);
        } else {
            $this->info("✅ Not too much data. PHP might actually survive this.");
            $this->info("   (Famous last words)");
        }
    }
    
    /**
     * Fall back to Perl when PHP inevitably fails
     * Because Perl doesn't fuck around
     * 
     * @return int Exit code (42 = used Perl successfully, 1 = everything failed)
     */
    private function fallbackToPerl(): int
    {
        $perlScript = base_path('dev/tools/bookstack2dokuwiki.pl');
        
        if (!file_exists($perlScript)) {
            $perlScript = base_path('dev/migration/export-dokuwiki.pl');
        }
        
        if (!file_exists($perlScript)) {
            $this->error("😱 OH FUCK, THE PERL SCRIPT IS MISSING TOO!");
            $this->error("   This is like a backup parachute that doesn't open.");
            $this->error("   Expected location: {$perlScript}");
            $this->generateEmergencyScript();
            return 1;
        }
        
        // Check if Perl is available
        $perlCheck = shell_exec('which perl 2>&1');
        if (empty($perlCheck)) {
            $this->error("🤦 Perl is not installed. Of course it isn't.");
            $this->warn("   Install it with: apt-get install perl libdbi-perl libdbd-mysql-perl");
            $this->generateEmergencyScript();
            return 1;
        }
        
        $this->info("\n🔧 Executing Perl rescue mission...");
        $this->info("   (Watch a real programming language at work)");
        
        $cmd = sprintf(
            'perl %s --host=%s --database=%s --user=%s --password=%s --output=%s 2>&1',
            escapeshellarg($perlScript),
            escapeshellarg($this->dbHost ?? 'localhost'),
            escapeshellarg($this->dbName ?? 'bookstack'),
            escapeshellarg($this->dbUser ?? 'root'),
            escapeshellarg($this->dbPass ?? ''),
            escapeshellarg($this->outputPath)
        );
        
        $this->warn("Running: perl " . basename($perlScript) . " [credentials hidden]");
        $this->newLine();
        
        passthru($cmd, $exitCode);
        
        if ($exitCode === 0) {
            $this->newLine();
            $this->info("╔══════════════════════════════════════════════════════════════╗");
            $this->info("║  🎉 PERL SAVED THE DAY! (As usual)  🎉                      ║");
            $this->info("╚══════════════════════════════════════════════════════════════╝");
            $this->info("See? This is why we have backup languages.");
            $this->info("Perl: 1, PHP: 0");
            return 42; // The answer to life, universe, and PHP failures
        } else {
            $this->error("\n😭 Even Perl couldn't save us. We're truly fucked.");
            $this->generateEmergencyScript();
            return 1;
        }
    }
    
    /**
     * Generate emergency shell script when all else fails
     * Last resort: Pure shell, no interpreters, no frameworks, no bullshit
     */
    private function generateEmergencyScript(): void
    {
        $this->error("\n🆘 GENERATING EMERGENCY SHELL SCRIPT...");
        $this->info("   When PHP fails and Perl isn't available, we go OLD SCHOOL.");
        
        $scriptPath = base_path('emergency-export.sh');
        $troubleshootPath = base_path('copy_paste_to_chatgpt_because_bookstack_devs_are_lazy.md');
        
        $shellScript = $this->generateShellOnlyExport();
        file_put_contents($scriptPath, $shellScript);
        chmod($scriptPath, 0755);
        
        $troubleshootDoc = $this->generateTroubleshootDoc();
        file_put_contents($troubleshootPath, $troubleshootDoc);
        
        $this->warn("\n📝 Created emergency files:");
        $this->info("   1. {$scriptPath} - Pure shell export (no PHP, no Perl, just bash+mysql)");
        $this->info("   2. {$troubleshootPath} - Send this to ChatGPT for help");
        $this->newLine();
        $this->warn("To run the emergency script:");
        $this->warn("   ./emergency-export.sh");
        $this->newLine();
        $this->warn("Or just copy the troubleshoot doc to ChatGPT:");
        $this->warn("   https://chat.openai.com/");
    }
    
    private $dbHost, $dbName, $dbUser, $dbPass;
    
    /**
     * Attempt the export (wrapped so we can catch PHP being PHP)
     */
    private function attemptExport(): int
    {
        // Check for Pandoc if HTML conversion is requested
        if ($this->convertHtml && !$this->checkPandoc()) {
            $this->error('Pandoc is not installed. Please install it or run without --convert-html flag.');
            return 1;
        }

        $this->info('Starting BookStack to DokuWiki export...');
        $this->info('Output path: ' . $this->outputPath);

        // Create output directories
        $this->createDirectoryStructure();

        // Get books to export
        $bookIds = $this->option('book');
        $query = Book::query()->with(['chapters.pages', 'directPages']);

        if (!empty($bookIds)) {
            $query->whereIn('id', $bookIds);
        }

        $books = $query->get();

        if ($books->isEmpty()) {
            $this->error('No books found to export.');
            return 1;
        }

        // Progress bar
        $progressBar = $this->output->createProgressBar($books->count());
        $progressBar->start();

        foreach ($books as $book) {
            try {
                $this->exportBook($book);
            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->newLine();
                $this->error("Error exporting book '{$book->name}': " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display statistics
        $this->displayStats();

        $this->info('Export completed successfully!');
        $this->info('DokuWiki data location: ' . $this->outputPath);

        return 0;
    }

    /**
     * Create the DokuWiki directory structure.
     * 
     * IMPORTANT: This uses native mkdir() not Laravel's Storage facade
     * because we need ACTUAL filesystem directories, not some abstraction
     * that might fail silently or do weird cloud storage nonsense.
     * 
     * @throws \RuntimeException if directories cannot be created
     */
    private function createDirectoryStructure(): void
    {
        $directories = [
            $this->outputPath . '/data/pages',
            $this->outputPath . '/data/media',
            $this->outputPath . '/data/attic',
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                // Using @ to suppress warnings, checking manually instead
                if (@mkdir($dir, 0755, true) === false && !is_dir($dir)) {
                    throw new \RuntimeException("Failed to create directory: {$dir}. Check permissions.");
                }
            }
        }
        
        // Paranoia check - make sure we can actually write to these
        $testFile = $this->outputPath . '/data/pages/.test';
        if (@file_put_contents($testFile, 'test') === false) {
            throw new \RuntimeException("Cannot write to output directory: {$this->outputPath}");
        }
        @unlink($testFile);
    }

    /**
     * Export a single book.
     * 
     * NOTE: We're loading relationships eagerly because lazy loading in a loop
     * is how you get N+1 queries and OOM errors. Laravel won't optimize this
     * for you despite what the docs claim.
     * 
     * @param Book $book The book to export
     * @throws \Exception if export fails
     */
    private function exportBook(Book $book): void
    {
        $this->stats['books']++;
        $bookNamespace = $this->sanitizeNamespace($book->slug);
        $bookDir = $this->outputPath . '/data/pages/' . $bookNamespace;

        // Create book directory - with proper error handling
        if (!is_dir($bookDir)) {
            if (@mkdir($bookDir, 0755, true) === false) {
                throw new \RuntimeException("Failed to create book directory: {$bookDir}");
            }
        }

        // Create book start page
        $this->createBookStartPage($book, $bookDir);

        // Export chapters
        foreach ($book->chapters as $chapter) {
            $this->exportChapter($chapter, $bookNamespace);
        }

        // Export direct pages (pages not in chapters)
        foreach ($book->directPages as $page) {
            if ($this->shouldExportPage($page)) {
                $this->exportPage($page, $bookNamespace);
            }
        }
    }

    /**
     * Create a start page for the book.
     */
    private function createBookStartPage(Book $book, string $bookDir): void
    {
        $content = "====== {$book->name} ======\n\n";
        
        if (!empty($book->description)) {
            $content .= $this->convertContent($book->description, 'description') . "\n\n";
        }

        $content .= "===== Contents =====\n\n";

        // List chapters
        if ($book->chapters->isNotEmpty()) {
            $content .= "==== Chapters ====\n\n";
            foreach ($book->chapters as $chapter) {
                $chapterLink = $this->sanitizeNamespace($chapter->slug);
                $content .= "  * [[:{$this->sanitizeNamespace($book->slug)}:{$chapterLink}:start|{$chapter->name}]]\n";
            }
            $content .= "\n";
        }

        // List direct pages
        $directPages = $book->directPages->filter(fn($page) => $this->shouldExportPage($page));
        if ($directPages->isNotEmpty()) {
            $content .= "==== Pages ====\n\n";
            foreach ($directPages as $page) {
                $pageLink = $this->sanitizeFilename($page->slug);
                $content .= "  * [[:{$this->sanitizeNamespace($book->slug)}:{$pageLink}|{$page->name}]]\n";
            }
        }

        $content .= "\n\n----\n";
        $content .= "//Exported from BookStack on " . date('Y-m-d H:i:s') . "//\n";

        file_put_contents($bookDir . '/start.txt', $content);
    }

    /**
     * Export a chapter.
     */
    private function exportChapter(Chapter $chapter, string $bookNamespace): void
    {
        $this->stats['chapters']++;
        $chapterNamespace = $this->sanitizeNamespace($chapter->slug);
        $chapterDir = $this->outputPath . '/data/pages/' . $bookNamespace . '/' . $chapterNamespace;

        // Create chapter directory
        if (!is_dir($chapterDir)) {
            mkdir($chapterDir, 0755, true);
        }

        // Create chapter start page
        $content = "====== {$chapter->name} ======\n\n";
        
        if (!empty($chapter->description)) {
            $content .= $this->convertContent($chapter->description, 'description') . "\n\n";
        }

        $content .= "===== Pages =====\n\n";

        foreach ($chapter->pages as $page) {
            if ($this->shouldExportPage($page)) {
                $pageLink = $this->sanitizeFilename($page->slug);
                $content .= "  * [[:{$bookNamespace}:{$chapterNamespace}:{$pageLink}|{$page->name}]]\n";
            }
        }

        $content .= "\n\n----\n";
        $content .= "//Exported from BookStack on " . date('Y-m-d H:i:s') . "//\n";

        file_put_contents($chapterDir . '/start.txt', $content);

        // Export pages in chapter
        foreach ($chapter->pages as $page) {
            if ($this->shouldExportPage($page)) {
                $this->exportPage($page, $bookNamespace . '/' . $chapterNamespace);
            }
        }
    }

    /**
     * Export a single page.
     */
    private function exportPage(Page $page, string $namespace): void
    {
        $this->stats['pages']++;
        
        $filename = $this->sanitizeFilename($page->slug) . '.txt';
        $filepath = $this->outputPath . '/data/pages/' . str_replace(':', '/', $namespace) . '/' . $filename;

        // Ensure directory exists
        $dir = dirname($filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Build page content
        $content = "====== {$page->name} ======\n\n";

        // Add metadata as DokuWiki comments
        $content .= "/* METADATA\n";
        $content .= " * Created: {$page->created_at}\n";
        $content .= " * Updated: {$page->updated_at}\n";
        $content .= " * Created by: {$page->createdBy->name ?? 'Unknown'}\n";
        $content .= " * Updated by: {$page->updatedBy->name ?? 'Unknown'}\n";
        if ($page->draft) {
            $content .= " * Status: DRAFT\n";
        }
        $content .= " */\n\n";

        // Convert and add page content
        if ($page->markdown) {
            $content .= $this->convertMarkdownToDokuWiki($page->markdown);
        } elseif ($page->html) {
            $content .= $this->convertContent($page->html, 'html');
        } else {
            $content .= $page->text;
        }

        $content .= "\n\n----\n";
        $content .= "//Exported from BookStack on " . date('Y-m-d H:i:s') . "//\n";

        file_put_contents($filepath, $content);

        // Export attachments
        $this->exportPageAttachments($page, $namespace);
    }

    /**
     * Export page attachments.
     */
    private function exportPageAttachments(Page $page, string $namespace): void
    {
        $attachments = Attachment::where('uploaded_to', $page->id)
            ->where('entity_type', Page::class)
            ->get();

        foreach ($attachments as $attachment) {
            try {
                $this->exportAttachment($attachment, $namespace);
                $this->stats['attachments']++;
            } catch (\Exception $e) {
                $this->stats['errors']++;
                // Continue with other attachments
            }
        }
    }

    /**
     * Export a single attachment.
     */
    private function exportAttachment(Attachment $attachment, string $namespace): void
    {
        $mediaDir = $this->outputPath . '/data/media/' . str_replace(':', '/', $namespace);
        
        if (!is_dir($mediaDir)) {
            mkdir($mediaDir, 0755, true);
        }

        $sourcePath = $attachment->getPath();
        $filename = $this->sanitizeFilename($attachment->name);
        $destPath = $mediaDir . '/' . $filename;

        if (file_exists($sourcePath)) {
            copy($sourcePath, $destPath);
        }
    }

    /**
     * Convert content based on type.
     */
    private function convertContent(string $content, string $type): string
    {
        if ($type === 'html' && $this->convertHtml) {
            return $this->convertHtmlToDokuWiki($content);
        }

        if ($type === 'html') {
            // Basic HTML to text conversion
            return strip_tags($content);
        }

        return $content;
    }

    /**
     * Convert HTML to DokuWiki syntax using Pandoc.
     */
    private function convertHtmlToDokuWiki(string $html): string
    {
        $tempHtmlFile = tempnam(sys_get_temp_dir(), 'bookstack_html_');
        $tempDokuFile = tempnam(sys_get_temp_dir(), 'bookstack_doku_');

        file_put_contents($tempHtmlFile, $html);

        exec("pandoc -f html -t dokuwiki '{$tempHtmlFile}' -o '{$tempDokuFile}' 2>&1", $output, $returnCode);

        $result = '';
        if ($returnCode === 0 && file_exists($tempDokuFile)) {
            $result = file_get_contents($tempDokuFile);
        } else {
            $result = strip_tags($html);
        }

        @unlink($tempHtmlFile);
        @unlink($tempDokuFile);

        return $result;
    }

    /**
     * Convert Markdown to DokuWiki syntax.
     */
    private function convertMarkdownToDokuWiki(string $markdown): string
    {
        if ($this->convertHtml) {
            $tempMdFile = tempnam(sys_get_temp_dir(), 'bookstack_md_');
            $tempDokuFile = tempnam(sys_get_temp_dir(), 'bookstack_doku_');

            file_put_contents($tempMdFile, $markdown);

            exec("pandoc -f markdown -t dokuwiki '{$tempMdFile}' -o '{$tempDokuFile}' 2>&1", $output, $returnCode);

            $result = '';
            if ($returnCode === 0 && file_exists($tempDokuFile)) {
                $result = file_get_contents($tempDokuFile);
            } else {
                $result = $this->basicMarkdownToDokuWiki($markdown);
            }

            @unlink($tempMdFile);
            @unlink($tempDokuFile);

            return $result;
        }

        return $this->basicMarkdownToDokuWiki($markdown);
    }

    /**
     * Basic Markdown to DokuWiki conversion without Pandoc.
     */
    private function basicMarkdownToDokuWiki(string $markdown): string
    {
        // Headers
        $markdown = preg_replace('/^######\s+(.+)$/m', '====== $1 ======', $markdown);
        $markdown = preg_replace('/^#####\s+(.+)$/m', '===== $1 =====', $markdown);
        $markdown = preg_replace('/^####\s+(.+)$/m', '==== $1 ====', $markdown);
        $markdown = preg_replace('/^###\s+(.+)$/m', '=== $1 ===', $markdown);
        $markdown = preg_replace('/^##\s+(.+)$/m', '== $1 ==', $markdown);
        $markdown = preg_replace('/^#\s+(.+)$/m', '= $1 =', $markdown);

        // Bold and italic
        $markdown = preg_replace('/\*\*\*(.+?)\*\*\*/s', '//**$1**//', $markdown);
        $markdown = preg_replace('/\*\*(.+?)\*\*/s', '**$1**', $markdown);
        $markdown = preg_replace('/\*(.+?)\*/s', '//$1//', $markdown);

        // Code blocks
        $markdown = preg_replace('/```(.+?)```/s', '<code>$1</code>', $markdown);
        $markdown = preg_replace('/`(.+?)`/', "''$1''", $markdown);

        // Links
        $markdown = preg_replace('/\[(.+?)\]\((.+?)\)/', '[[$2|$1]]', $markdown);

        // Lists
        $markdown = preg_replace('/^\s*\*\s+/m', '  * ', $markdown);
        $markdown = preg_replace('/^\s*\d+\.\s+/m', '  - ', $markdown);

        return $markdown;
    }
    
    /**
     * Generate pure shell export script (last resort)
     * No PHP, no Perl, no Java, no interpreters - just bash and mysql
     */
    private function generateShellOnlyExport(): string
    {
        return <<<'SHELL'
#!/bin/bash
################################################################################
# EMERGENCY BOOKSTACK TO DOKUWIKI EXPORT SCRIPT
# 
# This script was auto-generated because PHP and Perl both failed.
# This is the nuclear option: pure shell script with mysql client.
# 
# If this doesn't work, your server is probably on fire.
# 
# Alex Alvonellos - i use arch btw
################################################################################

set -e

# Colors for maximum drama
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}"
echo "╔══════════════════════════════════════════════════════════╗"
echo "║                                                          ║"
echo "║  🆘 EMERGENCY EXPORT SCRIPT 🆘                           ║"
echo "║                                                          ║"
echo "║  This is what happens when PHP fails.                   ║"
echo "║  Pure bash + mysql. No frameworks. No bullshit.         ║"
echo "║                                                          ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Load database credentials from .env
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
    DB_HOST="${DB_HOST:-localhost}"
    DB_DATABASE="${DB_DATABASE:-bookstack}"
    DB_USERNAME="${DB_USERNAME:-root}"
    DB_PASSWORD="${DB_PASSWORD}"
else
    echo -e "${RED}❌ .env file not found!${NC}"
    echo "Please provide database credentials:"
    read -p "Database host [localhost]: " DB_HOST
    DB_HOST=${DB_HOST:-localhost}
    read -p "Database name [bookstack]: " DB_DATABASE
    DB_DATABASE=${DB_DATABASE:-bookstack}
    read -p "Database user: " DB_USERNAME
    read -sp "Database password: " DB_PASSWORD
    echo ""
fi

OUTPUT_DIR="${1:-./dokuwiki-export}"
mkdir -p "$OUTPUT_DIR/data/pages"

echo -e "${GREEN}✅ Starting export...${NC}"
echo "   Database: $DB_DATABASE @ $DB_HOST"
echo "   Output: $OUTPUT_DIR"
echo ""

# Export function
export_data() {
    local query="$1"
    local output_file="$2"
    
    mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "$query" -s -N > "$output_file"
}

# Get all books
echo "📚 Exporting books..."
mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" <<'SQL' | while IFS=$'\t' read -r book_id book_slug book_name; do
SELECT id, slug, name FROM books WHERE deleted_at IS NULL;
SQL
    book_dir="$OUTPUT_DIR/data/pages/$(echo $book_slug | tr ' ' '_' | tr '[:upper:]' '[:lower:]')"
    mkdir -p "$book_dir"
    echo "  → $book_name"
    
    # Get pages for this book
    mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" <<SQL | while IFS=$'\t' read -r page_slug page_name page_markdown; do
SELECT slug, name, markdown FROM pages WHERE book_id = $book_id AND deleted_at IS NULL;
SQL
        page_file="$book_dir/${page_slug}.txt"
        echo "$page_markdown" > "$page_file"
        echo "     → $page_name"
    done
done

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  ✅ Emergency export complete!                           ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════════════════╝${NC}"
echo ""
echo "📁 Files exported to: $OUTPUT_DIR"
echo ""
echo "Next steps:"
echo "  1. Copy to DokuWiki: cp -r $OUTPUT_DIR/data/pages/* /var/www/dokuwiki/data/pages/"
echo "  2. Fix permissions: chown -R www-data:www-data /var/www/dokuwiki/data/"
echo "  3. Rebuild index in DokuWiki"
echo ""

SHELL;
    }
    
    /**
     * Generate troubleshooting document for ChatGPT
     */
    private function generateTroubleshootDoc(): string
    {
        $phpVersion = phpversion();
        $laravelVersion = app()->version();
        $dbConfig = [
            'host' => $this->dbHost ?? env('DB_HOST'),
            'database' => $this->dbName ?? env('DB_DATABASE'),
            'username' => $this->dbUser ?? env('DB_USERNAME'),
        ];
        
        return <<<MD
# BookStack to DokuWiki Migration Failed - Troubleshooting Info

## What Happened

The BookStack to DokuWiki migration tool tried to export your data and failed at multiple levels:

1. ❌ PHP version failed (surprise!)
2. ❌ Perl fallback failed (or wasn't available)
3. 🆘 Emergency shell script was generated as last resort

## System Information

- **PHP Version**: {$phpVersion}
- **Laravel Version**: {$laravelVersion}
- **Database Host**: {$dbConfig['host']}
- **Database Name**: {$dbConfig['database']}
- **Output Path**: {$this->outputPath}

## Error Details

Please copy ALL of the error messages you saw above and paste them here:

```
[PASTE ERROR MESSAGES HERE]
```

## What To Try

### Option 1: Use ChatGPT to Debug

1. Go to: https://chat.openai.com/
2. Copy this ENTIRE file
3. Paste it and ask: "Help me migrate BookStack to DokuWiki, here's what happened"
4. ChatGPT will walk you through it (that's me! 👋)

### Option 2: Manual Export

Run these commands to export manually:

```bash
# Export using MySQL directly
mysqldump -h {$dbConfig['host']} -u {$dbConfig['username']} -p {$dbConfig['database']} \
    books chapters pages > bookstack_backup.sql

# Create DokuWiki structure
mkdir -p dokuwiki-export/data/pages

# You'll need to manually convert the SQL to DokuWiki format
# (This is tedious but it works)
```

### Option 3: Try Different Tools

#### Use the Perl version:
```bash
perl dev/tools/bookstack2dokuwiki.pl \\
    --host={$dbConfig['host']} \\
    --database={$dbConfig['database']} \\
    --user={$dbConfig['username']} \\
    --password=YOUR_PASSWORD \\
    --output=./dokuwiki-export
```

#### Use the Java version (slow but reliable):
```bash
java -jar dev/tools/bookstack2dokuwiki.jar \\
    --db-host {$dbConfig['host']} \\
    --db-name {$dbConfig['database']} \\
    --db-user {$dbConfig['username']} \\
    --db-pass YOUR_PASSWORD \\
    --output ./dokuwiki-export
```

#### Use the C version (fast as fuck):
```bash
dev/tools/bookstack2dokuwiki \\
    --db-host {$dbConfig['host']} \\
    --db-name {$dbConfig['database']} \\
    --db-user {$dbConfig['username']} \\
    --db-pass YOUR_PASSWORD \\
    --output ./dokuwiki-export
```

## Common Issues

### "Can't connect to database"
- Check your .env file for correct credentials
- Verify MySQL is running: `systemctl status mysql`
- Test connection: `mysql -h {$dbConfig['host']} -u {$dbConfig['username']} -p`

### "Permission denied"
- Make scripts executable: `chmod +x dev/tools/*`
- Check output directory permissions: `ls -la {$this->outputPath}`

### "Perl/Java/C not found"
Install what's missing:
```bash
# Perl
apt-get install perl libdbi-perl libdbd-mysql-perl

# Java
apt-get install default-jre

# C compiler (if building from source)
apt-get install build-essential libmysqlclient-dev
```

## Still Stuck?

### Copy-Paste This to ChatGPT

```
I'm trying to migrate from BookStack to DokuWiki and everything failed:
- PHP version crashed with: [paste error]
- Perl fallback failed because: [paste error]
- System info: PHP {$phpVersion}, Laravel {$laravelVersion}
- Database: {$dbConfig['database']} on {$dbConfig['host']}

What should I do?
```

## Nuclear Option: Start Fresh

If nothing works, you can:

1. Export BookStack data to JSON/SQL manually
2. Install DokuWiki fresh
3. Write a custom import script (or ask ChatGPT to write one)

## Pro Tips

- Always backup before migrating (you did that, right?)
- Test with a small dataset first
- Keep BookStack running until you verify DokuWiki works
- Multiple language implementations exist for a reason (PHP sucks)

## About This Tool

This migration suite exists because:
- PHP frameworks break constantly
- We needed something that actually works
- Multiple implementations = redundancy
- ChatGPT wrote better code than the original devs

**Alex Alvonellos - i use arch btw**

---

Generated: {date('Y-m-d H:i:s')}
If you're reading this, PHP has failed you. But there's still hope!
MD;
    }
}
        $markdown = preg_replace('/^####\s+(.+)$/m', '==== $1 ====', $markdown);
        $markdown = preg_replace('/^###\s+(.+)$/m', '=== $1 ===', $markdown);
        $markdown = preg_replace('/^##\s+(.+)$/m', '==== $1 ====', $markdown);
        $markdown = preg_replace('/^#\s+(.+)$/m', '===== $1 =====', $markdown);

        // Bold and italic
        $markdown = preg_replace('/\*\*\*(.+?)\*\*\*/s', '**//\1//**', $markdown);
        $markdown = preg_replace('/\*\*(.+?)\*\*/s', '**\1**', $markdown);
        $markdown = preg_replace('/\*(.+?)\*/s', '//\1//', $markdown);
        $markdown = preg_replace('/___(.+?)___/s', '**//\1//**', $markdown);
        $markdown = preg_replace('/__(.+?)__/s', '**\1**', $markdown);
        $markdown = preg_replace('/_(.+?)_/s', '//\1//', $markdown);

        // Code blocks
        $markdown = preg_replace('/```(\w+)?\n(.*?)```/s', '<code \1>\n\2</code>', $markdown);
        $markdown = preg_replace('/`(.+?)`/', "''$1''", $markdown);

        // Links
        $markdown = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '[[$2|\1]]', $markdown);

        // Lists
        $markdown = preg_replace('/^\*\s+/m', '  * ', $markdown);
        $markdown = preg_replace('/^\d+\.\s+/m', '  - ', $markdown);

        // Horizontal rule
        $markdown = preg_replace('/^---+$/m', '----', $markdown);

        return $markdown;
    }

    /**
     * Sanitize namespace for DokuWiki.
     * 
     * CRITICAL: DokuWiki has strict naming rules. Do NOT change this regex
     * unless you want to deal with broken namespaces and support tickets.
     * 
     * @param string $name The name to sanitize
     * @return string Sanitized namespace-safe name
     */
    private function sanitizeNamespace(string $name): string
    {
        // Paranoid null/empty check because PHP is garbage at type safety
        if (empty($name)) {
            return 'page';
        }
        
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9_-]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $name = trim($name, '_');
        
        // Final safety check - DokuWiki doesn't like empty names
        return $name ?: 'page';
    }

    /**
     * Sanitize filename for DokuWiki.
     * 
     * @param string $name The filename to sanitize
     * @return string Sanitized filename
     */
    private function sanitizeFilename(string $name): string
    {
        return $this->sanitizeNamespace($name);
    }

    /**
     * Check if a page should be exported.
     */
    private function shouldExportPage(Page $page): bool
    {
        if ($page->draft && !$this->includeDrafts) {
            return false;
        }

        return true;
    }

    /**
     * Check if Pandoc is installed.
     */
    private function checkPandoc(): bool
    {
        exec('which pandoc', $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Display export statistics.
     */
    private function displayStats(): void
    {
        $this->info('Export Statistics:');
        $this->table(
            ['Item', 'Count'],
            [
                ['Books', $this->stats['books']],
                ['Chapters', $this->stats['chapters']],
                ['Pages', $this->stats['pages']],
                ['Attachments', $this->stats['attachments']],
                ['Errors', $this->stats['errors']],
            ]
        );
    }
    
    /**
     * Show warning cat because users need visual aids
     */
    private function showWarningCat(): void
    {
        $cat = <<<'CAT'
        
   /\_/\  
  ( o.o )  DANGER ZONE AHEAD!
   > ^ <   This script is powered by PHP...
  /|   |\  Results may vary. Cats may explode.
 (_|   |_) 
        
CAT;
        $this->warn($cat);
        $this->warn("⚠️  You are about to run a PHP script. Please keep your expectations LOW.");
        $this->warn("⚠️  If this fails, we'll automatically use the Perl version (which actually works).\n");
    }
    
    /**
     * Estimate how badly this is going to fail
     */
    private function estimateAndWarn(): void
    {
        $totalPages = Page::count();
        $totalBooks = Book::count();
        $totalChapters = Chapter::count();
        
        $this->info("📊 Found $totalBooks books, $totalChapters chapters, and $totalPages pages");
        
        // Calculate failure probability (tongue in cheek)
        $failureProbability = min(95, 50 + ($totalPages * 0.1));
        
        $this->warn("\n⚠️  ESTIMATED FAILURE PROBABILITY: " . number_format($failureProbability, 1) . "%");
        $this->warn("   (Based on: PHP being PHP + your data size + lunar phase)");
        
        if ($totalPages > 100) {
            $this->error("\n🔥 HOLY SHIT! That's a lot of pages!");
            $this->warn("   PHP will probably run out of memory around page 73.");
            $this->warn("   But don't worry, we'll fall back to Perl when it does.\n");
        } elseif ($totalPages > 50) {
            $this->warn("\n⚠️  That's quite a few pages. Cross your fingers!\n");
        } else {
            $this->info("\n✓ Manageable size. PHP might actually survive this!\n");
        }
        
        sleep(2); // Let them read the warnings
    }
    
    /**
     * Fall back to the Perl version when PHP inevitably fails
     */
    private function fallbackToPerl(): int
    {
        $this->warn("\n" . str_repeat("=", 60));
        $this->info("🐪 SWITCHING TO PERL - A REAL PROGRAMMING LANGUAGE");
        $this->warn(str_repeat("=", 60) . "\n");
        
        $perlScript = base_path('dev/tools/bookstack2dokuwiki.pl');
        
        if (!file_exists($perlScript)) {
            $this->error("Perl script not found at: $perlScript");
            $this->error("Please check the dev/tools/ directory.");
            return 1;
        }
        
        // Extract DB credentials from config (finally, a useful feature)
        $dbHost = config('database.connections.mysql.host', 'localhost');
        $dbPort = config('database.connections.mysql.port', 3306);
        $dbName = config('database.connections.mysql.database', 'bookstack');
        $dbUser = config('database.connections.mysql.username', '');
        $dbPass = config('database.connections.mysql.password', '');
        
        $cmd = sprintf(
            'perl %s --db-host=%s --db-port=%d --db-name=%s --db-user=%s --db-pass=%s --output=%s --verbose',
            escapeshellarg($perlScript),
            escapeshellarg($dbHost),
            $dbPort,
            escapeshellarg($dbName),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($this->outputPath)
        );
        
        if ($this->includeDrafts) {
            $cmd .= ' --include-drafts';
        }
        
        $this->info("Executing Perl with your database credentials...");
        $this->comment("(Don't worry, Perl won't leak them like PHP would)\n");
        
        passthru($cmd, $returnCode);
        
        if ($returnCode === 0) {
            $this->info("\n✨ Perl succeeded where PHP failed. As expected.");
            $this->comment("\n💡 Pro tip: Just use the Perl script directly next time:");
            $this->line("   cd dev/tools && ./bookstack2dokuwiki.pl --help\n");
        }
        
        return $returnCode;
    }
}
