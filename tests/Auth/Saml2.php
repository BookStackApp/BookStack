<?php namespace Tests;


class Saml2 extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // Set default config for SAML2
        config()->set([
            'saml2.name' => 'SingleSignOn',
            'saml2.enabled' => true,
            'saml2.auto_register' => true,
            'saml2.email_attribute' => 'email',
            'saml2.display_name_attributes' => 'username',
            'saml2.external_id_attribute' => 'external_id',
            'saml2.user_to_groups' => false,
            'saml2.group_attribute' => 'group',
            'saml2.remove_from_groups' => false,
            'saml2.onelogin_overrides' => null,
            'saml2.onelogin.idp.entityId' => 'https://example.com/saml2/idp/metadata',
            'saml2.onelogin.idp.singleSignOnService.url' => 'https://example.com/saml2/idp/sso',
            'saml2.onelogin.idp.singleLogoutService.url' => 'https://example.com/saml2/idp/sls',
            'saml2.autoload_from_metadata' => false,
            'saml2.onelogin.idp.x509cert' => 'MIIEazCCAtOgAwIBAgIUe7a088Cnr4izmrnBEnx5q3HTMvYwDQYJKoZIhvcNAQELBQAwRTELMAkGA1UEBhMCR0IxEzARBgNVBAgMClNvbWUtU3RhdGUxITAfBgNVBAoMGEludGVybmV0IFdpZGdpdHMgUHR5IEx0ZDAeFw0xOTExMTYxMjE3MTVaFw0yOTExMTUxMjE3MTVaMEUxCzAJBgNVBAYTAkdCMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBXaWRnaXRzIFB0eSBMdGQwggGiMA0GCSqGSIb3DQEBAQUAA4IBjwAwggGKAoIBgQDzLe9FfdyplTxHp4SuQ9gQtZT3t+SDfvEL72ppCfFZw7+B5s5B/T73aXpoQ3S53pGI1RIWCge2iCUQ2tzm27aSNH0iu9aJYcUQZ/RITqd0ayyDks1NA2PT3TW6t3m7KV5re4P0Nb+YDeuyHdkz+jcMtpn8CmBoT0H+skha0hiqINkjkRPiHvLHVGp+tHUEA/I6mN4aB/UExSTLs79NsLUfteqqxe9+tvdUaToyDPrhPFjONs+9NKCkzIC6vcv7J6AtuKG6nET+zB9yOWgtGYQifXqQA2y5dL81BB0q5uMaBLS2pq3aPPjzU2F3+EysjySWTnCkfk7C5SsCXRu8Q+U95tunpNfwf5olE6Was48NMM+PwV7iCNMPkNzllq6PCiM+P8DrMSczzUZZQUSv6dSwPCo+YSVimEM0Og3XJTiNhQ5ANlaIn66Kw5gfoBfuiXmyIKiSDyAiDYmFaf4395wWwLkTR+cw8WfjaHswKZTomn1MR3OJsY2UJ0eRBYM+YSsCAwEAAaNTMFEwHQYDVR0OBBYEFImp2CYCGfcb7w91H/cShTCkXwR/MB8GA1UdIwQYMBaAFImp2CYCGfcb7w91H/cShTCkXwR/MA8GA1UdEwEB/wQFMAMBAf8wDQYJKoZIhvcNAQELBQADggGBAA+g/C7uL9ln+W+qBknLW81kojYflgPK1I1MHIwnMvl/ZTHX4dRXKDrk7KcUq1KjqajNV66f1cakp03IijBiO0Xi1gXUZYLoCiNGUyyp9XloiIy9Xw2PiWnrw0+yZyvVssbehXXYJl4RihBjBWul9R4wMYLOUSJDe2WxcUBhJnxyNRs+P0xLSQX6B2n6nxoDko4p07s8ZKXQkeiZ2iwFdTxzRkGjthMUv704nzsVGBT0DCPtfSaO5KJZW1rCs3yiMthnBxq4qEDOQJFIl+/LD71KbB9vZcW5JuavzBFmkKGNro/6G1I7el46IR4wijTyNFCYUuD9dtignNmpWtN8OW+ptiL/jtTySWukjys0s+vLn83CVvjB0dJtVAIYOgXFdIuii66gczwwM/LGiOExJn0dTNzsJ/IYhpxL4FBEuP0pskY0o0aUlJ2LS2j+wSQTRKsBgMjyrUrekle2ODStStn3eabjIx0/FHlpFr0jNIm/oMP7kwjtUX4zaNe47QI4Gg==',
        ]);
    }

    public function test_metadata_endpoint_displays_xml_as_expected()
    {
        $req = $this->get('/saml2/metadata');
        $req->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $req->assertSee('md:EntityDescriptor');
        $req->assertSee(url('/saml2/acs'));
    }

    public function test_onelogin_overrides_functions_as_expected()
    {
        $json = '{"sp": {"assertionConsumerService": {"url": "https://example.com/super-cats"}}, "contactPerson": {"technical": {"givenName": "Barry Scott", "emailAddress": "barry@example.com"}}}';
        config()->set(['saml2.onelogin_overrides' => $json]);

        $req = $this->get('/saml2/metadata');
        $req->assertSee('https://example.com/super-cats');
        $req->assertSee('md:ContactPerson');
        $req->assertSee('<md:GivenName>Barry Scott</md:GivenName>');
    }
}
