# CODE AUDIT

## Scope

Audit covers the current repository state of the Joomla component `com_jugendtraining` at version 2.2.2. The assessment includes manifest, administrator and site MVC, models, views, controllers, services, router, ACL, SQL, language files, JavaScript, CSS, media, installer, update SQL and WebAssets.

## Manifest

- `com_jugendtraining.xml` is the Joomla component manifest.
- Component version is `2.2.2`.
- Declared Joomla target version is `6.0`.
- Component namespace is defined as `Jugendtraining\Component\Jugendtraining` with `path="src"`.
- Administrator files are declared under `administrator` and include `language`, `forms`, `services`, `sql`, `src`, `tmpl`, `access.xml`, and `config.xml`.
- Site files are declared under `site` and include `language`, `forms`, `src`, and `tmpl`.
- Media section installs `media/joomla.asset.json`, `css`, `js`, and `images` into `media/com_jugendtraining`.
- Install SQL uses `administrator/sql/install.mysql.utf8mb4.sql`.
- Uninstall SQL uses `administrator/sql/uninstall.mysql.utf8mb4.sql`.
- Updates are declared via `sql/updates/mysql`.
- Config fields are `organisation_name` and `default_training_duration`.
- Permissions are enabled through a standard Joomla `<field name="rules" ...>` configuration block.
- Update server is configured in the manifest to GitHub release source.

### Manifest anomalies

- The manifest namespace path is `src` but source code is stored under `administrator/src` and `site/src`.
- Several empty or stale sibling directories are present but not referenced by the manifest: `administrator/src 2`, `administrator/sql 2`, `administrator/forms 2`, `administrator/language 2`, `administrator/tmpl 2`, `site/src 2`, `site/forms 2`, `site/language 2`, `site/tmpl 2`, `media/css 2`, `media/js 2`.

## MVC Structure

### Administrator

- Controllers: 28 files.
- Models: 29 files.
- Views: 30 view directories.
- Tables: 15 table classes.
- Services: 2 service classes.
- Extension entry class: `administrator/src/Extension/JugendtrainingComponent.php`.

### Site

- Controllers: 20 files.
- Models: 35 files.
- Views: 35 view directories.
- Services: 6 service classes.
- Templates: 61 site template files.

### Forms

- Administrator form definitions: 13 XML files.
- Site form definitions: 4 XML files.

### Helpers

- No dedicated helper files or `*Helper.php` classes were found in the repository.

## Models

- Administrator and frontend models rely on Joomla MVC base classes like `BaseDatabaseModel`, `AdminModel`, and `FormController`.
- `TrainertrainingModel` implements business rules for trainer-owned sessions, series creation, attendance persistence, and permission checks.
- `TrainertrainingsModel` provides trainer-specific training listings with period and group filters.
- `SelfcheckinModel` delegates eligible session retrieval and settings retrieval to `SelfCheckinService`.
- Some model and controller PHP sources appear to be stored in compact/minified single-line format, negatively affecting readability.

## Views

- Administrator views are standard Joomla backend views with ToolbarHelper usage and form integration.
- Site views support a broad feature set including dashboards, self-checkin, trainer training, athlete detail, calendar, and penalties.
- `DisplayController` acts as the default site controller and performs request preprocessing before dispatch.

## Controllers

- Administrator controllers implement CRUD and toolbar behavior across entities.
- Frontend controllers include form handling, self-checkin endpoints, trainer task management, and dashboard display.
- `DisplayController`:
  - sets the default frontend view to `dashboard`
  - applies a user theme cookie and inline theme script
  - synchronizes the current sport year on every frontend request
- `TrainertrainingController` exposes AJAX-style attendance saving and scoped deletion of trainer-owned sessions.

## Service Layer

- There is no generic helper layer; logic is placed in service classes and models.
- Site service classes include:
  - `AccessService`
  - `AchievementService`
  - `BadgeUploadService`
  - `CalendarService`
  - `SelfAttendanceService`
  - `SelfCheckinService`
- Administrator service classes include:
  - `CsvImportService`
  - `ClassTransitionService`
- `AccessService` centralizes trainer and athlete authorization and group membership detection.

## Router

- No custom router implementation exists.
- No `Router.php` file or `buildRoute` / `parseRoute` methods were found.
- The component relies on Joomla core routing and standard `index.php?option=com_jugendtraining&view=...` links.

## ACL

- ACL actions in `administrator/access.xml`:
  - `core.admin`, `core.options`, `core.manage`, `core.create`, `core.delete`, `core.edit`, `core.edit.state`, `core.edit.own`
  - custom actions: `trainer.access`, `athlete.access`, `groups.manage`, `training.manage`, `attendance.manage`, `results.manage`
- Frontend authorization is handled by `AccessService`, not by route-level ACL only.
- `AccessService` combines Joomla ACL, named Joomla user groups, and component-specific group membership checks.

## SQL and Database

- The install schema is defined in `administrator/sql/install.mysql.utf8mb4.sql` and includes all core tables plus seeded achievements.
- Primary schema objects:
  - athletes, clubs, classes, sportyears
  - training locations, training sessions, attendance
  - results, exercises, programs, program progress
  - goals, trainer notes, training groups
  - bow setups, sight settings, training diary
  - achievements, athlete achievements
  - penalties, calendar events, calendar attachments
  - settings, audit log
- Foreign key usage is limited and inconsistent.
- Explicit foreign keys exist only for:
  - `#__jt_athletes.club_id` -> `#__jt_clubs.id`
  - `#__jt_athletes.class_id` -> `#__jt_classes.id`
  - `#__jt_attendance.training_session_id` -> `#__jt_training_sessions.id`
  - `#__jt_attendance.athlete_id` -> `#__jt_athletes.id`
  - `#__jt_athlete_achievements.athlete_id` -> `#__jt_athletes.id`
  - `#__jt_athlete_achievements.achievement_id` -> `#__jt_achievements.id`
  - `#__jt_penalty_register.athlete_id` -> `#__jt_athletes.id`
  - `#__jt_penalty_register.penalty_definition_id` -> `#__jt_penalty_definitions.id`
  - `#__jt_calendar_attachments.event_id` -> `#__jt_calendar_events.id`
- Many relationship fields do not have DB-level constraints:
  - training_groups, training_locations, trainer_user_id on `#__jt_training_sessions`
  - join tables for programs, training groups, and bow setups
  - results, training diary, athlete programs, and calendar event references

## Language Files

- Administrator languages: `administrator/language/de-DE/com_jugendtraining.ini`, `administrator/language/en-GB/com_jugendtraining.ini`.
- Site languages: `site/language/de-DE/com_jugendtraining.ini`, `site/language/en-GB/com_jugendtraining.ini`.
- Admin language files contain 762 keys each.
- Site language files contain 694 keys each.
- All used translation keys found in code are defined in the available language files.

## JavaScript

- `media/js/site.js` and `media/js/admin.js` are minimal startup markers.
- `media/js/selfcheckin.js` implements the frontend GPS self-checkin flow with HTTPS and geolocation handling.
- `media/js/theme.js` exists but is not referenced anywhere in the current codebase.
- `selfcheckin.js` is loaded directly in the self-checkin template, bypassing the asset registry.

## CSS

- `media/css/site.css` provides the frontend component theme and dark/light mode styles.
- `media/css/admin.css` provides backend styling for dashboard cards and related admin UI.
- `site.css` header comment references `Jugendtraining 0.8.2`, which is inconsistent with component version 2.2.2.

## Media

- `media/images/badges` contains badge graphics used by seeded achievements.
- `media/joomla.asset.json` defines Joomla WebAssets for admin, site, and selfcheckin.
- There are empty duplicate directories outside manifest scope: `media/css 2`, `media/js 2`.

## Installer

- `script.php` provides installer hooks:
  - `preflight` removes stale administrator menu entries.
  - `postflight` ensures Joomla groups `BoTa - Trainer` and `BoTa - Schütze` exist.
  - `postflight` also synchronizes missing schema columns for current install state.
- `script.php` patches missing columns for:
  - `#__jt_results.bow_setup_id`
  - `#__jt_goals.program_id`
  - `#__jt_athlete_programs.completed_at`

## Update SQL

- The repository contains schema update files for multiple historical versions under `administrator/sql/updates/mysql`.
- Present update file names include: `0.1.0.sql`, `0.1.4.sql`, `0.2.0.sql`, `0.3.0.sql`, `0.4.0.sql`, `0.4.1.sql`, `0.4.2.sql`, `0.4.4.sql`, `0.5.0.sql`, `0.5.1.sql`, `0.5.3.sql`, `0.6.0.sql`, `0.7.0.sql`, `0.8.0.sql`, `0.9.0.sql`, `0.9.2.sql`, `0.9.3.sql`, `1.10.0.sql`, `1.2.0.sql`, `1.3.0.sql`, `1.6.0.sql`, `1.9.0.sql`, `2.2.0.sql`.
- The update history covers schema evolution from the initial data model through achievements, calendar, penalties, and training locations.
- There is no update file for `2.2.2`.

## WebAssets

- `media/joomla.asset.json` defines the WebAsset registry:
  - `com_jugendtraining.admin` (style + script)
  - `com_jugendtraining.site` (style + script)
  - `com_jugendtraining.selfcheckin` (script)
- `com_jugendtraining.site` is actively used in site controllers and some templates.
- `com_jugendtraining.admin` is declared but not referenced in code.
- `com_jugendtraining.selfcheckin` is declared but the frontend self-checkin template loads its JS file directly.

## Versioning and Namespace

- Component version declared as `2.2.2` in `com_jugendtraining.xml`.
- Asset registry version declared as `2.2.2` in `media/joomla.asset.json`.
- Update server version declared as `2.2.2` in `updates.xml`.
- Root `changelog.xml` includes only a single `2.2.1` changelog entry; the `2.2.2` release is missing from this root changelog.
- Namespace usage is consistent across admin and site components, with `Administrator` and `Site` sub-namespaces.

## Findings and Issues

- The current component state is functionally complete for a broad set of training management features.
- There is no custom router implementation.
- There is no dedicated helper layer in the codebase.
- Database referential integrity is weak: many relation fields are not protected by foreign keys.
- Several source files are stored in compact/minified format, reducing maintainability.
- Asset registry declarations and actual asset usage are not fully aligned.
- The self-checkin asset is defined but not consumed through WebAssets, and `theme.js` is not referenced.
- Some version metadata is stale: `site.css` header comment and root `changelog.xml` missing latest release notes.
- `script.php` supplements installation and update processes by ensuring groups and schema columns.
- No mail system or email notifications are implemented in the current code base.
