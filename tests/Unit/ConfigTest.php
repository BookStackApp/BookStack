<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Tests\TestCase;

/**
 * Class ConfigTest
 * Many of the tests here are to check on tweaks made
 * to maintain backwards compatibility.
 */
class ConfigTest extends TestCase
{
    public function test_filesystem_images_falls_back_to_storage_type_var()
    {
        $this->runWithEnv(['STORAGE_TYPE' => 'local_secure'], function () {
            $this->checkEnvConfigResult('STORAGE_IMAGE_TYPE', 's3', 'filesystems.images', 's3');
            $this->checkEnvConfigResult('STORAGE_IMAGE_TYPE', null, 'filesystems.images', 'local_secure');
        });
    }

    public function test_filesystem_attachments_falls_back_to_storage_type_var()
    {
        $this->runWithEnv(['STORAGE_TYPE' => 'local_secure'], function () {
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
        $original = ini_set('error_log', $temp);

        Log::channel('errorlog_plain_webserver')->info('Aww, look, a cute puppy');

        ini_set('error_log', $original);

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

    public function test_dompdf_remote_fetching_controlled_by_allow_untrusted_server_fetching_false()
    {
        $this->checkEnvConfigResult('ALLOW_UNTRUSTED_SERVER_FETCHING', 'false', 'exports.dompdf.enable_remote', false);
        $this->checkEnvConfigResult('ALLOW_UNTRUSTED_SERVER_FETCHING', 'true', 'exports.dompdf.enable_remote', true);
    }

    public function test_dompdf_paper_size_options_are_limited()
    {
        $this->checkEnvConfigResult('EXPORT_PAGE_SIZE', 'cat', 'exports.dompdf.default_paper_size', 'a4');
        $this->checkEnvConfigResult('EXPORT_PAGE_SIZE', 'letter', 'exports.dompdf.default_paper_size', 'letter');
        $this->checkEnvConfigResult('EXPORT_PAGE_SIZE', 'a4', 'exports.dompdf.default_paper_size', 'a4');
    }

    public function test_snappy_paper_size_options_are_limited()
    {
        $this->checkEnvConfigResult('EXPORT_PAGE_SIZE', 'cat', 'exports.snappy.options.page-size', 'A4');
        $this->checkEnvConfigResult('EXPORT_PAGE_SIZE', 'letter', 'exports.snappy.options.page-size', 'Letter');
        $this->checkEnvConfigResult('EXPORT_PAGE_SIZE', 'a4', 'exports.snappy.options.page-size', 'A4');
    }

    public function test_sendmail_command_is_configurable()
    {
        $this->checkEnvConfigResult('MAIL_SENDMAIL_COMMAND', '/var/sendmail -o', 'mail.mailers.sendmail.path', '/var/sendmail -o');
    }

    public function test_mail_disable_ssl_verification_alters_mailer()
    {
        $getStreamOptions = function (): array {
            /** @var EsmtpTransport $transport */
            $transport = Mail::mailer('smtp')->getSymfonyTransport();
            return $transport->getStream()->getStreamOptions();
        };

        $this->assertEmpty($getStreamOptions());


        $this->runWithEnv(['MAIL_VERIFY_SSL' => 'false'], function () use ($getStreamOptions) {
            $options = $getStreamOptions();
            $this->assertArrayHasKey('ssl', $options);
            $this->assertFalse($options['ssl']['verify_peer']);
            $this->assertFalse($options['ssl']['verify_peer_name']);
        });
    }

    public function test_non_null_mail_encryption_options_enforce_smtp_scheme()
    {
        $this->checkEnvConfigResult('MAIL_ENCRYPTION', 'tls', 'mail.mailers.smtp.require_tls', true);
        $this->checkEnvConfigResult('MAIL_ENCRYPTION', 'ssl', 'mail.mailers.smtp.require_tls', true);
        $this->checkEnvConfigResult('MAIL_ENCRYPTION', 'null', 'mail.mailers.smtp.require_tls', false);
    }

    public function test_smtp_scheme_and_certain_port_forces_tls_usage()
    {
        $isMailTlsRequired = function () {
            /** @var EsmtpTransport $transport */
            $transport = Mail::mailer('smtp')->getSymfonyTransport();
            Mail::purge('smtp');
            return $transport->isTlsRequired();
        };

        $runTest = function (string $tlsOption, int $port, bool $expectedResult) use ($isMailTlsRequired) {
            $this->runWithEnv(['MAIL_ENCRYPTION' => $tlsOption, 'MAIL_PORT' => $port], function () use ($isMailTlsRequired, $port, $expectedResult) {
                $this->assertEquals($expectedResult, $isMailTlsRequired());
            });
        };

        $runTest('null', 587, false);
        $runTest('tls', 587, true);
        $runTest('null', 465, true);
    }

    public function test_mysql_host_parsed_as_expected()
    {
        $cases = [
            '127.0.0.1' => ['127.0.0.1', 3306],
            '127.0.0.1:3307' => ['127.0.0.1', 3307],
            'a.example.com' => ['a.example.com', 3306],
            'a.example.com:3307' => ['a.example.com', 3307],
            '[::1]' => ['[::1]', 3306],
            '[::1]:123' => ['[::1]', 123],
            '[2001:db8:3c4d:0015:0000:0000:1a2f]' => ['[2001:db8:3c4d:0015:0000:0000:1a2f]', 3306],
            '[2001:db8:3c4d:0015:0000:0000:1a2f]:4567' => ['[2001:db8:3c4d:0015:0000:0000:1a2f]', 4567],
        ];

        foreach ($cases as $host => [$expectedHost, $expectedPort]) {
            $this->runWithEnv(["DB_HOST" => $host], function () use ($expectedHost, $expectedPort) {
                $this->assertEquals($expectedHost, config("database.connections.mysql.host"));
                $this->assertEquals($expectedPort, config("database.connections.mysql.port"));
            }, false);
        }
    }

    /**
     * Set an environment variable of the given name and value
     * then check the given config key to see if it matches the given result.
     * Providing a null $envVal clears the variable.
     */
    protected function checkEnvConfigResult(string $envName, ?string $envVal, string $configKey, mixed $expectedResult): void
    {
        $this->runWithEnv([$envName => $envVal], function () use ($configKey, $expectedResult) {
            $this->assertEquals($expectedResult, config($configKey));
        });
    }
}
