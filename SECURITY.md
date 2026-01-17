# Security Policy

## Reporting Security Vulnerabilities

If you discover a security vulnerability in Lara-Veil, please email security@scapteinc.com instead of using the issue tracker.

**Please do not disclose the vulnerability publicly until a fix has been released.**

When reporting a vulnerability, please include:

- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact
- Suggested fix (if any)

## Security Response Timeline

- **48 hours**: Initial acknowledgment
- **7 days**: Initial fix or workaround
- **30 days**: Release of security patch

## Security Practices

### For Users

- Keep Lara-Veil and its dependencies updated
- Validate all user input in your plugins and themes
- Use prepared statements for database queries
- Implement proper authorization checks
- Sanitize output data
- Enable HTTPS on production sites

### For Contributors

- Never commit secrets or API keys
- Follow secure coding practices
- Review security implications of changes
- Update dependencies regularly
- Report any security issues responsibly

## Supported Versions

| Version | Laravel | PHP  | Security Fixes Until |
|---------|---------|------|----------------------|
| 2.0.x   | 10.x    | 8.3+ | January 2026         |
| 1.1.x   | 10.x    | 8.2+ | June 2025            |
| 1.0.x   | 9.x     | 8.1+ | December 2024        |

## Dependencies

This package requires:
- **PHP**: ^8.1
- **Laravel**: ^9.0|^10.0|^11.0
- **intervention/image**: ^3.0

Keep these dependencies updated to receive security patches.
