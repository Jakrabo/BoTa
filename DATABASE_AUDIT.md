# DATABASE AUDIT

Stand der Analyse: Repository-Arbeitsbaum am 1. August 2026, Branch `feature/2.6.0-dashboard`. Maßgeblich sind `administrator/sql/install.mysql.utf8mb4.sql`, `administrator/sql/uninstall.mysql.utf8mb4.sql`, die Update-SQL-Dateien und alle Tabellenzugriffe im PHP-Code.

## Überblick

- Datenbank: MySQL/MariaDB über Joomla Database API.
- Tabellenpräfix: Joomla-Platzhalter `#__`.
- Engine/Zeichensatz: InnoDB, `utf8mb4`, Kollation `utf8mb4_unicode_ci`.
- Neuinstallationsschema: 29 Tabellen.
- Primärschlüssel: überwiegend `int unsigned AUTO_INCREMENT`; Audit-, Achievement-Zuordnungs- und Strafregister verwenden `bigint unsigned`.
- Explizite Foreign Keys: 9. Viele weitere Beziehungen sind nur durch Indizes und Anwendungslogik abgesichert.

## Tabelleninventar

### Stammdaten und Benutzerbezug

| Tabelle | Zweck | Zentrale Beziehungen/Indizes |
|---|---|---|
| `#__jt_clubs` | Vereine | eindeutiger Name; Statusindex |
| `#__jt_classes` | Alters-/Geschlechtsklassen | Statusindex; Zuordnung über `athletes.class_id` |
| `#__jt_sportyears` | Sportjahre und aktuelles Sportjahr | Indizes auf `is_current` und Datumsbereich |
| `#__jt_athletes` | Schützenprofil, Joomla-Benutzerbezug, Kontakt- und Klassendaten | Indizes auf Name, `user_id`, Club, Klasse, Trainer, Status; FKs zu Club und Klasse |
| `#__jt_settings` | Key-Value-Laufzeitkonfiguration | eindeutiger `setting_key` |
| `#__jt_audit_log` | Protokoll automatischer Klassenwechsel, Self-Service-Aktionen u. a. | Indizes auf Entität, Benutzer und Zeitpunkt |

### Training und Anwesenheit

| Tabelle | Zweck | Zentrale Beziehungen/Indizes |
|---|---|---|
| `#__jt_training_locations` | GPS-fähige Trainingsorte | eindeutiger Name; Statusindex |
| `#__jt_training_groups` | Trainingsgruppen | Statusindex |
| `#__jt_training_group_athletes` | n:m Gruppe–Schütze | zusammengesetzter PK; Index auf Schütze |
| `#__jt_training_group_trainers` | n:m Gruppe–Joomla-Trainer | zusammengesetzter PK; Index auf Benutzer |
| `#__jt_training_sessions` | einzelne bzw. seriell erzeugte Trainingseinheiten | Indizes auf Datum, Trainer, Ort und Status; `training_group_id` ist im Installationsschema nicht indiziert |
| `#__jt_attendance` | Anwesenheit je Einheit und Schütze | eindeutiges Paar `(training_session_id, athlete_id)`; FKs mit Cascade Delete |
| `#__jt_training_diary` | persönliches Trainingstagebuch | Index auf Schütze/Datum und Bogensetup |

### Leistung, Programme und Ziele

| Tabelle | Zweck | Zentrale Beziehungen/Indizes |
|---|---|---|
| `#__jt_results` | Ergebnisse, Wetter, Prüfstatus und optionales Setup | Indizes auf Schütze, Datum, Ereignistyp, Distanz; kein FK |
| `#__jt_exercises` | Übungskatalog | Kategorie- und Statusindex |
| `#__jt_training_programs` | Trainingsprogramme | Kategorie- und Statusindex |
| `#__jt_program_exercises` | n:m Programm–Übung mit Reihenfolge | zusammengesetzter PK; Index auf Übung |
| `#__jt_athlete_programs` | wiederholbare Programmzuweisungen an Schützen | eigener PK; nicht-eindeutiger Index auf Schütze/Programm; Statusindex |
| `#__jt_program_progress` | Fortschritt je Programmzuweisung und Übung | eindeutiges Paar `(athlete_program_id, exercise_id)` |
| `#__jt_goals` | manuelle/automatische Ziele, optional programmbezogen | Indizes auf Schütze und Abschluss; kein Index auf `program_id` im Neuinstallationsschema |
| `#__jt_trainer_notes` | Trainerhinweise mit Sichtbarkeit und Status | Indizes auf Schütze und Datum; im Neuinstallationsschema fehlt der Update-Index auf `status` |

### Material und Visiereinstellungen

| Tabelle | Zweck | Zentrale Beziehungen/Indizes |
|---|---|---|
| `#__jt_bow_setups` | revisionierte Bogensetups | eindeutige Revision je Schütze; Aktivindex |
| `#__jt_sight_settings` | Entfernungswerte je Setup | eindeutiges Paar `(bow_setup_id, distance_m)` |

### Achievements, Strafen und Kalender

| Tabelle | Zweck | Zentrale Beziehungen/Indizes |
|---|---|---|
| `#__jt_achievements` | Definition manueller und automatischer Badges | eindeutiger Code; Modus-, Kategorie- und Statusindex |
| `#__jt_athlete_achievements` | Vergabe/Widerruf je Schütze | eindeutiges Paar Schütze–Achievement; zwei Cascade-FKs |
| `#__jt_penalty_definitions` | monetäre/nichtmonetäre Strafdefinitionen | Typ- und Statusindex |
| `#__jt_penalty_register` | konkrete Straffälle mit Snapshots und Status | Indizes auf Schütze, Definition, Status, Zeitpunkt; zwei FKs |
| `#__jt_calendar_events` | mehrtägige Termine, Zielgruppe und optionale Gruppe | Datums-, Zielgruppen-, Gruppen-, Kategorie-, Orts- und Statusindizes |
| `#__jt_calendar_attachments` | PDF-Anhänge als Base64/`mediumtext` | FK zum Termin mit Cascade Delete |

## Beziehungen und Datenfluss

- Joomla-Benutzer werden über `athletes.user_id`, `trainer_user_id`, Zuordnungstabellen und Audit-Felder referenziert; es gibt keine FKs auf Joomla-Coretabellen.
- Ein Schütze gehört über `training_group_athletes` zu Gruppen; Trainer erhalten ihren Datenbereich über `training_group_trainers`. Trainingseinheiten referenzieren genau eine Gruppe.
- Attendance verbindet Trainingseinheit und Schütze. Trainerpflege, Self-Check-in und Selbstabmeldung schreiben in dieselbe Tabelle.
- Programme bestehen aus Übungen. `athlete_programs.id` repräsentiert eine konkrete Zuweisung, sodass dasselbe Programm mehrfach zugewiesen werden kann; Fortschritt hängt an dieser Zuweisungs-ID.
- Ergebnisse und Tagebuch können eine konkrete Bogensetup-Revision referenzieren. Visiereinstellungen hängen an der Revision.
- Achievement-Auswertung liest Tagebuch und bestätigte Ergebnisse und schreibt Vergaben. Widerrufe bleiben als Felder im Vergabedatensatz erhalten.
- Kalenderanhänge werden vollständig in der Datenbank gespeichert und nur nach serverseitiger Sichtbarkeitsprüfung ausgeliefert.

## Explizite Foreign Keys

Vorhanden sind ausschließlich:

1. `athletes.club_id -> clubs.id` (`SET NULL`)
2. `athletes.class_id -> classes.id` (`SET NULL`)
3. `attendance.training_session_id -> training_sessions.id` (`CASCADE`)
4. `attendance.athlete_id -> athletes.id` (`CASCADE`)
5. `athlete_achievements.athlete_id -> athletes.id` (`CASCADE`)
6. `athlete_achievements.achievement_id -> achievements.id` (`CASCADE`)
7. `penalty_register.athlete_id -> athletes.id` (`CASCADE`)
8. `penalty_register.penalty_definition_id -> penalty_definitions.id` (`RESTRICT`)
9. `calendar_attachments.event_id -> calendar_events.id` (`CASCADE`)

Nicht auf DB-Ebene abgesichert sind insbesondere Gruppen-, Programm-, Übungs-, Ziel-, Ergebnis-, Tagebuch-, Setup-, Ort-, Trainer- und Kalendergruppenreferenzen. Löschkonsistenz hängt dort von Models/Services ab; verwaiste Datensätze sind bei direkten DB-Eingriffen oder unvollständigen Anwendungspfaden möglich.

## Installations- und Updatepfad

Das Manifest verweist für Neuinstallationen auf das vollständige Installationsschema und für Updates auf `administrator/sql/updates/mysql`.

| SQL-Version | Änderungsschwerpunkt |
|---|---|
| `0.1.0` | Marker; keine DDL |
| `0.1.4` | Basisstammdaten, Athleten, Training, Attendance, Settings, Audit |
| `0.2.0` | Trainer-, Geschlechts- und Bogentypfelder |
| `0.3.0` | Training und Attendance erneut defensiv angelegt |
| `0.4.0`–`0.4.4` | Ergebnisse, Prüfstatus, historische Benutzer-ID-Felder |
| `0.5.0`–`0.5.3` | Übungen, Programme, Fortschritt, Ziele, Trainernotizen |
| `0.6.0` | Trainingsgruppen und Gruppenzuordnungen |
| `0.7.0` | Bogensetups, Visiereinstellungen, Tagebuch, Ergebnis-Setup |
| `0.8.0` | Wetterfelder an Ergebnissen |
| `0.9.0`–`0.9.3` | Achievements, Seeds, Regelattribute, Badgepfad-Migration |
| `1.2.0` | Strafdefinitionen und Strafregister |
| `1.3.0` | Programmabschluss und Strafsaldo-Resetwert |
| `1.6.0` | Trainernotizstatus |
| `1.9.0` | Kalender und PDF-Anhänge |
| `1.10.0` | mehrtägige Termine, Zielgruppe, Gruppensichtbarkeit |
| `2.2.0` | Trainingsorte/GPS, Ort an Training, wiederholbare Programmzuweisung, Prüfstatusbereinigung |

`script.php` ergänzt den SQL-Pfad nach Installation/Update nur für drei Spalten: `results.bow_setup_id`, `goals.program_id` und `athlete_programs.completed_at`. Es ist kein allgemeiner Schemaabgleich.

## Kritische Befunde

### Hoch: Deinstallation ist unvollständig

`uninstall.mysql.utf8mb4.sql` löscht weder `#__jt_results` noch `#__jt_athlete_achievements` noch `#__jt_achievements`. Dadurch bleiben Nutz-/Leistungsdaten und Tabellen nach einer Komponenten-Deinstallation zurück. Wegen der FKs muss `athlete_achievements` vor `athletes` entfernt werden; andernfalls kann das vorhandene `DROP athletes` je nach FK-Prüfung fehlschlagen.

### Hoch: Updatepfad kann Altschema vom Neuinstallationsschema abweichen lassen

- `0.4.2.sql` legt historisch `athletes.joomla_user_id` an; eine spätere Migration/Entfernung dieses Feldes ist nicht vorhanden. Aktualisierte Installationen können damit ein zusätzliches Legacy-Feld behalten.
- `0.4.4.sql` fügt daneben `user_id` hinzu. Eine Datenübernahme von `joomla_user_id` nach `user_id` ist im SQL-Pfad nicht erkennbar.
- Mehrere frühe `ALTER TABLE`-Anweisungen verwenden kein `IF NOT EXISTS`; der tatsächliche Erfolg hängt vom gespeicherten Joomla-Schemastand ab.

### Mittel: Installationsschema und Update-Indizes sind nicht vollständig synchron

- `0.6.0.sql` legt `idx_jt_training_group_id` an; dieser Index fehlt im aktuellen Neuinstallationsschema.
- `0.5.3.sql` legt `idx_jt_goals_program_id` an; dieser Index fehlt im aktuellen Neuinstallationsschema.
- `1.6.0.sql` legt `idx_jt_notes_status` an; dieser Index fehlt im aktuellen Neuinstallationsschema.
- `0.7.0.sql` legt `idx_jt_results_bow_setup` an; dieser Index fehlt im aktuellen Neuinstallationsschema.

Damit können aktualisierte und frisch installierte Systeme unterschiedliche Query-Pläne besitzen.

### Mittel: Geringe referenzielle Absicherung

Nur neun Beziehungen sind als FKs modelliert. Besonders die Zuordnungstabellen sowie `training_sessions.training_group_id/location_id`, `results.athlete_id/bow_setup_id`, `athlete_programs.*`, `program_progress.*`, `training_diary.*` und `calendar_events.training_group_id` können verwaisen.

### Niedrig bis mittel: Konfigurations- und Blobdaten

- `#__jt_settings` speichert untypisierte Textwerte; Grenzen und Defaults werden ausschließlich in PHP erzwungen.
- PDFs werden Base64-kodiert in `mediumtext` gespeichert. Das erhöht den Speicherbedarf gegenüber Binärdaten grob um ein Drittel und vergrößert Backups sowie DB-Transfers.
- `is_current` ist nur indiziert, nicht eindeutig; mehrere aktuelle Sportjahre sind DB-seitig möglich. Die Anwendung synchronisiert dies logisch.

## Verifikation

- Alle 29 im Installationsschema angelegten Tabellen werden im PHP-Code referenziert.
- PHP verwendet keine weitere `#__jt_*`-Tabelle außerhalb dieses Inventars.
- Der Updatepfad endet bei `2.2.0`; für 2.2.1/2.2.2 gibt es laut Changelog keine zusätzliche DDL.
- Es wurde keine Datenbank ausgeführt oder verändert; die Prüfung ist statisch.
