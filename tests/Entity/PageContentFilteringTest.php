<?php

namespace Tests\Entity;

use Tests\TestCase;

class PageContentFilteringTest extends TestCase
{
    public function test_page_content_scripts_removed_by_default()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $script = 'abc123<script>console.log("hello-test")</script>abc123';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('abc123abc123');
    }

    public function test_more_complex_content_script_escaping_scenarios()
    {
        config()->set('app.content_filtering', 'j');

        $checks = [
            "<p>Some script</p><script>alert('cat')</script>",
            "<div><div><div><div><p>Some script</p><script>alert('cat')</script></div></div></div></div>",
            "<p>Some script<script>alert('cat')</script></p>",
            "<p>Some script <div><script>alert('cat')</script></div></p>",
            "<p>Some script <script><div>alert('cat')</script></div></p>",
            "<p>Some script <script><div>alert('cat')</script><script><div>alert('cat')</script></p><script><div>alert('cat')</script>",
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<script>');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '</script>');
        }
    }

    public function test_js_and_base64_src_urls_are_removed()
    {
        config()->set('app.content_filtering', 'j');

        $checks = [
            '<iframe src="javascript:alert(document.cookie)"></iframe>',
            '<iframe src="JavAScRipT:alert(document.cookie)"></iframe>',
            '<iframe src="JavAScRipT:alert(document.cookie)"></iframe>',
            '<iframe SRC=" javascript: alert(document.cookie)"></iframe>',
            '<iframe src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<iframe src="DaTa:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<iframe src=" data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<img src="javascript:alert(document.cookie)"/>',
            '<img src="JavAScRipT:alert(document.cookie)"/>',
            '<img src="JavAScRipT:alert(document.cookie)"/>',
            '<img SRC=" javascript: alert(document.cookie)"/>',
            '<img src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<img src="DaTa:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<img src=" data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<iframe srcdoc="<script>window.alert(document.cookie)</script>"></iframe>',
            '<iframe SRCdoc="<script>window.alert(document.cookie)</script>"></iframe>',
            '<IMG SRC=`javascript:alert("RSnake says, \'XSS\'")`>',
            '<object data="javascript:alert(document.cookie)"></object>',
            '<object data="JavAScRipT:alert(document.cookie)"></object>',
            '<object data="JavAScRipT:alert(document.cookie)"></object>',
            '<object SRC=" javascript: alert(document.cookie)"></object>',
            '<object data="data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></object>',
            '<object data="DaTa:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></object>',
            '<object data=" data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></object>',
            '<embed src="javascript:alert(document.cookie)"/>',
            '<embed src="JavAScRipT:alert(document.cookie)"/>',
            '<embed src="JavAScRipT:alert(document.cookie)"/>',
            '<embed SRC=" javascript: alert(document.cookie)"/>',
            '<embed src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<embed src="DaTa:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<embed src=" data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $html = $this->withHtml($pageView);
            $html->assertElementNotContains('.page-content', '<object');
            $html->assertElementNotContains('.page-content', 'data=');
            $html->assertElementNotContains('.page-content', '<iframe>');
            $html->assertElementNotContains('.page-content', '<img');
            $html->assertElementNotContains('.page-content', '</iframe>');
            $html->assertElementNotContains('.page-content', 'src=');
            $html->assertElementNotContains('.page-content', 'javascript:');
            $html->assertElementNotContains('.page-content', 'data:');
            $html->assertElementNotContains('.page-content', 'base64');
        }
    }

    public function test_javascript_uri_links_are_removed()
    {
        config()->set('app.content_filtering', 'j');

        $checks = [
            '<a id="xss" href="javascript:alert(document.cookie)>Click me</a>',
            '<a id="xss" href="javascript: alert(document.cookie)>Click me</a>',
            '<a id="xss" href="JaVaScRiPt: alert(document.cookie)>Click me</a>',
            '<a id="xss" href=" JaVaScRiPt: alert(document.cookie)>Click me</a>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<a id="xss"');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'href=javascript:');
        }
    }

    public function test_form_filtering_is_controlled_by_config()
    {
        config()->set('app.content_filtering', '');
        $page = $this->entities->page();
        $page->html = '<form><input type="text" id="dont-see-this" value="test"></form>';
        $page->save();

        $this->asEditor()->get($page->getUrl())->assertSee('dont-see-this', false);

        config()->set('app.content_filtering', 'f');
        $this->get($page->getUrl())->assertDontSee('dont-see-this', false);
    }

    public function test_form_actions_with_javascript_are_removed()
    {
        config()->set('app.content_filtering', 'j');

        $checks = [
            '<customform><custominput id="xss" type=submit formaction=javascript:alert(document.domain) value=Submit><custominput></customform>',
            '<customform ><custombutton id="xss" formaction="JaVaScRiPt:alert(document.domain)">Click me</custombutton></customform>',
            '<customform ><custombutton id="xss" formaction=javascript:alert(document.domain)>Click me</custombutton></customform>',
            '<customform id="xss" action=javascript:alert(document.domain)><input type=submit value=Submit></customform>',
            '<customform id="xss" action="JaVaScRiPt:alert(document.domain)"><input type=submit value=Submit></customform>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $pageView->assertDontSee('id="xss"', false);
            $pageView->assertDontSee('action=javascript:', false);
            $pageView->assertDontSee('action=JaVaScRiPt:', false);
            $pageView->assertDontSee('formaction=javascript:', false);
            $pageView->assertDontSee('formaction=JaVaScRiPt:', false);
        }
    }

    public function test_form_elements_are_removed()
    {
        config()->set('app.content_filtering', 'f');

        $checks = [
            '<p>thisisacattofind</p><form>thisdogshouldnotbefound</form>',
            '<p>thisisacattofind</p><input type="text" value="thisdogshouldnotbefound">',
            '<p>thisisacattofind</p><select><option>thisdogshouldnotbefound</option></select>',
            '<p>thisisacattofind</p><textarea>thisdogshouldnotbefound</textarea>',
            '<p>thisisacattofind</p><fieldset>thisdogshouldnotbefound</fieldset>',
            '<p>thisisacattofind</p><button>thisdogshouldnotbefound</button>',
            '<p>thisisacattofind</p><BUTTON>thisdogshouldnotbefound</BUTTON>',
            <<<'TESTCASE'
<svg width="200" height="100" xmlns="http://www.w3.org/2000/svg">
  <foreignObject width="100%" height="100%">
    
    <body xmlns="http://www.w3.org/1999/xhtml">
    <p>thisisacattofind</p>
      <form>
        <p>thisdogshouldnotbefound</p>
      </form>
      <input type="text" placeholder="thisdogshouldnotbefound" />
      <button type="submit">thisdogshouldnotbefound</button>
    </body>

  </foreignObject>
</svg>
TESTCASE

        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $pageView->assertSee('thisisacattofind');
            $pageView->assertDontSee('thisdogshouldnotbefound');
        }
    }

    public function test_form_attributes_are_removed()
    {
        config()->set('app.content_filtering', 'f');

        $withinSvgSample = <<<'TESTCASE'
<svg width="200" height="100" xmlns="http://www.w3.org/2000/svg">
  <foreignObject width="100%" height="100%">
    
    <body xmlns="http://www.w3.org/1999/xhtml">
    <p formaction="a">thisisacattofind</p>
    <p formaction="a">thisisacattofind</p>
    </body>

  </foreignObject>
</svg>
TESTCASE;

        $checks = [
            'formaction' => '<p formaction="a">thisisacattofind</p>',
            'form' => '<p form="a">thisisacattofind</p>',
            'formmethod' => '<p formmethod="a">thisisacattofind</p>',
            'formtarget' => '<p formtarget="a">thisisacattofind</p>',
            'FORMTARGET' => '<p FORMTARGET="a">thisisacattofind</p>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $attribute => $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $pageView->assertSee('thisisacattofind');
            $this->withHtml($pageView)->assertElementNotExists(".page-content [{$attribute}]");
        }

        $page->html = $withinSvgSample;
        $page->save();
        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $html = $this->withHtml($pageView);
        foreach ($checks as $attribute => $check) {
            $pageView->assertSee('thisisacattofind');
            $html->assertElementNotExists(".page-content [{$attribute}]");
        }
    }

    public function test_metadata_redirects_are_removed()
    {
        config()->set('app.content_filtering', 'h');

        $checks = [
            '<meta http-equiv="refresh" content="0; url=//external_url">',
            '<meta http-equiv="refresh" ConTeNt="0; url=//external_url">',
            '<meta http-equiv="refresh" content="0; UrL=//external_url">',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<meta>');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '</meta>');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'content=');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'external_url');
        }
    }

    public function test_page_inline_on_attributes_removed_by_default()
    {
        config()->set('app.content_filtering', 'j');

        $this->asEditor();
        $page = $this->entities->page();
        $script = '<p onmouseenter="console.log(\'test\')">Hello</p>';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('<p>Hello</p>', false);
    }

    public function test_more_complex_inline_on_attributes_escaping_scenarios()
    {
        config()->set('app.content_filtering', 'j');

        $checks = [
            '<p onclick="console.log(\'test\')">Hello</p>',
            '<p OnCliCk="console.log(\'test\')">Hello</p>',
            '<div>Lorem ipsum dolor sit amet.</div><p onclick="console.log(\'test\')">Hello</p>',
            '<div>Lorem ipsum dolor sit amet.<p onclick="console.log(\'test\')">Hello</p></div>',
            '<div><div><div><div>Lorem ipsum dolor sit amet.<p onclick="console.log(\'test\')">Hello</p></div></div></div></div>',
            '<div onclick="console.log(\'test\')">Lorem ipsum dolor sit amet.</div><p onclick="console.log(\'test\')">Hello</p><div></div>',
            '<a a="<img src=1 onerror=\'alert(1)\'> ',
            '\<a onclick="alert(document.cookie)"\>xss link\</a\>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'onclick');
        }
    }

    public function test_page_content_scripts_show_with_filters_disabled()
    {
        $this->asEditor();
        $page = $this->entities->page();
        config()->set('app.content_filtering', '');

        $script = 'abc123<script>console.log("hello-test")</script>abc123';
        $page->html = "no escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee($script, false);
        $pageView->assertDontSee('abc123abc123');
    }

    public function test_svg_script_usage_is_removed()
    {
        config()->set('app.content_filtering', 'j');

        $checks = [
            '<svg id="test" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100" height="100"><a xlink:href="javascript:alert(document.domain)"><rect x="0" y="0" width="100" height="100" /></a></svg>',
            '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use xlink:href="data:application/xml;base64 ,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4KPGRlZnM+CjxjaXJjbGUgaWQ9InRlc3QiIHI9IjAiIGN4PSIwIiBjeT0iMCIgc3R5bGU9ImZpbGw6ICNGMDAiPgo8c2V0IGF0dHJpYnV0ZU5hbWU9ImZpbGwiIGF0dHJpYnV0ZVR5cGU9IkNTUyIgb25iZWdpbj0nYWxlcnQoZG9jdW1lbnQuZG9tYWluKScKb25lbmQ9J2FsZXJ0KCJvbmVuZCIpJyB0bz0iIzAwRiIgYmVnaW49IjBzIiBkdXI9Ijk5OXMiIC8+CjwvY2lyY2xlPgo8L2RlZnM+Cjx1c2UgeGxpbms6aHJlZj0iI3Rlc3QiLz4KPC9zdmc+#test"/></svg>',
            '<svg><animate href=#xss attributeName=href values=javascript:alert(1) /></svg>',
            '<svg><animate href="#xss" attributeName="href" values="a;javascript:alert(1)" /></svg>',
            '<svg><animate href="#xss" attributeName="href" values="a;data:alert(1)" /></svg>',
            '<svg><animate href=#xss attributeName=href from=javascript:alert(1) to=1 /><a id=xss><text x=20 y=20>XSS</text></a>',
            '<svg><set href=#xss attributeName=href from=? to=javascript:alert(1) /><a id=xss><text x=20 y=20>XSS</text></a>',
            '<svg><g><g><g><animate href=#xss attributeName=href values=javascript:alert(1) /></g></g></g></svg>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $html = $this->withHtml($pageView);
            $html->assertElementNotContains('.page-content', 'alert');
            $html->assertElementNotContains('.page-content', 'xlink:href');
            $html->assertElementNotContains('.page-content', 'application/xml');
            $html->assertElementNotContains('.page-content', 'javascript');
        }
    }

    public function test_page_inline_on_attributes_show_with_filters_disabled()
    {
        $this->asEditor();
        $page = $this->entities->page();
        config()->set('app.content_filtering', '');

        $script = '<p onmouseenter="console.log(\'test\')">Hello</p>';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee($script, false);
        $pageView->assertDontSee('<p>Hello</p>', false);
    }

    public function test_non_content_filtering_is_controlled_by_config()
    {
        config()->set('app.content_filtering', '');
        $page = $this->entities->page();
        $html = <<<'HTML'
<style>superbeans!</style>
<template id="template">superbeans!</template>
HTML;
        $page->html = $html;
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl());
        $resp->assertSee('superbeans', false);

        config()->set('app.content_filtering', 'h');

        $resp = $this->asEditor()->get($page->getUrl());
        $resp->assertDontSee('superbeans', false);
    }

    public function test_non_content_filtering()
    {
        config()->set('app.content_filtering', 'h');
        $page = $this->entities->page();
        $html = <<<'HTML'
<style>superbeans!</style>
<p>inbetweenpsection</p>
<link rel="stylesheet" href="https://example.com/superbeans.css">
<meta name="description" content="superbeans!">
<title>superbeans!</title>
<template id="template">superbeans!</template>
HTML;

        $page->html = $html;
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl());
        $resp->assertDontSee('superbeans', false);
        $resp->assertSee('inbetweenpsection', false);
    }

    public function test_allow_list_filtering_is_controlled_by_config()
    {
        config()->set('app.content_filtering', '');
        $page = $this->entities->page();
        $page->html = '<div style="position: absolute; left: 0;color:#00FFEE;">Hello!</div>';
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl());
        $resp->assertSee('style="position: absolute; left: 0;color:#00FFEE;"', false);

        config()->set('app.content_filtering', 'a');
        $resp = $this->get($page->getUrl());
        $resp->assertDontSee('style="position: absolute; left: 0;color:#00FFEE;"', false);
        $resp->assertSee('style="color:#00FFEE;"', false);
    }

    public function test_allow_list_style_filtering()
    {
        $testCasesExpectedByInput = [
            '<div style="position:absolute;left:0;color:#00FFEE;">Hello!</div>' => '<div style="color:#00FFEE;">Hello!</div>',
            '<div style="background:#FF0000;left:0;color:#00FFEE;">Hello!</div>' => '<div style="background:#FF0000;color:#00FFEE;">Hello!</div>',
            '<div style="color:#00FFEE;">Hello!<style>testinghello!</style></div>' => '<div style="color:#00FFEE;">Hello!</div>',
            '<div drawio-diagram="5332" another-attr="cat">Hello!</div>' => '<div drawio-diagram="5332">Hello!</div>',
        ];

        config()->set('app.content_filtering', 'a');
        $page = $this->entities->page();
        $this->asEditor();

        foreach ($testCasesExpectedByInput as $input => $expected) {
            $page->html = $input;
            $page->save();
            $resp = $this->get($page->getUrl());

            $resp->assertSee($expected, false);
        }
    }

    public function test_allow_list_does_not_filter_cases()
    {
        $testCasesExpectedByInput = [
            '<p><a href="https://example.com" target="_blank">New tab linkydoodle</a></p>',
            '<p><a href="https://example.com/user/1" data-mention-user-id="5">@mentionusertext</a></p>',
            '<details><summary>Hello</summary><p>Mydetailshere</p></details>',
        ];

        config()->set('app.content_filtering', 'a');
        $page = $this->entities->page();
        $this->asEditor();

        foreach ($testCasesExpectedByInput as $input) {
            $page->html = $input;
            $page->save();
            $resp = $this->get($page->getUrl());

            $resp->assertSee($input, false);
        }
    }
}
