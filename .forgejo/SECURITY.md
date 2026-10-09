# Security Policy

## Supported Versions

Only the [latest version](https://codeberg.org/bookstack/bookstack/releases) of BookStack is supported.
We generally don't support older versions of BookStack due to maintenance effort and
since we aim to provide a fairly stable upgrade path for new versions.

## Security Notifications

If you'd like to be notified of new potential security concerns, you can [sign-up to the BookStack security mailing list](https://updates.bookstackapp.com/signup/bookstack-security-updates).

## Reporting a Vulnerability

If you've found an issue that likely has no impact on existing users (for example, an issue only in the development branch)
feel free to raise it via a standard Codeberg bug report issue.

If the issue could have a security impact on BookStack instances, 
please directly contact the lead maintainer, Dan Brown, via email using the [details found here](https://www.bookstackapp.com/links/contact/).

When contacting us, please note any names (and optionally any profile/company/website links) that you'd like to be used in
any attribution within our release notes and content.

Please be patient while the vulnerability is being reviewed. Deploying the fix to address the vulnerability
can often take a little time due to the amount of preparation required to ensure the vulnerability has
been covered and to create the content required to adequately notify the user-base.

Thank you for keeping BookStack instances safe!

### CVE Creation

We're generally happy for (and prefer) researchers to raise CVEs for issues they've discovered.
We ask that you first confirm with us to ensure the vulnerability is valid and that it hasn't yet been discovered
and reported by someone else. 

We can raise CVEs ourselves, but we would only go to the effort for security issues with a significant level of risk to users.
We typically won't pursue CVEs if there's a lesser level of risk.

### Our Announcement Channels

When security issues meet a reasonable level of risk to users, they will be assigned to be addressed via a BookStack "Security Release". When made available, these releases will be announced via our [security mailing list](https://updates.bookstackapp.com/signup/bookstack-security-updates), as a post on [our blog](https://www.bookstackapp.com/blog/), and security notices will be added to our [updates documentation page](https://www.bookstackapp.com/docs/admin/updates/#version-specific-instructions). These releases will also be typically announced in some of our social communities. 

To meet the "reasonable level of risk" for a security release, the issue(s) will typically need to pose a risk to instance/user data and security. Security *improvements* (for example, hardening against denial of service attacks) typically won't trigger a security release by themselves, and may be released as a standard patch release.

CVEs may also be created for security issues, but this is not assured. We only seek CVEs for high-risk issues, although we're very liberal in allowing security researchers to report lesser issues also.

### CRA Reporting

In line with the [Cyber Resilience Act](https://digital-strategy.ec.europa.eu/en/library/cyber-resilience-act), we aim to meet certain levels of reporting requirements by reporting the following to the relevant ENISA established single reporting platform:

- Actively exploited vulnerabilities contained in BookStack, when made aware of such exploit use.
- Severe incidents having an impact on the security of the product (for example, compromised BookStack infrastructure).


