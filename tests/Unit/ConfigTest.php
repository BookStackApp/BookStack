<?php namespace Tests\Unit;

use Tests\TestCase;

/**
 * Class ConfigTest
 * Many of the tests here are to check on tweaks made
 * to maintain backwards compatibility.
 *
 * @package Tests
 */
class ConfigTest extends TestCase
{

    public function test_filesystem_images_falls_back_to_storage_type_var()
    {
        $this->runWithEnv('STORAGE_TYPE', 'local_secure', function() {
            $this->checkEnvConfigResult('STORAGE_IMAGE_TYPE', 's3', 'filesystems.images', 's3');
            $this->checkEnvConfigResult('STORAGE_IMAGE_TYPE', null, 'filesystems.images', 'local_secure');
        });
    }

    public function test_filesystem_attachments_falls_back_to_storage_type_var()
    {
        $this->runWithEnv('STORAGE_TYPE', 'local_secure', function() {
            $this->checkEnvConfigResult('STORAGE_ATTACHMENT_TYPE', 's3', 'filesystems.attachments', 's3');
            $this->checkEnvConfigResult('STORAGE_ATTACHMENT_TYPE', null, 'filesystems.attachments', 'local_secure');
        });
    }

    public function test_app_url_blank_if_old_default_value()
    {
        $initUrl = 'https://example.com/docs';
        $oldDefault = 'http://bookstack.dev';
        $this->checkEnvConfigResult('APP_URL', $initUrl, 'app.url', $initUrl);
        $this->checkEnvConfigResult('APP_URL', $oldDefault, 'app.url', '');
    }

    /**
     * Set an environment variable of the given name and value
     * then check the given config key to see if it matches the given result.
     * Providing a null $envVal clears the variable.
     * @param string $envName
     * @param string|null $envVal
     * @param string $configKey
     * @param string $expectedResult
     */
    protected function checkEnvConfigResult(string $envName, $envVal, string $configKey, string $expectedResult)
    {
        $this->runWithEnv($envName, $envVal, function() use ($configKey, $expectedResult) {
            $this->assertEquals($expectedResult, config($configKey));
        });
    }

}