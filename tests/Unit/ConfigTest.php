<?php namespace Tests\Unit;

use Illuminate\Support\Facades\Log;
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

    public function test_errorlog_plain_webserver_channel()
    {
        // We can't full test this due to it being targeted for the SAPI logging handler
        // so we just overwrite that component so we can capture the error log output.
        config()->set([
            'logging.channels.errorlog_plain_webserver.handler_with' => [0],
        ]);

        $temp = tempnam(sys_get_temp_dir(), 'bs-test');
        $original = ini_set( 'error_log', $temp);

        Log::channel('errorlog_plain_webserver')->info('Aww, look, a cute puppy');

        ini_set( 'error_log', $original);

        $output = file_get_contents($temp);
        $this->assertStringContainsString('Aww, look, a cute puppy', $output);
        $this->assertStringNotContainsString('INFO', $output);
        $this->assertStringNotContainsString('info', $output);
        $this->assertStringNotContainsString('testing', $output);
    }

    public function test_session_cookie_uses_sub_path_from_app_url()
    {
        $this->checkEnvConfigResult('APP_URL', 'https://example.com', 'session.path', '/');
        $this->checkEnvConfigResult('APP_URL', 'https://a.com/b', 'session.path', '/b');
        $this->checkEnvConfigResult('APP_URL', 'https://a.com/b/d/e', 'session.path', '/b/d/e');
        $this->checkEnvConfigResult('APP_URL', '', 'session.path', '/');
    }

    public function test_saml2_idp_authn_context_string_parsed_as_space_separated_array()
    {
        $this->checkEnvConfigResult(
            'SAML2_IDP_AUTHNCONTEXT',
            'urn:federation:authentication:windows urn:federation:authentication:linux',
            'saml2.onelogin.security.requestedAuthnContext',
            ['urn:federation:authentication:windows', 'urn:federation:authentication:linux']
        );
    }

    /**
     * Set an environment variable of the given name and value
     * then check the given config key to see if it matches the given result.
     * Providing a null $envVal clears the variable.
     * @param mixed $expectedResult
     */
    protected function checkEnvConfigResult(string $envName, ?string $envVal, string $configKey, $expectedResult)
    {
        $this->runWithEnv($envName, $envVal, function() use ($configKey, $expectedResult) {
            $this->assertEquals($expectedResult, config($configKey));
        });
    }

}