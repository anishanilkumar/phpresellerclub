# Security Policy

## Supported Versions

Security fixes are applied to the latest released minor version. Because this
library talks to a live billing and domain-management API, always run a
supported release.

| Version | Supported          |
| ------- | ------------------ |
| Latest `main` / newest release | :white_check_mark: |
| Older releases | :x: |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues,
pull requests, or discussions.**

Instead, report them privately using one of the following:

1. **GitHub Security Advisories (preferred)** — open a private report from the
   repository's **Security → Report a vulnerability** tab
   (`https://github.com/anishanilkumar/phpresellerclub/security/advisories/new`).
2. **Email** — `aneesh.nl@gmail.com`. Please put `SECURITY` in the subject line.

To help triage quickly, include as much of the following as you can:

- A description of the vulnerability and its impact.
- The affected file(s), class, or method, and the version/commit you tested.
- Step-by-step reproduction instructions or a proof-of-concept.
- Any suggested remediation.

Please **do not** include real ResellerClub credentials, API keys, customer
data, or other secrets in your report; redact them or use placeholders.

## What to Expect

- **Acknowledgement:** within 5 business days.
- **Assessment & triage:** we will confirm the issue, determine severity, and
  keep you informed of progress.
- **Fix & disclosure:** once a fix is ready we will release it and publish a
  security advisory. With your permission we are happy to credit you.

We ask that you give us a reasonable opportunity to release a fix before any
public disclosure (coordinated disclosure).

## Scope

In scope:

- Vulnerabilities in this library's source code (`src/`) — for example,
  credential leakage, insecure transport, request tampering, injection into
  API requests, or unsafe handling of API responses.

Out of scope:

- Vulnerabilities in the ResellerClub / LogicBoxes API itself — report those to
  ResellerClub.
- Vulnerabilities in third-party dependencies — report those upstream (we will,
  however, upgrade promptly once a fix is available).
- Issues that require a misconfigured or already-compromised host.

## Security Considerations for Users

- **Never hardcode or commit your API credentials.** Load `Credentials` from
  environment variables or a secrets manager, and keep any local config file
  (e.g. `rc-config.php`) out of version control.
- **Keep TLS verification enabled.** This library sends every request over
  HTTPS with certificate verification on by default. Do not disable peer/host
  verification on the HTTP client you inject.
- **Restrict API access** to whitelisted IPs in your ResellerClub account where
  possible, and rotate keys periodically.
- Keep this package and its dependencies up to date (e.g. via Dependabot).
