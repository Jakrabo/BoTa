# VERSION INVENTORY

## Component Version

- Manifest version: `2.2.2` (`com_jugendtraining.xml`)
- Asset manifest version: `2.2.2` (`media/joomla.asset.json`)
- Update server version: `2.2.2` (`updates.xml`)
- Current package release is `2.2.2`.

## Joomla and PHP Requirements

- Joomla compatibility: `6.0` (manifest attribute `version="6.0"`).
- PHP minimum requirement: `8.3.0` (`updates.xml`).

## Release Notes and Changelogs

- `CHANGELOG.md` includes entries for:
  - `2.2.2`
  - `2.2.1`
  - `2.2.0`
  - `2.1.1`
  - `2.1.0`
  - `2.0.3`
  - `2.0.2`
  - `2.0.1`
  - `2.0.0`
  - `1.10.10` through `1.10.0`
  - `1.9.1` through `1.9.0`
  - `1.8.6` through `1.8.2`
- `changelog.xml` includes only version `2.2.1`; the `2.2.2` release note is absent.

## Update SQL Files

Stored update scripts in `administrator/sql/updates/mysql`:

- `0.1.0.sql`
- `0.1.4.sql`
- `0.2.0.sql`
- `0.3.0.sql`
- `0.4.0.sql`
- `0.4.1.sql`
- `0.4.2.sql`
- `0.4.4.sql`
- `0.5.0.sql`
- `0.5.1.sql`
- `0.5.3.sql`
- `0.6.0.sql`
- `0.7.0.sql`
- `0.8.0.sql`
- `0.9.0.sql`
- `0.9.2.sql`
- `0.9.3.sql`
- `1.10.0.sql`
- `1.2.0.sql`
- `1.3.0.sql`
- `1.6.0.sql`
- `1.9.0.sql`
- `2.2.0.sql`

Notes:
- The update history extends to `2.2.0` for schema changes.
- There is no schema update script named `2.2.2.sql`.

## Installer Schema Files

- Install schema: `administrator/sql/install.mysql.utf8mb4.sql`
- Uninstall schema: `administrator/sql/uninstall.mysql.utf8mb4.sql`
- Installer script: `script.php`

## WebAssets

- `media/joomla.asset.json` defines:
  - `com_jugendtraining.admin`
  - `com_jugendtraining.site`
  - `com_jugendtraining.selfcheckin`
- `media/js/theme.js` exists but is not referenced.

## Version Metadata Mismatches

- `media/css/site.css` contains a header comment referring to `Jugendtraining 0.8.2`.
- Root `changelog.xml` is outdated compared to the current manifest version.
- `media/joomla.asset.json` and manifest are aligned on `2.2.2`, but WebAsset usage is inconsistent.

## Package and Update Server

- `updates.xml` is configured to publish a full ZIP download from `https://github.com/Jakrabo/BoTa/releases/download/v2.2.2/com_bota-2.2.2.zip`.
- `updates.xml` references `changelog.xml` for release notes.
- `com_jugendtraining.xml` uses a remote update server URL `https://raw.githubusercontent.com/Jakrabo/BoTa/main/updates.xml`.
