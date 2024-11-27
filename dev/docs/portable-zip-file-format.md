# Portable ZIP File Format

BookStack provides exports in a "Portable ZIP" which allows the portable transfer, storage, import & export of BookStack content.
This document details the format used, and is intended for our own internal development use in addition to detailing the format for potential external use-cases (readers, apps, import for other platforms etc...).

**Note:** This is not a BookStack backup format! This format misses much of the data that would be needed to re-create/restore a BookStack instance. There are existing better alternative options for this use-case.

## Stability

Following the goals & ideals of BookStack, stability is very important. We aim for this defined format to be stable and forwards compatible, to prevent breakages in use-case due to changes. Here are the general rules we follow in regard to stability & changes:

- New features & properties may be added with any release.
- Where reasonably possible, we will attempt to avoid modifications/removals of existing features/properties.
- Where potentially breaking changes do have to be made, these will be noted in BookStack release/update notes.

The addition of new features/properties alone are not considered as a breaking change to the format. Breaking changes are considered as such where they could impact common/expected use of the existing properties and features we document, they are not considered based upon user assumptions or any possible breakage.
For example if your application, using the format, breaks because we added a new property while you hard-coded your application to use the third property (instead of a property name), then that's on you.

## Format Outline

The format is intended to be very simple, readable and based on open standards that could be easily read/handled in most common programming languages.
The below outlines the structure of the format:

- **ZIP archive container**
   - **data.json** - Export data.
   - **files/** - Directory containing referenced files.
     - *file-a*
     - *file-b*
     - *...*

## References

Some properties in the export data JSON are indicated as `String reference`, and these are direct references to a file name within the `files/` directory of the ZIP. For example, the below book cover is directly referencing a `files/4a5m4a.jpg` within the ZIP which would be expected to exist.

```json
{
  "book": {
    "cover": "4a5m4a.jpg"
  }
}
```

Within HTML and markdown content, you may require references across to other items within the export content.
This can be done using the following format:

```
[[bsexport:<object>:<reference>]]
```

References are to the `id` for data objects.
Here's an example of each type of such reference that could be used:

```
[[bsexport:image:22]]
[[bsexport:attachment:55]]
[[bsexport:page:40]]
[[bsexport:chapter:2]]
[[bsexport:book:8]]
```

## HTML & Markdown Content

BookStack commonly stores & utilises content in the HTML format.
Properties that expect or provided HTML will either be named `html` or contain `html` in the property name.
While BookStack supports a range of HTML, not all HTML content will be supported by BookStack and be assured to work as desired across all BookStack features.
The HTML supported by BookStack is not yet formally documented, but you can inspect to what the WYSIWYG editor produces as a basis.
Generally, top-level elements should keep to common block formats (p, blockquote, h1, h2 etc...) with no nesting or custom structure apart from common inline elements.
Some areas of BookStack where HTML is used, like book & chapter descriptions, will strictly limit/filter HTML tag & attributes to an allow-list.

For markdown content, in BookStack we target [the commonmark spec](https://commonmark.org/) with the addition of tables & task-lists.
HTML within markdown is supported but not all HTML is assured to work as advised above.

### Content Security

If you're consuming HTML or markdown within an export please consider that the content is not assured to be safe, even if provided directly by a BookStack instance. It's best to treat such content as potentially unsafe.
By default, BookStack performs some basic filtering to remove scripts among other potentially dangerous elements but this is not foolproof. BookStack itself relies on additional security mechanisms such as [CSP](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP) to help prevent a range of exploits.

## Export Data - `data.json`

The `data.json` file is a JSON format file which contains all structured data for the export. The properties are as follows:

- `instance` - [Instance](#instance) Object, optional, details of the export source instance.
- `exported_at` - String, optional, full ISO 8601 datetime of when the export was created.
- `book` - [Book](#book) Object, optional, book export data.
- `chapter` - [Chapter](#chapter) Object, optional, chapter export data.
- `page` - [Page](#page) Object, optional, page export data.

Either `book`, `chapter` or `page` will exist depending on export type. You'd want to check for each to check what kind of export this is, and if it's an export you can handle. It's possible that other options are added in the future (`books` for a range of books for example) so it'd be wise to specifically check for properties that can be handled, otherwise error to indicate lack of support.

## Data Objects

The below details the objects & their properties used in Application Data.

#### Instance

These details are informational regarding the exporting BookStack instance from where an export was created from.

- `id` - String, required, unique identifier for the BookStack instance.
- `version` - String, required, BookStack version of the export source instance.

#### Book

- `id` - Number, optional, original ID for the book from exported system.
- `name` - String, required, name/title of the book.
- `description_html` - String, optional, HTML description content.
- `cover` - String reference, optional, reference to book cover image.
- `chapters` - [Chapter](#chapter) array, optional, chapters within this book.
- `pages` - [Page](#page) array, optional, direct child pages for this book.
- `tags` - [Tag](#tag) array, optional, tags assigned to this book.

The `pages` are not all pages within the book, just those that are direct children (not in a chapter). To build an ordered mixed chapter/page list for the book, as what you'd see in BookStack, you'd need to combine `chapters` and `pages` together and sort by their `priority` value (low to high).

#### Chapter

- `id` - Number, optional, original ID for the chapter from exported system.
- `name` - String, required, name/title of the chapter.
- `description_html` - String, optional, HTML description content.
- `priority` - Number, optional, integer order for when shown within a book (shown low to high).
- `pages` - [Page](#page) array, optional, pages within this chapter.
- `tags` - [Tag](#tag) array, optional, tags assigned to this chapter.

#### Page

- `id` - Number, optional, original ID for the page from exported system.
- `name` - String, required, name/title of the page.
- `html` - String, optional, page HTML content.
- `markdown` - String, optional, user markdown content for this page.
- `priority` - Number, optional, integer order for when shown within a book (shown low to high).
- `attachments` - [Attachment](#attachment) array, optional, attachments uploaded to this page.
- `images` - [Image](#image) array, optional, images used in this page.
- `tags` - [Tag](#tag) array, optional, tags assigned to this page.

To define the page content, either `markdown` or `html` should be provided. Ideally these should be limited to the range of markdown and HTML which BookStack supports. See the ["HTML & Markdown Content"](#html--markdown-content) section.

The page editor type, and edit content will be determined by what content is provided. If non-empty `markdown` is provided, the page will be assumed as a markdown editor page (where permissions allow) and the HTML will be rendered from the markdown content. Otherwise, the provided `html` will be used as editor & display content.

#### Image

- `id` - Number, optional, original ID for the page from exported system.
- `name` - String, required, name of image.
- `file` - String reference, required, reference to image file.
- `type` - String, required, must be 'gallery' or 'drawio'

File must be an image type accepted by BookStack (png, jpg, gif, webp).
Images of type 'drawio' are expected to be png with draw.io drawing data
embedded within it.

#### Attachment

- `id` - Number, optional, original ID for the attachment from exported system.
- `name` - String, required, name of attachment.
- `link` - String, semi-optional, URL of attachment.
- `file` - String reference, semi-optional, reference to attachment file.

Either `link` or `file` must be present, as that will determine the type of attachment. 

#### Tag

- `name` - String, required, name of the tag.
- `value` - String, optional, value of the tag (can be empty).