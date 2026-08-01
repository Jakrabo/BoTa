# CHANGELOG ANALYSIS

Stand der Analyse: Repository-Arbeitsbaum am 1. August 2026. Analysiert wurden `CHANGELOG.md`, `changelog.xml`, `updates.xml`, `com_jugendtraining.xml`, `README.md`, `media/joomla.asset.json`, die SQL-Updates und der aktuelle Code.

## Aktueller Versionsstand

| Quelle | deklarierter Stand | Bewertung |
|---|---:|---|
| `com_jugendtraining.xml` | 2.2.2 | maßgebliche Paketversion |
| `updates.xml` | 2.2.2 | deckungsgleich; Release-ZIP und Infourl zeigen auf `v2.2.2` |
| `media/joomla.asset.json` | 2.2.2 | deckungsgleich |
| `CHANGELOG.md` | 2.2.2 | jüngster Markdown-Eintrag vorhanden |
| `changelog.xml` | 2.2.1 | veraltet; vom Updatefeed referenziert |
| `README.md` | Überschrift 1.0.1 | stark veraltet und als Versionsquelle ungeeignet |
| `media/css/site.css` | Kommentar 0.8.2 | veralteter Kommentar ohne Laufzeitwirkung |
| jüngstes Update-SQL | 2.2.0 | plausibel, sofern 2.2.1/2.2.2 ausschließlich Code-/Assetfixes sind |

Joomla-Ziel und PHP-Minimum stehen nur im Updatefeed vollständig zusammen: Joomla `6.[0-9]+`, PHP `8.3.0`. Das Manifest trägt `version="6.0"`, enthält aber kein eigenes PHP-Minimum.

## Entwicklungslinien

`CHANGELOG.md` dokumentiert 78 Releases von 0.2.0 bis 2.2.2, überwiegend am 27.–29. Juli 2026. Die wesentlichen Entwicklungslinien sind:

1. **0.2–0.4:** Athletenstammdaten, Backend-Training/Attendance, Frontend-Ergebnisse und Prüfstatus.
2. **0.5:** Übungskatalog, Programme, Fortschritt, Ziele und Trainernotizen.
3. **0.6:** Joomla-Rollen, Trainingsgruppen, Trainer-Frontend und gruppenbasierte Sichtbarkeit.
4. **0.7–0.8:** revisionierte Bogensetups, Visiereinstellungen, Trainingstagebuch, Statistiken, Wetterdaten und Designsystem.
5. **0.9:** Achievement-System mit automatischen Regeln, manueller Vergabe, Upload und CSV-Import.
6. **1.0–1.6:** Attendance-Ausbau, CSV-Import/Export, Dashboardkonfiguration, Strafen, Aufgabenstatus und Notizstatus.
7. **1.7–1.10:** Schützendetail, Theme, Login-Ziel, Kalender, PDF-Anhänge und gruppenabhängige Termine.
8. **2.0–2.1:** Selbstabmeldung, Fristen/Strafen, Sportjahr-/Klassenwechsel und Listenverbesserungen.
9. **2.2:** GPS-Self-Check-in, Trainingsorte, wiederholbare Programmzuweisung und Robustheitsfixes.

## Abgleich der letzten Releases mit dem Code

### 2.2.2

Der Eintrag beschreibt die direkte Einbindung von `selfcheckin.js`, um die GPS-Schaltfläche unabhängig von einer veralteten WebAsset-Registry auszuführen. Das ist im Template umgesetzt: Der Self-Check-in lädt das Skript direkt. Gleichzeitig bleibt `com_jugendtraining.selfcheckin` in `joomla.asset.json` registriert; Dokumentation und Registry sind daher funktional, aber nicht konzeptionell bereinigt.

### 2.2.1

Die beschriebenen konfigurierbaren Zeitfenster sind in `SelfCheckinService` vorhanden: Vorlauf, Ende über `end_time` oder Fallbackdauer. Browser-/HTTPS-/Geolocationfehler werden in `media/js/selfcheckin.js` behandelt. Die XML-Changelogdatei führt unter Version 2.2.1 allerdings zusätzlich größere 2.2.0-Funktionen auf und bildet die Markdown-Trennung nicht sauber ab.

### 2.2.0

Trainingsorte, `location_id`, Geofencing, normalisierter Ergebnis-Prüfstatus und der nicht mehr eindeutige Schütze/Programm-Index sind sowohl im Neuinstallationsschema als auch in `2.2.0.sql` sichtbar. Achievement-CSV verwendet im aktuellen Service das Feld `note`.

### 2.1.x

`ClassTransitionService`, die Sportjahr-Aktion, Trainerlistenfilter und Sortierung sind im aktuellen Administrator-/Site-Code vorhanden. Der Klassenwechsel wird über `#__jt_audit_log` nachvollziehbar gemacht.

### 2.0.x

`SelfAttendanceService` implementiert Selbstabmeldung, Fristlogik, optionale Strafe, optionales `excused` trotz verspäteter Abmeldung sowie Audit-Logging. Das Schützendashboard bindet die Funktion ein.

### 1.9–1.10

Kalender-Tabellen, mehrtägige Felder, Zielgruppen, Gruppenfilter, Kategorien und PDF-Anhänge sind vorhanden. `Athletecalendar` und `Trainercalendar` existieren weiterhin als Kompatibilitätsviews; mindestens die View-Klassen leiten zum gemeinsamen Kalender um, während ältere Templates noch im Paket liegen.

## SQL-Versionen gegenüber Releasehistorie

Vorhanden sind 23 Update-SQL-Dateien: `0.1.0`, `0.1.4`, `0.2.0`, `0.3.0`, `0.4.0`, `0.4.1`, `0.4.2`, `0.4.4`, `0.5.0`, `0.5.1`, `0.5.3`, `0.6.0`, `0.7.0`, `0.8.0`, `0.9.0`, `0.9.2`, `0.9.3`, `1.2.0`, `1.3.0`, `1.6.0`, `1.9.0`, `1.10.0` und `2.2.0`.

Nicht jedes Release benötigt SQL. Die Lücken sind deshalb nicht automatisch Fehler. Auffällig sind jedoch:

- Der Markdown-Verlauf beginnt bei 0.2.0, während SQL-Marker 0.1.0/0.1.4 existieren.
- Die sehr dichte Releasefolge wird nur selektiv durch Migrationen begleitet.
- Historische Migration `0.4.2` führt `joomla_user_id` ein, `0.4.4` zusätzlich `user_id`; eine dokumentierte Datenmigration oder Entfernung des Legacy-Feldes fehlt.
- Der Neuinstallationsstand hat mehrere Update-Indizes nicht übernommen; Details stehen in `DATABASE_AUDIT.md`.

## Inkonsistenzen

### `changelog.xml` ist nicht releaseaktuell

Der Updatefeed verweist ausdrücklich auf diese Datei, sie enthält aber nur einen Block mit Version 2.2.1. Release 2.2.2 fehlt. Außerdem vermischt ihr 2.2.1-Block Inhalte, die `CHANGELOG.md` unter 2.2.0 führt. Nutzer der Joomla-Updateansicht erhalten damit abweichende Releaseinformationen.

### `README.md` ist historisch und repetitiv

Die Überschrift nennt 1.0.1. Danach folgen viele gleichnamige Abschnitte `Version 1.0.1`, die Funktionen aus deutlich späteren Entwicklungsständen beschreiben. Aktuelle Kernfunktionen und technische Mindestversionen sind nicht verlässlich zusammengefasst.

### Datumsangaben fehlen bei den neuesten Versionen

`CHANGELOG.md` nennt für 2.2.0–2.2.2 kein Datum, obwohl ältere Einträge datiert sind. Manifest `creationDate` enthält nur `2026-07`.

### Produkt- und Technikname variieren

Der technische Name bleibt `com_jugendtraining`/`COM_JUGENDTRAINING`; Produkttexte und Updatefeed nennen BoTa. Das ist im Code absichtlich stabilisiert, sollte in Dokumentation aber eindeutig als technischer Name versus Anzeigename erklärt sein.

### Versionsmetadaten außerhalb der Releasequellen sind veraltet

Der CSS-Kommentar 0.8.2 und die README-Überschrift 1.0.1 widersprechen dem Paketstand 2.2.2. Sie beeinflussen die Laufzeit nicht, erschweren jedoch Inventarisierung und Wartung.

## Belastbarkeit des Changelogs

- **Hoch** für die Funktionsentwicklung ab 1.8: Die zentralen Features sind im aktuellen Code auffindbar.
- **Mittel** für exakte Releasezuordnung: `changelog.xml` verschiebt Inhalte zwischen 2.2.0/2.2.1, und viele Releases wurden in sehr kurzer Folge veröffentlicht.
- **Niedrig** für `README.md` als Historie: Versionsüberschrift und wiederholte Abschnittstitel sind nicht belastbar.
- **Nicht beweisbar aus dem Repository:** Ob jedes in `updates.xml` genannte GitHub-Releaseartefakt tatsächlich existiert oder exakt dem Quellstand entspricht; gemäß Auftrag wurde kein Remote-Zustand zur technischen Wahrheit erhoben.

## Zusammenfassung

Der konsistente operative Versionsstand ist 2.2.2. `CHANGELOG.md`, Manifest, Assetmanifest und Updatefeed stimmen dabei überein. Größte Dokumentationsabweichungen sind das veraltete `changelog.xml`, das historische README und stale Versionskommentare. Der SQL-Updatepfad endet nachvollziehbar bei 2.2.0, weist jedoch unabhängig vom Changelog Schemaabweichungen und Legacy-Reste auf, die im Datenbankaudit dokumentiert sind.
