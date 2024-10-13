# Portable ZIP File Format

BookStack provides exports in a "Portable ZIP" which allows the portable transfer, storage, import & export of BookStack content.
This document details the format used, and is intended for our own internal development use in addition to detailing the format for potential external use-cases (readers, apps, import for other platforms etc...).

**Note:** This is not a BookStack backup format! This format misses much of the data that would be needed to re-create/restore a BookStack instance. There are existing better alternative options for this use-case.

## Stability

Following the goals & ideals of BookStack, stability is very important. We aim for this defined format to be stable and forwards compatible, to prevent breakages in use-case due to changes. Here are the general rules we follow in regard to stability & changes:

- New features & properties may be added with any release.
- Where reasonably possible, we will attempt to avoid modifications/removals of existing features/properties.
- Where potentially breaking changes do have to be made, these will be noted in BookStack release/update notes.

The addition of new features/properties alone are not considered as a breaking change to the format. Breaking changes are considered as such where they could impact common/expected use of the existing properties and features we document, they are not considered based upon user assumptions or any possible breakage. For example if your application, using the format, breaks because we added a new property while you hard-coded your application to use the third property (instead of a property name), then that's on you.

## Format Outline

The format is intended to be very simple, readable and based on open standards that could be easily read/handled in most common programming languages.
The below outlines the structure of the format:

- **ZIP archive container**
   - **data.json** - Application data.
   - **files/** - Directory containing referenced files.
     -  *...file.ext*

## References

TODO - Define how we reference across content:
TODO - References to files from data.json
TODO - References from in-content to file URLs
TODO - References from in-content to in-export content (page cross links within same export).

## Application Data - `data.json`

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

These details are mainly informational regarding the exporting BookStack instance from where an export was created from.

- `version` - String, required, BookStack version of the export source instance.
- `id_ciphertext` - String, required, identifier for the BookStack instance.

The `id_ciphertext` is the ciphertext of encrypting the text `bookstack`. This is used as a simple & rough way for a BookStack instance to be able to identify if they were the source (by attempting to decrypt the ciphertext).

#### Book

- `id` - Number, optional, original ID for the book from exported system.
- `name` - String, required, name/title of the book.
- `description_html` - String, optional, HTML description content.
- `chapters` - [Chapter](#chapter) array, optional, chapters within this book.
- `pages` - [Page](#page) array, optional, direct child pages for this book.
- `tags` - [Tag](#tag) array, optional, tags assigned to this book.

The `pages` are not all pages within the book, just those that are direct children (not in a chapter). To build an ordered mixed chapter/page list for the book, as what you'd see in BookStack, you'd need to combine `chapters` and `pages` together and sort by their `priority` value (low to high).

#### Chapter

- `id` - Number, optional, original ID for the chapter from exported system.
- `name` - String, required, name/title of the chapter.
- `description_html` - String, optional, HTML description content.
- `pages` - [Page](#page) array, optional, pages within this chapter.
- `tags` - [Tag](#tag) array, optional, tags assigned to this chapter.

#### Page

TODO

#### Image

TODO

#### Attachment

TODO

#### Tag

- `name` - String, required, name of the tag.
- `value` - String, optional, value of the tag (can be empty).
- `order` - Number, optional, integer order for the tags (shown low to high).