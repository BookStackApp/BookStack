<?php

namespace Tests\Commands;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class InstallModuleCommandTest extends TestCase
{
    public function test_local_module_install_with_active_theme()
    {
        $this->usingThemeFolder(function () {
            $zip = $this->getModuleZipPath();
            $expectedInstallPath = theme_path('modules/test-module');
            $this->artisan('bookstack:install-module', ['location' => $zip])
                ->expectsOutput("\nThis will install a module from: {$zip}\n\nModules can contain code which would have the ability to do anything on the BookStack host server.\nYou should only install modules from trusted sources.")
                ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
                ->expectsOutput('Module "Test Module" (v1.0.0) successfully installed!')
                ->expectsOutput("Install location: {$expectedInstallPath}")
                ->assertExitCode(0);

            $this->assertDirectoryExists($expectedInstallPath);
            $this->assertFileExists($expectedInstallPath . '/bookstack-module.json');
        });
    }

    public function test_remote_module_install_with_active_theme()
    {
        $this->usingThemeFolder(function () {
            $zip = $this->getModuleZipPath();

            $http = $this->mockHttpClient([
                new Response(200, ['Content-Length' => filesize($zip)], file_get_contents($zip))
            ]);
            $expectedInstallPath = theme_path('modules/test-module');

            $this->artisan('bookstack:install-module', ['location' => 'https://example.com/test-module.zip'])
                ->expectsOutput("\nThis will download a module from: example.com\n\nModules can contain code which would have the ability to do anything on the BookStack host server.\nYou should only install modules from trusted sources.")
                ->expectsConfirmation('Are you sure you trust this source?', 'yes')
                ->expectsOutput('Module "Test Module" (v1.0.0) successfully installed!')
                ->expectsOutput("Install location: {$expectedInstallPath}")
                ->assertExitCode(0);

            $this->assertEquals(1, $http->requestCount());
            $request = $http->requestAt(0);
            $this->assertEquals('/test-module.zip', $request->getUri()->getPath());

            $this->assertDirectoryExists($expectedInstallPath);
            $this->assertFileExists($expectedInstallPath . '/bookstack-module.json');
        });
    }

    public function test_remote_http_module_warns_and_prompts_users()
    {
        $this->usingThemeFolder(function () {
            $zip = $this->getModuleZipPath();

            $http = $this->mockHttpClient([
                new Response(200, ['Content-Length' => filesize($zip)], file_get_contents($zip))
            ]);
            $expectedInstallPath = theme_path('modules/test-module');

            $this->artisan('bookstack:install-module', ['location' => 'http://example.com/test-module.zip'])
                ->expectsOutput("\nThis will download a module from: example.com\n\nModules can contain code which would have the ability to do anything on the BookStack host server.\nYou should only install modules from trusted sources.")
                ->expectsConfirmation('Are you sure you trust this source?', 'yes')
                ->expectsOutput("You are downloading a module from an insecure HTTP source.\nWe recommend only using HTTPS sources to avoid various security risks.")
                ->expectsConfirmation('Are you sure you want to continue without HTTPS?', 'yes')
                ->expectsOutput('Module "Test Module" (v1.0.0) successfully installed!')
                ->expectsOutput("Install location: {$expectedInstallPath}")
                ->assertExitCode(0);

            $request = $http->requestAt(0);
            $this->assertEquals('/test-module.zip', $request->getUri()->getPath());
        });
    }

    public function test_remote_module_install_follows_redirects()
    {
        $this->usingThemeFolder(function () {
            $zip = $this->getModuleZipPath();

            $http = $this->mockHttpClient([
                new Response(302, ['Location' => 'https://example.com/a-test-module.zip']),
                new Response(200, ['Content-Length' => filesize($zip)], file_get_contents($zip))
            ]);

            $this->artisan('bookstack:install-module', ['location' => 'https://example.com/test-module.zip'])
                ->expectsConfirmation('Are you sure you trust this source?', 'yes')
                ->assertExitCode(0);

            $this->assertEquals(2, $http->requestCount());
            $this->assertEquals('/test-module.zip', $http->requestAt(0)->getUri()->getPath());
            $this->assertEquals('/a-test-module.zip', $http->requestAt(1)->getUri()->getPath());
        });
    }

    public function test_remote_module_install_prompts_on_following_redirects_to_different_origin()
    {
        $this->usingThemeFolder(function () {
            $zip = $this->getModuleZipPath();

            $http = $this->mockHttpClient([
                new Response(302, ['Location' => 'http://example.com/a-test-module.zip']),
                new Response(301, ['Location' => 'https://a.example.com:8080/a-test-module.zip']),
                new Response(200, ['Content-Length' => filesize($zip)], file_get_contents($zip))
            ]);

            $this->artisan('bookstack:install-module', ['location' => 'https://example.com/test-module.zip'])
                ->expectsConfirmation('Are you sure you trust this source?', 'yes')
                ->expectsOutput('The download URL is redirecting to a different site: http://example.com')
                ->expectsConfirmation('Do you trust downloading the module from this site?', 'yes')
                ->expectsOutput('The download URL is redirecting to a different site: https://a.example.com:8080')
                ->expectsConfirmation('Do you trust downloading the module from this site?', 'yes')
                ->assertExitCode(0);

            $this->assertEquals(3, $http->requestCount());
            $this->assertEquals('https', $http->requestAt(0)->getUri()->getScheme());
            $this->assertEquals('http', $http->requestAt(1)->getUri()->getScheme());
            $this->assertEquals('a.example.com', $http->requestAt(2)->getUri()->getHost());
        });
    }

    public function test_remote_module_install_redirect_origin_prompt_rejection()
    {
        $this->usingThemeFolder(function () {
            $http = $this->mockHttpClient([
                new Response(302, ['Location' => 'http://example.com/a-test-module.zip']),
                new Response(301, ['Location' => 'https://a.example.com:8080/a-test-module.zip']),
            ]);

            $this->artisan('bookstack:install-module', ['location' => 'https://example.com/test-module.zip'])
                ->expectsConfirmation('Are you sure you trust this source?', 'yes')
                ->expectsOutput('The download URL is redirecting to a different site: http://example.com')
                ->expectsConfirmation('Do you trust downloading the module from this site?', 'no')
                ->assertExitCode(1);

            $this->assertEquals(1, $http->requestCount());
            $this->assertEquals('https', $http->requestAt(0)->getUri()->getScheme());
        });
    }

    public function test_remote_module_install_has_redirect_limit()
    {
        $this->usingThemeFolder(function () {
            $http = $this->mockHttpClient([
                new Response(302, ['Location' => 'https://example.com/a-test-module.zip']),
                new Response(302, ['Location' => 'https://example.com/b-test-module.zip']),
                new Response(302, ['Location' => 'https://example.com/c-test-module.zip']),
                new Response(302, ['Location' => 'https://example.com/d-test-module.zip']),
            ]);

            $this->artisan('bookstack:install-module', ['location' => 'https://example.com/test-module.zip'])
                ->expectsConfirmation('Are you sure you trust this source?', 'yes')
                ->expectsOutput('ERROR: Failed to download module from https://example.com/test-module.zip')
                ->assertExitCode(1);

            $this->assertEquals(4, $http->requestCount());
            $this->assertEquals('/c-test-module.zip', $http->requestAt(3)->getUri()->getPath());
        });
    }

    public function test_remote_module_install_download_failures_are_announced_to_user()
    {
        $this->usingThemeFolder(function () {
            $http = $this->mockHttpClient([
                new Response(404),
            ]);

            $this->artisan('bookstack:install-module', ['location' => 'https://example.com/test-module.zip'])
                ->expectsConfirmation('Are you sure you trust this source?', 'yes')
                ->expectsOutput('ERROR: Failed to download module from https://example.com/test-module.zip')
                ->expectsOutput('Download failed with status code 404')
                ->assertExitCode(1);
            $this->assertEquals(1, $http->requestCount());
        });
    }

    public function test_run_with_invalid_path_exits_early()
    {
        $this->artisan('bookstack:install-module', ['location' => '/not-found.zip'])
            ->expectsOutput('ERROR: Module file not found at /not-found.zip')
            ->assertExitCode(1);
    }

    public function test_run_with_invalid_zip_has_early_exit()
    {
        $zip = $this->getModuleZipPath();
        file_put_contents($zip, 'invalid zip');

        $this->artisan('bookstack:install-module', ['location' => $zip])
            ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
            ->expectsOutput("ERROR: Cannot open ZIP file at {$zip}")
            ->assertExitCode(1);
    }

    public function test_run_with_large_zip_has_early_exit()
    {
        $zip = $this->getModuleZipPath(null, [
            'large-file.txt' => str_repeat('a', 1024 * 1024 * 51)
        ]);

        $this->artisan('bookstack:install-module', ['location' => $zip])
            ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
            ->expectsOutput("ERROR: Module ZIP file contents are too large. Maximum size is 50MB")
            ->assertExitCode(1);
    }

    public function test_run_with_invalid_module_data_has_early_exit()
    {
        $zip = $this->getModuleZipPath([
            'name' => 'Invalid Module',
            'description' => 'A module with invalid data',
            'version' => 'dog',
        ]);

        $this->artisan('bookstack:install-module', ['location' => $zip])
            ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
            ->expectsOutput("ERROR: Failed to read module metadata with error: Module in folder \"_temp\" has an invalid 'version' format. Expected semantic version format like '1.0.0' or 'v1.0.0'")
            ->assertExitCode(1);
    }

    public function test_module_zip_when_files_in_nested_directory()
    {
        $this->usingThemeFolder(function ($themeFolder) {
            $zip = new ZipArchive();
            $zipFile = tempnam(sys_get_temp_dir(), 'bs-test-module');
            $zip->open($zipFile, ZipArchive::CREATE);

            $zip->addEmptyDir('mod');
            $zip->addFromString('mod/bookstack-module.json', json_encode($metadata ?? [
                'name' => 'Test Module',
                'description' => 'A test module for BookStack',
                'version' => '1.0.0',
            ]));
            $zip->addFromString('mod/functions.php', '<?php $a = "cat";');
            $zip->addEmptyDir('mod/a');
            $zip->addFromString('mod/a/cat.txt', 'Meow');
            $zip->close();

            $this->artisan('bookstack:install-module', ['location' => $zipFile])
                ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
                ->assertExitCode(0);

            $modulePath = glob(theme_path('modules/*'), GLOB_ONLYDIR)[0];
            $this->assertFileExists($modulePath . '/a/cat.txt');
            $contents = file_get_contents($modulePath . '/a/cat.txt');
            $this->assertEquals('Meow', $contents);
        });
    }

    public function test_module_install_negates_zip_slip()
    {
        $this->usingThemeFolder(function () {
            $zip = $this->getModuleZipPath(null, [
                '../parent.txt' => str_repeat('dog', 10)
            ]);

            $expectedInstallPath = theme_path('modules/test-module');
            $this->artisan('bookstack:install-module', ['location' => $zip])
                ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
                ->expectsOutput("ERROR: Failed to install module with error: Failed to load extract files from module ZIP with error: Bad file path found in module ZIP file: ../parent.txt")
                ->assertExitCode(1);

            $this->assertDirectoryDoesNotExist($expectedInstallPath);
        });
    }

    public function test_local_module_install_without_active_theme_can_setup_theme_folder()
    {
        $zip = $this->getModuleZipPath();
        $expectedThemePath = base_path('themes/custom');
        File::deleteDirectory($expectedThemePath);

        $this->artisan('bookstack:install-module', ['location' => $zip])
            ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
            ->expectsConfirmation('No active theme folder found, would you like to create one?', 'yes')
            ->expectsOutput("Created theme folder at {$expectedThemePath}")
            ->expectsOutput("You will need to set APP_THEME=custom in your BookStack env configuration to enable this theme!")
            ->expectsOutput('Module "Test Module" (v1.0.0) successfully installed!')
            ->assertExitCode(0);

        $this->assertDirectoryExists($expectedThemePath . '/modules/test-module');

        File::deleteDirectory($expectedThemePath);
    }

    public function test_local_module_install_with_active_theme_and_conflicting_modules_file_causes_early_exit()
    {
        $this->usingThemeFolder(function () {
            $zip = $this->getModuleZipPath();
            File::put(theme_path('modules'), '{}');

            $this->artisan('bookstack:install-module', ['location' => $zip])
                ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
                ->expectsOutput("ERROR: Cannot create a modules folder, file already exists at " . theme_path('modules'))
                ->assertExitCode(1);
        });
    }

    public function test_single_existing_module_with_same_name_replace()
    {
        $this->usingThemeFolder(function () {
            $original = $this->createModuleFolderInCurrentTheme(['name' => 'Test Module', 'description' => 'cat', 'version' => '1.0.0']);
            $new = $this->getModuleZipPath(['name' => 'Test Module', 'description' => '', 'version' => '2.0.0']);

            $this->artisan('bookstack:install-module', ['location' => $new])
                ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
                ->expectsOutput('The following modules already exist with the same name:')
                ->expectsOutput('Test Module (test-module:v1.0.0) - cat')
                ->expectsChoice('What would you like to do?', 'Replace existing module', ['Cancel module install', 'Add alongside existing module', 'Replace existing module'])
                ->expectsOutput("Replacing existing module in test-module folder")
                ->assertExitCode(0);

            $this->assertFileExists($original . '/bookstack-module.json');
            $metadata = json_decode(file_get_contents($original . '/bookstack-module.json'), true);
            $this->assertEquals('2.0.0', $metadata['version']);
        });
    }

    public function test_single_existing_module_with_same_name_cancel()
    {
        $this->usingThemeFolder(function () {
            $original = $this->createModuleFolderInCurrentTheme(['name' => 'Test Module', 'description' => 'cat', 'version' => '1.0.0']);
            $new = $this->getModuleZipPath(['name' => 'Test Module', 'description' => '', 'version' => '2.0.0']);

            $this->artisan('bookstack:install-module', ['location' => $new])
                ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
                ->expectsOutput('The following modules already exist with the same name:')
                ->expectsOutput('Test Module (test-module:v1.0.0) - cat')
                ->expectsChoice('What would you like to do?', 'Cancel module install', ['Cancel module install', 'Add alongside existing module', 'Replace existing module'])
                ->assertExitCode(1);

            $this->assertFileExists($original . '/bookstack-module.json');
            $metadata = json_decode(file_get_contents($original . '/bookstack-module.json'), true);
            $this->assertEquals('1.0.0', $metadata['version']);
        });
    }

    public function test_single_existing_module_with_same_name_add()
    {
        $this->usingThemeFolder(function () {
            $original = $this->createModuleFolderInCurrentTheme(['name' => 'Test Module', 'description' => 'cat', 'version' => '1.0.0']);
            $new = $this->getModuleZipPath(['name' => 'Test Module', 'description' => '', 'version' => '2.0.0']);

            $this->artisan('bookstack:install-module', ['location' => $new])
                ->expectsConfirmation('Are you sure you want to install this module?', 'yes')
                ->expectsOutput('The following modules already exist with the same name:')
                ->expectsOutput('Test Module (test-module:v1.0.0) - cat')
                ->expectsChoice('What would you like to do?', 'Add alongside existing module', ['Cancel module install', 'Add alongside existing module', 'Replace existing module'])
                ->assertExitCode(0);

            $dirs = File::directories(theme_path('modules/'));
            $this->assertCount(2, $dirs);
        });
    }

    protected function createModuleFolderInCurrentTheme(array|null $metadata = null, array $extraFiles = []): string
    {
        $original = $this->getModuleZipPath($metadata, $extraFiles);
        $targetPath = theme_path('modules/test-module');
        mkdir($targetPath, 0777, true);
        $originalZip = new ZipArchive();
        $originalZip->open($original);
        $originalZip->extractTo($targetPath);
        $originalZip->close();

        return $targetPath;
    }

    protected function getModuleZipPath(array|null $metadata = null, array $extraFiles = []): string
    {
        $zip = new ZipArchive();
        $tmpFile = tempnam(sys_get_temp_dir(), 'bs-test-module');
        $zip->open($tmpFile, ZipArchive::CREATE);

        $zip->addFromString('bookstack-module.json', json_encode($metadata ?? [
            'name' => 'Test Module',
            'description' => 'A test module for BookStack',
            'version' => '1.0.0',
        ]));

        foreach ($extraFiles as $path => $contents) {
            $zip->addFromString($path, $contents);
        }

        $zip->close();
        return $tmpFile;
    }
}
