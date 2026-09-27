<p align="center"> <img style="max-width: 100%; margin: 2rem auto; display: block;" src="https://raw.githubusercontent.com/moonwalkerz/oc-contact-form/master/cover_github.jpg"></p>

# Contact Form | October CMS

MoonWalkerz present "Contact Form"! A simple and convenient contact form  plugin for October CMS.
this plugin allows the saving of email and telephone number, you can active captcha and is GDPR compliant!
What more do you want?

## 🔥 Features 🔥

- Fully manageable from backend.
- Saving of email and telephone number for marketing purposes.
- GDPR compliant and third part privacy rules.
- **Agenda**: a backend address book (name, email, phone, city, country, consents) with CSV import/export.
- **Multi-provider captcha**, selectable from the backend:
  - Google reCAPTCHA v2 (checkbox / invisible) [More Info](https://developers.google.com/recaptcha/docs/display)
  - Google reCAPTCHA v3 (score threshold, action check) [More Info](https://developers.google.com/recaptcha/docs/v3)
  - Google reCAPTCHA Enterprise [More Info](https://cloud.google.com/recaptcha/docs)
  - hCaptcha [More Info](https://docs.hcaptcha.com/)
  - Cloudflare Turnstile [More Info](https://developers.cloudflare.com/turnstile/)
  - Built-in math captcha (no keys needed)
- Server-side captcha verification, with single-use tokens.
- Anti-spam extras that work with any provider (or with none): honeypot field, minimum fill time, optional hostname verification.
- Rate limiting (5 submissions per minute per IP).
- Backend permissions for contacts, agenda and settings.
- Multilanguage support via Rainlab.Translate plugin (English and Italian included).

## 💊 Dependencies 💊

this plugin needs the following dependencies:
- October CMS 4.x, PHP >= 8.1
- Rainlab.Translate

you can install them with this command:
```
php artisan plugin:install RainLab.Translate
```

## 🚀 Install 🚀
You can install this plugin with this command:

```
composer require moonwalkerz/contact-plugin
php artisan october:migrate
```

## ⚙️ Documentation ⚙️

Using this plugin is really simple. Before using it, you need to follow these steps:

1) Set up the email settings in October CMS backend, under Settings > Mail Configuration.
2) Set up the Contacts settings in October CMS backend, under Settings > Contacts. In this section, you can insert the company name, which will be used to autocomplete the GDPR checkbox.
3) Choose a captcha provider in Settings > Contacts > Captcha and fill in its keys. Each provider shows the instructions and the links to get the keys. If you don't want to use an external service, pick the math captcha.

   Keys can also come from the environment: a setting left empty falls back to `{PROVIDER}_SITE_KEY` / `{PROVIDER}_SECRET_KEY` (for example `HCAPTCHA_SECRET_KEY`); Turnstile also reads Cloudflare's canonical `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET`. This keeps the secret out of the database and lets deployment tools such as Cloudflare's Turnstile Spin write it to `.env`.

   Turnstile tokens are verified server side against `https://challenges.cloudflare.com/turnstile/v0/siteverify` and must carry the form's action (`contact_form` or `newsletter_form`, from the component class name; override `captchaAction()` in a component to change it). Enable "Verify hostname" to also require the token to have been solved on the same host that receives the submission.

Now, you can insert the `contactform` component in your page. You can set additional custom settings, such as:
- Email destination address
- Name of email sender
- Email subject
- add phone field
- set phone field mandatory
- GDPR checkbox

This plugin creates a new default mail template. If you need to customize it, you can find it in Settings > Mail Templates > moonwalkerz.contact::mail.message

### Custom templates

If you override the component template in your theme, print the captcha and its error placeholder inside the form:

```twig
{{ __SELF__.captchaField()|raw }}
<div class="text-danger" data-validate-for="captcha"></div>
```

The captcha widget resets automatically after each AJAX request.

### Upgrading from 1.1.x

The 1.3.0 update migrates the old reCAPTCHA settings (site key and secret key) to the reCAPTCHA v2 provider automatically. If you override `contactform/default.htm` in your theme, replace the old reCAPTCHA markup with the snippet above.

See [CHANGELOG.md](CHANGELOG.md) for the full list of changes.

## 🤑 Support Us 🤑

These codes make your life easier and you avoid wasting time?\
Give us some RedBull!

BUSD(BEP20)\
0x367B9207ACBC30022F9A7262320E36661D7Ffeb5

## ✉️ Contact Us ✉️ 

Do you have any suggestions?\
Do you need to customise this plugin?

Mail: webmaster@moonwalkerz.dev\
Telegram: @MoonWalkerzDev

