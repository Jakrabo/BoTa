# PROJECT STATE

## Projektübersicht

BoTa ist eine Joomla-Komponente zur Verwaltung von Jugendtraining für Bogensport. Der aktuelle Codebestand reflektiert Version `2.2.2` und enthält Backend- und Frontend-Funktionalität für Athleten, Trainer, Trainings, Ergebnisse, Kalender, Achievements, Strafen, Programme und Self-Check-in.

## Architektur

- Joomla 6-kompatible Komponente mit getrennten Administrator- und Site-Bereichen.
- Namespace-Architektur: `Jugendtraining\Component\Jugendtraining\Administrator` und `Jugendtraining\Component\Jugendtraining\Site`.
- Administratorseite: klassische MVC-Struktur mit Controllern, Models, Views, Table-Klassen und Formularen.
- Frontendseite: ebenfalls MVC mit zusätzlichem Service-Layer für Berechtigungen, Kalender, Achievements und Self-Check-in.
- Keine dedizierte Helper-Schicht vorhanden.

## Datenmodell

- Relationale Tabellen für Kernentitäten: Athleten, Clubs, Klassen, Sportjahre.
- Trainingsdaten: Trainingsgruppen, Trainingssessions, Trainingsorte, Anwesenheit.
- Leistungsdaten: Ergebnisse, Trainings-Tagebuch, Programme, Übungen, Programmfortschritt, Ziele.
- Erweiterte Funktionalität: Achievements, Strafen, Kalenderveranstaltungen, PDF-Anhänge, Bogensetups und Sicht-Einstellungen.
- Zentraler Einstellungen-Store: `#__jt_settings`.
- Audit-Log: `#__jt_audit_log` für automatische Aktionen und Nachvollziehbarkeit.
- Datenintegrität: dezente DB-Foreign-Keys existieren, aber viele Referenzen werden nur auf Anwendungsebene gesteuert.

## Backend

- Backend-Menü umfasst Dashboard, Athleten, Trainings, Trainingsorte, Ergebnisse, Übungen, Programme, Ziele, Trainingsgruppen, Achievements, Import, Trainernotizen, Vereine, Klassen, Sportjahre, Kalender.
- Backend verwendet Joomla-ACL-Regeln und Backend-Toolbar-Integration.
- Administratorischen Services:
  - `CsvImportService` für CSV-Import von Ergebnissen, Tagebucheinträgen und Achievements.
  - `ClassTransitionService` für automatische Klassenzuordnung beim Wechsel des Sportjahres.
- Backend-Konfiguration über `administrator/config.xml` mit Basis-Konfiguration und Berechtigungsregeln.

## Frontend

- Frontend bietet Trainer- und Athletenansichten.
- `DisplayController` ist der zentrale Einstiegspunkt und verwaltet Theme-Optionen sowie die Synchronisierung des aktuellen Sportjahres.
- Seiten für Trainer:
  - Trainerdashboard
  - Trainertrainings
  - Trainerathleten
  - Trainerkalender
  - Trainerpenalties
  - Trainerachievementdefinitions / Trainerachievementedit
  - Trainernotesfront
  - Trainerresults
- Seiten für Athleten:
  - Dashboard
  - Selfcheckin
  - Trainingstagebuch
  - Ergebnisse
  - Userpreferences
- Frontend verwendet moderne CSS-Theme-Unterstützung und eine eigene `bota_theme`-Cookie-/Benutzereinstellungslogik.

## ACL

- Backend-Actions sind im `access.xml` definiert.
- Frontend-Berechtigungen prüfen:
  - Superuser
  - Komponentenspezifisches `trainer.access` oder `athlete.access`
  - Joomla-Benutzergruppen `BoTa - Trainer` und `Jugendtraining - Trainer`
  - Joomla-Benutzergruppen `BoTa - Schütze` und `Jugendtraining - Schütze`
  - Verknüpfte Athlete/Datenbank-Einträge.
- Frontend-Berechtigung ist stark an `AccessService` gebunden.

## Routing

- Keine eigene Routerklasse vorhanden.
- Routing erfolgt über Joomla-Standard-URLs.
- Templates und Controller verwenden häufig `Route::_('index.php?option=com_jugendtraining&view=...')`.

## Konfiguration

- Basis-Konfiguration im Joomla-Komponenten-Parameterbereich verfügbar.
- Laufzeitkonfiguration für Self-Check-in und Selbstabmeldung wird in `#__jt_settings` gespeichert.
- `script.php` stellt sicher, dass Joomla-Benutzergruppen und aktuelle Schema-Spalten bei Installation/Update vorhanden sind.

## Mailsystem

- Es existiert kein E-Mail- oder Benachrichtigungssystem im aktuellen Quellstand.
- Es gibt E-Mail-Felder im Athletenprofil, aber keinen Versand- oder Empfangscode.

## Self Checkin

- Self-Check-in wird über `SelfCheckinService` realisiert.
- Voraussetzungen:
  - HTTPS / Secure Context
  - Geolocation API im Browser
  - konfigurierter Self-Check-in-Status in `#__jt_settings`
  - zugeordneter Schütze mit aktiver Trainingsgruppe und Trainingstermin
- Ergebnis: Anwesenheit `present` wird in der `#__jt_attendance`-Tabelle gespeichert.
- Frontendseite lädt `media/js/selfcheckin.js` direkt und verarbeitet Positionen und Fehler.

## Übungen

- Übungen und Trainingsprogramme sind getrennt modelliert.
- `#__jt_exercises`, `#__jt_training_programs`, `#__jt_program_exercises`, `#__jt_athlete_programs`, `#__jt_program_progress` bilden das Übungs- und Programm-Subsystem.
- Programme können mehreren Schützen zugewiesen werden, Fortschritt wird pro Zuweisung dokumentiert.

## Trainingseinheiten

- Trainingsgruppen und Trainingssessions sind zentrale Entities.
- Sitzungen referenzieren Trainingsgruppen, Orte, Trainer und optional Trainingsort-Details.
- Attendance wird pro Training und Athlet gespeichert.
- Trainer können Trainings als Serien anlegen und Anwesenheit für Gruppen speichern.
- Gruppenzugehörigkeit ist Bestandteil der Zugriffskontrolle.

## Achievements

- Achievement-Definitionen sind in `#__jt_achievements` gespeichert.
- Automatische und manuelle Auszeichnung möglich.
- Bewertungslogik umfasst:
  - Tages- und Wochenpfeil-Schwellen
  - Tagebuch-Streaks
  - Eventname-basierte Achievements
- `AchievementService` wertet Athleten aus und schreibt in `#__jt_athlete_achievements`.
- Seeded Achievements werden beim Installieren in `install.mysql.utf8mb4.sql` angelegt.

## Strafen

- Strafdefinitionen und Register sind vorhanden.
- `#__jt_penalty_definitions` definiert Strafen.
- `#__jt_penalty_register` speichert Straffälle.
- `SelfAttendanceService` erzeugt bei verspäteter Selbstabmeldung optional eine Strafe.
- Es gibt eine Traineransicht für Strafen.

## Kalender

- Kalender ist als eigener Teil implementiert.
- `CalendarService` verwaltet:
  - zugängliche Kategorien
  - Veranstaltungsfilter nach Datum, Kategorie, Ort
  - Trainergruppenabhängige Sichtbarkeit
  - PDF-Anhänge als Base64-Daten in der DB
- Tabellen: `#__jt_calendar_events`, `#__jt_calendar_attachments`.

## Bekannte TODOs

- Im aktuellen Quellstand wurden keine TODO- oder FIXME-Kommentare gefunden.

## Technische Schulden

- Viele PHP-Dateien sind stark komprimiert oder einzeilig formatiert.
- WebAsset-Registry und tatsächliche Asset-Nutzung sind nicht vollständig synchronisiert.
- `media/js/theme.js` ist im Bestand, wird aber nicht verwendet.
- `media/css/site.css` trägt einen veralteten Versionskommentar (`0.8.2`).
- Root `changelog.xml` fehlt der Eintrag für Version `2.2.2`.
- Datenbank-FK-Integrität ist inkonsistent und nur teilweise umgesetzt.
- Stale Ordner wie `administrator/src 2` und `site/src 2` existieren.
- `com_jugendtraining.admin` WebAsset ist definiert, aber nicht referenziert.
- Die Manifest-/Namespace-Path-Abstimmung erfordert Überprüfung.
