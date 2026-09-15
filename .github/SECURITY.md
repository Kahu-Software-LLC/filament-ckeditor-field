# Security Policy

## Supported Versions

Two release lines are maintained in parallel, one per major Filament version:

| Version | Filament | Supported          |
| ------- | -------- | ------------------ |
| 2.x     | 4.x      | :white_check_mark: |
| 1.x     | 3.x      | :white_check_mark: |
| 0.x     | 3.x      | :x:                |

Fixes land on both supported lines. Always upgrade within your line rather than
staying on an older tag.

## Reporting a Vulnerability

If you discover a security vulnerability, please **do not** open a public issue. Instead, please email the security team directly at [hello@kahusoftware.com](mailto:hello@kahusoftware.com).

Please include the following information in your report:

- A description of the vulnerability
- Steps to reproduce the vulnerability
- The potential impact of the vulnerability
- Any suggested fixes or mitigations

## Response Timeline

- **Initial Response**: Within 48 hours
- **Status Update**: Within 7 days
- **Fix Release**: Depends on severity, but typically within 30 days

## How Fixes Are Published

Security fixes are published as [GitHub Security Advisories](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/security/advisories) on this repository. Publishing there puts them in the GitHub Advisory Database, which is what makes `composer audit` and Dependabot flag an affected version for you.

[GHSA-q9fx-9x5r-pfmf](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/security/advisories/GHSA-q9fx-9x5r-pfmf) is the worked example: the bundled CKEditor build carried an upstream XSS, and the advisory is what told affected applications about it.

## A Note on the Bundled CKEditor Build

This package **vendors a compiled CKEditor 5 build into `resources/dist/`**. CKEditor is not a declared dependency and never appears in your `composer.lock` or `package-lock.json`.

The consequence is worth stating plainly: a CKEditor security advisory can reach your application through this package even though CKEditor is nowhere in your own dependency tree, and your own tooling will not see it. That is why advisories are published against this package rather than left to the upstream one, and it is the reason to treat a release of this package that mentions the bundled build as security-relevant.

## Security Best Practices

When using this package, please follow these security best practices:

1. **Keep dependencies updated**: Regularly update this package and its dependencies
2. **Validate user input**: Always validate and sanitize content from CKEditor fields
3. **Use HTTPS**: Ensure your application uses HTTPS in production
4. **Content Security Policy**: Implement appropriate CSP headers for your application
5. **File uploads**: If using image uploads, validate file types and sizes server-side
6. **Review the General HTML Support config**: The shipped default allows every element, attribute, style and class. If your content does not need that, narrow `htmlSupport.allow` in the published config.

## Security Updates

Security updates are released on both supported lines and are documented under a `### Security` heading in the [CHANGELOG](../CHANGELOG.md), alongside the advisory they close. They ship in whichever version number the change warrants, which is not always a patch: a fix bundled with a new option is released as a minor.

Thank you for helping keep this package and its users safe!
