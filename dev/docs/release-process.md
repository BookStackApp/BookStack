# Release Versioning & Process

### BookStack Version Number Scheme

BookStack releases are each assigned a date-based version number in the format `v<year>.<month>[.<optional_patch_number>]`. For example:

- `v20.12` - New feature released launched during December 2020.
- `v21.06.2` - Second patch release upon the June 2021 feature release.

Patch releases are generally fairly minor, primarily intended for fixes and therefore are fairly unlikely to cause breakages upon update.
Feature releases are generally larger, bringing new features in addition to fixes and enhancements. These releases have a greater chance of introducing breaking changes upon update, so it's worth checking for any notes in the [update guide](https://www.bookstackapp.com/docs/admin/updates/).

### Release Planning Process

Each BookStack release will have a [milestone](https://github.com/BookStackApp/BookStack/milestones) created with issues & pull requests assigned to it to define what will be in that release. Milestones are built up then worked through until complete at which point, after some testing and documentation updates, the release will be deployed.

### Release Announcements

Feature releases, and some patch releases, will be accompanied by a post on the [BookStack blog](https://www.bookstackapp.com/blog/) which will provide additional detail on features, changes & updates otherwise the [GitHub release page](https://github.com/BookStackApp/BookStack/releases) will show a list of changes. You can sign up to be alerted to new BookStack blog posts (once per week maximum) [at this link](https://updates.bookstackapp.com/signup/bookstack-news-and-updates).

### Release Technical Process

Deploying a release, at a high level, simply involves merging the development branch into the release branch before then building & committing any release-only assets.
A helper script [can be found in our](https://github.com/BookStackApp/devops/blob/main/meta-scripts/bookstack-release-steps) devops repo which provides the steps and commands for deploying a new release. 