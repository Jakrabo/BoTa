# BoTa 1.8.6 – Security & Privacy Review

Review scope: static review of the component package, focusing on frontend authorization,
object-level access control, CSRF, SQL construction, output handling, uploads and personal data.

## Fixed in 1.8.6

- Attendance status is translated instead of exposing internal database values.
- Trainer/Athlete named groups support both the new BoTa names and legacy Jugendtraining names.
- `getTrainerAthleteIds()` refuses cross-user lookups for non-superusers.
- Administrator entry point now explicitly requires `core.manage` / `core.admin`.
- Configuration/import actions require both administrator permission and trainer permission.
- Training diary edit authorization is object-specific instead of “any logged-in user”.
- Training diary bow-setup references must belong to the current athlete.
- Bow-setup edit/load/sight-setting access is object-specific; arbitrary IDs are rejected.
- Trainer task editing verifies that the assignment belongs to the selected managed athlete.
- Trainer athlete editing adds server-side length, email and class validation.
- Trainer notes add server-side date and length validation.
- Penalty reasons/completion notes are length-limited server-side.
- Badge uploads are limited by file size and dimensions, PNG signature is verified, and images
  are re-encoded when GD is available.
- Language override editing is limited to known component keys, known languages, bounded values.
- Sensitive trainer detail/note/penalty/edit views send `Cache-Control: private, no-store`.

## Existing protections confirmed

- Trainer data queries are scoped through assigned training groups.
- Trainer detail access uses `canManageAthlete()` before returning contact data and notes.
- Athlete-facing trainer notes only return `private_note = 0`.
- Result editing/deletion is scoped to the logged-in athlete.
- Trainer penalty changes validate trainer ownership of the athlete/entry.
- Progress changes validate the logged-in athlete and assigned program.
- CSV exports protect spreadsheet cells against formula injection.
- State-changing custom POST actions use Joomla session tokens.
- Database input is generally cast to integer or quoted through Joomla's database API.
- Template output reviewed in sensitive trainer views uses HTML escaping for user-controlled text.

## Residual / operational considerations

- A static review cannot prove the absence of vulnerabilities. Keep Joomla and PHP patched and
  restrict backend access to trusted accounts.
- Some legacy state-changing actions still use token-protected GET links. The CSRF token prevents
  ordinary cross-site requests, but POST-only actions would be preferable in a future cleanup.
- Uploaded badge files are public by design. Do not upload personal/confidential images as badges.
- Database backups contain personal data (names, phone, e-mail, attendance, notes, penalties).
  Protect backups, restrict DB accounts and define a retention/deletion policy.
- Whether the installation is legally GDPR-compliant also depends on hosting, privacy notices,
  legal basis, retention periods, access roles, processor agreements and organisational processes;
  those cannot be established from source code alone.
