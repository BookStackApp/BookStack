

### Srcdoc usage

By default, as of tiny 6, the editor would use srcdoc which prevents cookies being sent with images in Firefox as 
it's considered cross origin. This removes that usage to work around this case:

[Relevant TinyMCE issue](https://github.com/tinymce/tinymce/issues/7746).

Source code change applied:

```javascript
// Find:
t.srcdoc=e.iframeHTML
// Replace:
t.contentDocument.open();t.contentDocument.write(e.iframeHTML);t.contentDocument.close();
```