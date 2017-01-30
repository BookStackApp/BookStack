<?php

namespace BookStack\Providers;

use Illuminate\Support\ServiceProvider;
use \Webwizo\Shortcodes\Facades;
      define('MEMCACHED_HOST', '127.0.0.1');
      define('MEMCACHED_PORT', '11211');

class ShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        \Webwizo\Shortcodes\Facades\Shortcode::register('codesample', function ($shortcode, $content) {
          $tabCounter = 0;
 	  $body = '';
	  $header = '<ul class="nav nav-tabs" role="tablist">';
            	foreach($shortcode->toArray() as $attr) {
			$activeTab = ($tabCounter == 0) ? 'active' : '';
			list($lang, $source) = explode(':', $attr, 2);
			preg_match("/(.*)\((.*)\)/", $lang, $matches); 
			var_dump($matches);
		        if (count($matches) > 1) {
				$lang = $matches[1];
				$titleLang = $matches[2];
			} else {
				$titleLang = strtoupper($lang);
			}
			$srcData = $this->loadRemote($source);
			$header .= "<li role='presentation' class='$activeTab'><a href='#$lang' aria-controls='$lang' role='tab' data-toggle='tab'>$titleLang</a></li>";
			$body .= "<div class='tab-pane $activeTab' role='tabpanel' id='$lang'><pre><code class=\"$lang\">" . htmlentities($srcData) . "</code></pre></div>";
                        $tabCounter++;
		}

		// close header
		$header .= "</ul>";
		return "<div>" . $header . "<div class='tab-content'>" .  $body . "</div></div>";

        }); 
    }

    private function loadRemote($url) {
      // Connection constants
 
      // Connection creation
      $memcache = new \Memcache;
      $cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);

      if ($cacheAvailable) {
        $cached = $memcache->get($url);
        if (!$cached) {
          $srcData = @file_get_contents($url);
          if ($srcData === false) { $srcData = "Error: '$url' does not appear to be a valid URL."; }
          else {
            $memcache->set($url, $srcData);
          }

          $cached = $srcData;
        }
        return $cached;
      }

      $srcData = @file_get_contents($source);
      if ($srcData === false) { $srcData = "Error: '$source' does not appear to be a valid URL."; }
      return $srcData;
    }
}
