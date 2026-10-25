# PHP Security Check

**A single-file, read-only PHP advisory checker by [Murali Sugumar](https://muralisugumar.com).**

Upload one PHP file, open it through your website, and see whether the PHP serving that request meets the published security-fix thresholds. Includes clear warnings, an affected-version table, issue descriptions, and a report to copy to your hosting provider.

**No Composer, database, WordPress plugin, CMS installation, root access or SSH is required to run the browser check.**

> Advisory snapshot: **25 October 2026**. This is an independent version/configuration checker, not an exploit scanner, patch installer, live vulnerability feed or security certificate.

## Why this tool exists

The PHP project published emergency security updates on **21 October 2026** for two vulnerabilities that could allow unauthenticated remote code execution. A follow-up concerning session upload progress was published on **23 October**. See the [official PHP announcements](https://www.php.net/archive/2026.php#2026-10-21-5).

A website owner may know that their hosting uses “PHP 8.3” without knowing the full patch version, or whether the website uses the same PHP installation as the terminal. This tool makes that first check easier and provides a practical report for the hosting team.

WordPress, a custom PHP CMS, a framework or a plain PHP site can use the same checker. Updating application code alone does not establish that the server's PHP runtime has been patched.

## What it checks

| Check | What you get |
|---|---|
| PHP version | The actual `PHP_VERSION` executing this file |
| Runtime handler | The observed `PHP_SAPI`, such as LiteSpeed or PHP-FPM |
| October 21 patch thresholds | A branch-specific comparison against the published minimum fixes |
| Session upload progress | The setting observed in this request, with follow-up guidance |
| Advisory age | A reminder to refresh the snapshot after 24 November 2026 |

The report provides an action warning for versions below the listed thresholds, highlights the current branch, and includes a support message you can select and copy. Its layout supports mobile screens and printing.

## The issues covered

| Issue | Why it matters | Guidance |
|---|---|---|
| [CVE-2026-109618](https://github.com/php/php-src/security/advisories/GHSA-w8hw-w9fx-j79w) | Out-of-memory corruption can lead to unauthenticated remote code execution. | Install the relevant PHP fix, or verify a vendor backport. |
| [CVE-2026-109619](https://github.com/php/php-src/security/advisories/GHSA-6wmf-4rv5-p3rp) | Stale request-related global state can lead to remote code execution. | Install the relevant PHP fix, or verify a vendor backport. |
| [October 23 follow-up: CVE-2026-115560](https://www.php.net/archive/2026.php) | Session upload-progress behavior depends on an unusual configuration and application interaction. | Have the provider review startup configuration. The checker cannot establish exploitability; this snapshot lists no fixed release for the follow-up. |

### Minimum fixed upstream versions for the two October 21 issues

| PHP branch | Affected upstream releases | Minimum fixed version |
|---|---|---|
| 8.2 | Before 8.2.35 | **8.2.35** |
| 8.3 | Before 8.3.36 | **8.3.36** |
| 8.4 | Before 8.4.27 | **8.4.27** |
| 8.5 | Before 8.5.12 | **8.5.12** |

These are **minimum historical fix levels**, not a promise that they remain the latest secure releases. Prefer the latest appropriate patched production release. Older branches need a supported upgrade or explicit vendor-backport confirmation; unknown and prerelease branches are not automatically marked as patched. See [PHP's supported versions](https://www.php.net/supported-versions.php).

## How to use it — shared hosting, Hostinger or DirectAdmin

1. Download the raw [`php-runtime-check.php`](php-runtime-check.php) file from this repository. Do not save a GitHub HTML page as PHP.
2. Open your hosting **File Manager** or use SFTP.
3. Upload **only that file** into the website's document root, commonly `public_html`, alongside its `index.php`. Do not overwrite `index.php`, your CMS files, or `.htaccess`.
4. Open it over HTTPS:

   ```text
   https://yourdomain.com/php-runtime-check.php
   ```

5. Review the warning, issue table and update targets. Copy the hosting-support report if anything needs attention.
6. After your provider applies changes, reload the checker and ask them to confirm all workers and domains are patched.
7. **Delete the checker from the public website when finished**, or protect it with hosting-level access control if you retain it.

Repeat this for each domain or subdomain you manage. Sites on the same account can use different PHP versions. There is no need to enter database details, passwords or API keys into this tool.

## How to interpret the result

| Result | Meaning | Next step |
|---|---|---|
| **Action required** | The version is below the upstream fixed level or belongs to an older unsupported upstream branch. Vendor backports cannot be detected. | Request an update or written backport confirmation for the exact build. |
| **Version check passed** | The version meets the two October 21 fix thresholds, and no additional review condition was raised by the current checks. | Keep PHP updated; this does not certify the whole site or server. |
| **Review required** | The branch is unknown/prerelease, the configuration needs review, or the snapshot has aged. | Read the explanation and check the current vendor advisory. |

**Upload progress “On” does not prove that the follow-up is exploitable.** Its conditions involve application behavior this standalone script does not inspect. Where PHP recommends disabling it, the provider must apply the setting before request parsing and restart workers. Do not rely on adding `ini_set()` to the site or on a PHP-FPM `.user.ini`. See the [official guidance](https://www.php.net/archive/2026.php).

Do not treat a WAF, HTTPS, a security plugin or an application update as a substitute for the PHP runtime patch.

## What to ask your hosting provider

Use the generated report, which includes the observed PHP version and handler. Ask the provider to confirm:

- Both October 21 fixes are present in the exact PHP build serving the domain, including any vendor backports.
- Affected PHP workers were restarted and other installed web PHP versions were checked.
- The October 23 follow-up was reviewed and any relevant setting applies before request parsing.

DirectAdmin is a control panel; the hosting operator or VPS administrator is responsible for the underlying PHP installation. Updating PHP is outside this tool's permissions and scope.

## Privacy and limitations

The checker makes **no outgoing requests**, uses no analytics, loads no third-party fonts/scripts, and reads no CMS or WordPress configuration. It does not start sessions, query databases, write files, change settings or try to exploit vulnerabilities. It does not display `phpinfo()`, environment variables, credentials or private server paths.

The public page does disclose a limited PHP runtime report to anyone who knows its URL. Response headers discourage caching, indexing and framing, but **do not provide authentication**. Remove it after use or restrict access at the host.

It cannot confirm:

- Binary patches or vendor backports hidden behind an older version number.
- Every worker, server or load-balancer node from one request.
- WordPress core/plugin/theme security or custom application security.
- Whether a site was previously compromised.
- Vulnerabilities outside its dated advisory catalog.

Its green result must never be presented as “this server is completely safe.” It only applies to the version-based checks explained on the page.

## Requirements and local testing

The file needs a working PHP runtime and an ordinary browser. It has no package dependencies. Rendering and classification were tested with PHP **8.5.5**; results for other version strings are covered by deterministic boundary checks. This is not a claim of execution testing across every historical PHP build. PHP 8.5.5 itself is below the advisory's patched level.

For local development:

```sh
php -S 127.0.0.1:8080
```

Open `http://127.0.0.1:8080/php-runtime-check.php`. Keep the development server bound to loopback.

To run the included checks:

```sh
php -l php-runtime-check.php
php tests/check.php
```

Running `php php-runtime-check.php` prints HTML for **command-line PHP**, which may differ from the web runtime. Use the browser URL to evaluate a hosted website.

## Keeping the advisory catalog accurate

This project does **not** fetch updates automatically. Before changing a threshold:

1. Verify the current PHP/vendor advisory and its affected/fixed versions.
2. Update the catalog, explanations, snapshot date and stale-snapshot reminder in the PHP file together.
3. Update this README and boundary tests.
4. Exercise warning, patched and review layouts on desktop and mobile.

Do not infer a fixed release for a separate issue from another issue's patch threshold.

## Author and credits

Created and maintained by **Murali Sugumar**.

- Website: **[muralisugumar.com](https://muralisugumar.com)**
- GitHub: **[muralisugumar](https://github.com/muralisugumar)**

Security findings and patch guidance belong to the PHP project and the advisory authors linked above. This is an independent diagnostic interface and is not an official PHP product or endorsement.

© 2026 Murali Sugumar.
