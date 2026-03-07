# Changelog

All notable changes to this project will be documented in this file.  
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [1.1.0] – 2026-03-07

### Security
- Added **server-side reCAPTCHA verification** via `siteverify` API — previously the CAPTCHA was front-end only and could be bypassed by posting directly to the AJAX handler
- Added **rate limiting** (5 submissions / minute per IP) to prevent form flooding / DoS
- Backend controllers (`Contacts`, `Subscribers`) now enforce `$requiredPermissions` — unauthenticated or underprivileged users can no longer access contact records

### Added
- `registerPermissions()` in `Plugin.php` formally declares `moonwalkerz.contact.access_contacts` and `moonwalkerz.contact.manage_settings`
- `google_secret_key` setting field (required for server-side reCAPTCHA); lang keys added for `en` and `it`
- Model-level validation rules on `Contact` (name, email, message, phone length limits)

### Changed
- **OctoberCMS 4.x compatibility**: removed deprecated `Input` facade, `MailManager::registerCallback()`, string-based `$implement` arrays and global facade aliases — replaced with `post()`, `registerMailTemplates()`, `::class` constants and fully-qualified namespace imports
- GDPR consent validation changed from `required` to `accepted` — correctly rejects an unchecked checkbox (applies to both `ContactForm` and `NewsletterForm`)
- Input validation now enforces max lengths: `name` 100, `email` 191, `message` 5000, `phone` 30
- Email input in the component template changed to `type="email"`
- `composer.json` PHP requirement raised to `>=8.1`; `rainlab/translate-plugin` bumped to `>=2.0`

### Fixed
- Migration `up()` no longer calls `Schema::dropIfExists()` before creating the table — prevents accidental data loss on re-run
- Removed unused `public $l` property from `ContactForm`
- Removed stale comment about `Mail\Message` cast exception (resolved by extracting variables before the closure)

---

## [1.0.9] – 2025

### Changed
- Initial OctoberCMS 4.x compatibility pass

---

## [1.0.6] – 2024

### Added
- Created `moonwalkerz_contact_subscribers` table
- Newsletter form component

---

## [1.0.5] – 2024

### Added
- Newsletter form additions and fixes

---

## [1.0.4] – 2024

### Added
- Google reCAPTCHA support (front-end)

---

## [1.0.3] – 2024

### Changed
- Refactored namespace to `moonwalkerz`

---

## [1.0.2] – 2024

### Added
- Created `moonwalkerz_contact_contacts` table migration

---

## [1.0.1] – 2024

### Added
- Initial plugin release
