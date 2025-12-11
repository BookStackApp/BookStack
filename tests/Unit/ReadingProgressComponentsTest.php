<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadingProgressComponentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reading_progress_bar_component_can_be_rendered()
    {
        $this->withoutExceptionHandling();
        
        // Test that the component files exist
        $this->assertFileExists(resource_path('js/components/reading-progress-bar.js'));
        $this->assertFileExists(resource_path('js/components/page-reading-progress.js'));
        $this->assertFileExists(resource_path('js/components/user-reading-stats.js'));
        $this->assertFileExists(resource_path('js/components/chapter-navigation.js'));
    }

    /** @test */
    public function reading_progress_service_can_be_instantiated()
    {
        $this->assertFileExists(resource_path('js/services/reading-progress.js'));
        
        // Check if the service has the required methods
        $serviceContent = file_get_contents(resource_path('js/services/reading-progress.js'));
        
        $this->assertStringContainsString('getProgress', $serviceContent);
        $this->assertStringContainsString('updateProgress', $serviceContent);
        $this->assertStringContainsString('deleteProgress', $serviceContent);
        $this->assertStringContainsString('getUserStats', $serviceContent);
        $this->assertStringContainsString('getUserProgress', $serviceContent);
        $this->assertStringContainsString('getChapterProgress', $serviceContent);
    }

    /** @test */
    public function css_styles_are_properly_defined()
    {
        $this->assertFileExists(resource_path('sass/components/_reading-progress.scss'));
        
        $cssContent = file_get_contents(resource_path('sass/components/_reading-progress.scss'));
        
        // Check for required CSS classes
        $this->assertStringContainsString('.reading-progress-bar', $cssContent);
        $this->assertStringContainsString('.progress-fill', $cssContent);
        $this->assertStringContainsString('.progress-text', $cssContent);
        $this->assertStringContainsString('.chapter-navigation', $cssContent);
        $this->assertStringContainsString('.user-reading-stats', $cssContent);
        
        // Check for responsive design
        $this->assertStringContainsString('@media', $cssContent);
        
        // Check for dark mode support
        $this->assertStringContainsString('.dark-mode', $cssContent);
    }

    /** @test */
    public function api_endpoints_are_properly_defined()
    {
        $routesContent = file_get_contents(base_path('routes/api.php'));
        
        // Check for reading progress endpoints
        $this->assertStringContainsString('/reading-progress', $routesContent);
        
        // Check for specific endpoints
        $this->assertStringContainsString('pages/{id}/reading-progress', $routesContent);
        $this->assertStringContainsString('users/me/reading-progress', $routesContent);
        $this->assertStringContainsString('users/me/reading-stats', $routesContent);
        
        // Check for HTTP methods
        $this->assertStringContainsString('GET', $routesContent);
        $this->assertStringContainsString('PUT', $routesContent);
        $this->assertStringContainsString('DELETE', $routesContent);
    }

    /** @test */
    public function database_migration_is_properly_defined()
    {
        $migrationFiles = glob(database_path('migrations/*_create_reading_progress_table.php'));
        
        $this->assertNotEmpty($migrationFiles, 'Reading progress migration file not found');
        
        $migrationContent = file_get_contents($migrationFiles[0]);
        
        // Check for required columns
        $this->assertStringContainsString('user_id', $migrationContent);
        $this->assertStringContainsString('page_id', $migrationContent);
        $this->assertStringContainsString('progress_percentage', $migrationContent);
        $this->assertStringContainsString('current_scroll_position', $migrationContent);
        $this->assertStringContainsString('time_spent_seconds', $migrationContent);
        $this->assertStringContainsString('is_completed', $migrationContent);
        
        // Check for indexes and foreign keys
        $this->assertStringContainsString('foreign', $migrationContent);
        $this->assertStringContainsString('unique', $migrationContent);
        $this->assertStringContainsString('index', $migrationContent);
    }

    /** @test */
    public function model_has_proper_relationships_and_methods()
    {
        $modelContent = file_get_contents(app_path('Entities/Models/ReadingProgress.php'));
        
        // Check for relationships
        $this->assertStringContainsString('belongsTo', $modelContent);
        $this->assertStringContainsString('user()', $modelContent);
        $this->assertStringContainsString('page()', $modelContent);
        
        // Check for custom methods
        $this->assertStringContainsString('forUserAndPage', $modelContent);
        $this->assertStringContainsString('updateOrCreateProgress', $modelContent);
        $this->assertStringContainsString('getUserReadingStats', $modelContent);
        
        // Check for fillable fields
        $this->assertStringContainsString('fillable', $modelContent);
        
        // Check for casts
        $this->assertStringContainsString('casts', $modelContent);
    }

    /** @test */
    public function controller_has_proper_methods_and_validation()
    {
        $controllerContent = file_get_contents(app_path('Entities/Controllers/ReadingProgressApiController.php'));
        
        // Check for controller methods
        $this->assertStringContainsString('getProgress', $controllerContent);
        $this->assertStringContainsString('updateProgress', $controllerContent);
        $this->assertStringContainsString('getUserStats', $controllerContent);
        $this->assertStringContainsString('getUserProgress', $controllerContent);
        $this->assertStringContainsString('deleteProgress', $controllerContent);
        
        // Check for validation
        $this->assertStringContainsString('validate', $controllerContent);
        
        // Check for authorization
        $this->assertStringContainsString('authorize', $controllerContent);
        
        // Check for proper responses
        $this->assertStringContainsString('response()->json', $controllerContent);
    }

    /** @test */
    public function all_vue_components_have_required_structure()
    {
        $components = [
            'reading-progress-bar.js',
            'page-reading-progress.js',
            'user-reading-stats.js',
            'chapter-navigation.js'
        ];

        foreach ($components as $component) {
            $componentPath = resource_path("js/components/{$component}");
            $this->assertFileExists($componentPath);
            
            $content = file_get_contents($componentPath);
            
            // Check for Vue component structure
            $this->assertStringContainsString('defineComponent', $content);
            $this->assertStringContainsString('props', $content);
            $this->assertStringContainsString('data', $content);
            $this->assertStringContainsString('methods', $content);
            $this->assertStringContainsString('template', $content);
            
            // Check for service usage
            $this->assertStringContainsString('readingProgressService', $content);
        }
    }
}