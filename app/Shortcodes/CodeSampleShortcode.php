namespace App\Shortcodes;

class CodeSampleShortcode {

  public function register($shortcode, $content, $compiler, $name)
  {
    error_log("Running shortcode....");
    return sprintf('<strong class="%s">%s</strong>', $shortcode->class, $content);
  }

}
