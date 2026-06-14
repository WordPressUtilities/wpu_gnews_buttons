# WPUGnewsButtons

Display Google News call-to-action buttons on your WordPress site: an **Add source** button (lets readers prioritize your domain in Google News) and a **Follow us on Google News** button.

## Features

- Two independent buttons, each toggleable from the settings page:
  - **Add source** — links to Google News source preferences for your domain (or a custom string).
  - **Follow us** — links to your Google News publication URL.
- Localized button artwork (English / French) with light & dark variants.
- Output via a single shortcode, ready to drop anywhere.

## Installation

1. Copy the `wpu_gnews_buttons` folder into `wp-content/plugins/`.
2. Activate the plugin from the WordPress admin.
3. Configure the buttons under **Settings → WPUGnewsButtons**.

## Usage

Place the shortcode where you want the buttons to appear:

```
[wpu_gnews_buttons]
```

Only the buttons enabled in the settings (and with valid values) are rendered. If none are enabled, nothing is output.

## Requirements

- WordPress 6.2+
- PHP 8.0+

## License

MIT License — https://opensource.org/licenses/MIT
