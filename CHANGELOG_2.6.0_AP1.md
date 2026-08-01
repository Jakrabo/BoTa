# BoTa 2.6.0 AP1 — Dashboard and Backend Improvements

## Umfang

AP1 implementiert Recovery F auf Basis des bestehenden Joomla-MVC und des produktiven Live-Schemas. Es wurden vorhandene Models, Views, Controller, Services, Templates und Styles erweitert. Für Dashboard oder Filter wurden keine neuen PHP-Klassen angelegt.

Nachtest-Korrektur: Die AP1-Kacheln und Statusschaltflächen besitzen explizite Darkmode-Oberflächen. Die WebAsset-Version `2.6.0.1` erzwingt nach einem erneuten Update das Laden des korrigierten Stylesheets statt einer zwischengespeicherten 2.6.0-Fassung.

## Geänderte Dateien

### Trainerdashboard

- `site/src/Model/TrainerdashboardModel.php` — Abfrage heutiger, nicht abgesagter Trainings einschließlich Gruppe, Ort, Uhrzeit, erwarteter Teilnehmer und Anwesenheitsstatus.
- `site/src/Model/TrainerModel.php` — Block `today_trainings` in die bestehende Dashboardkonfiguration aufgenommen; offene Strafen bleiben explizit auf `status='open'` begrenzt.
- `site/src/View/Trainerdashboard/HtmlView.php` — heutige Trainings an das bestehende Template übergeben.
- `site/tmpl/trainerdashboard/default.php` — klickbare Kennzahlen für offen, anwesend, entschuldigt, verspätet und abwesend; Link zur zugeordneten Trainingseinheit mit Dashboard-Rücksprung.

### Trainertraining und Trainertrainings

- `site/tmpl/trainertraining/edit.php` — vorhandene Anwesenheitsfilter auf alle Status erweitert, Query-Filter vom Dashboard übernommen, Karten-/Tabellenumschaltung ergänzt und Speichern/Abbrechen/Löschen als Sticky-Aktionsleiste umgesetzt.
- `site/src/Controller/TrainertrainingController.php` — abgesicherter `return`-Rücksprung für Speichern, Abbrechen und Löschen; Dashboard-Aufruf kehrt damit zum Dashboard zurück.
- `site/src/Model/TrainertrainingsModel.php` — Ort, erwartete/erfasste Teilnehmer und Trainingsstatus in der bestehenden Listenabfrage ergänzt.
- `site/tmpl/trainertrainings/default.php` — Status-/Teilnehmeranzeige und responsives Kartenlayout mit direktem Edit-Link.
- `site/src/Service/SelfAttendanceService.php` — abgesagte Trainings von Selbstabmeldung und kommenden Trainings ausgeschlossen.
- `site/src/Service/SelfCheckinService.php` — abgesagte Trainings von Auswahl und Check-in ausgeschlossen.
- `media/css/site.css` — Tabellenmodus, mobile Trainingskarten, Sticky-Aktionsleiste, Dashboardkennzahlen und Darkmode-Regeln.

### Backend-Athleten

- `administrator/src/Model/AthletesModel.php` — Verein-/Bogenartfilter, erweiterte Suche über Name, Verein, Klasse und Trainingsgruppe, Gruppenaggregation und Sortierfelder.
- `administrator/src/View/Athletes/HtmlView.php` — vorhandene Vereine und Bogenarten an das Template übergeben.
- `administrator/tmpl/athletes/default.php` — Filterfelder, Trainingsgruppen-Spalte und Sortierung nach Name, Verein und Bogenart.

### Backend-Trainings

- `administrator/src/Model/TrainingsModel.php` — Filter für Trainingsgruppe, Trainingsort, Status und Datumsbereich sowie Ort-/Statusdaten ergänzt.
- `administrator/src/View/Trainings/HtmlView.php` — Gruppen und Orte an das Template übergeben.
- `administrator/tmpl/trainings/default.php` — neue Filter und Datumssortierung auf-/absteigend.

### Schema und Version

- `administrator/sql/install.mysql.utf8mb4.sql` — Recovery-F-Felder und Indizes des Live-Schemas an `#__jt_training_sessions` ergänzt.
- `administrator/sql/updates/mysql/2.6.0.sql` — additiver Updatepfad von 2.2.3 auf die benötigten Session-Felder.
- `com_jugendtraining.xml` — Version 2.6.0 und Erstellungsmonat synchronisiert.
- `media/joomla.asset.json` — Assetversion 2.6.0.
- `updates.xml`, `changelog.xml`, `CHANGELOG.md` — Versions- und Funktionsstand 2.6.0 synchronisiert.
- `site/language/de-DE/com_jugendtraining.ini`, `site/language/en-GB/com_jugendtraining.ini`, `administrator/language/de-DE/com_jugendtraining.ini`, `administrator/language/en-GB/com_jugendtraining.ini` — AP1-Sprachschlüssel ergänzt.

## Neue Sprachschlüssel

Alle Schlüssel wurden auf Site und Administrator in Deutsch und Englisch ergänzt:

- `COM_JUGENDTRAINING_TODAY_TRAININGS`
- `COM_JUGENDTRAINING_EXPECTED_PARTICIPANTS`
- `COM_JUGENDTRAINING_NO_TRAININGS_TODAY`
- `COM_JUGENDTRAINING_TRAINING_UNIT`
- `COM_JUGENDTRAINING_VIEW_MODE`
- `COM_JUGENDTRAINING_CARD_VIEW`
- `COM_JUGENDTRAINING_TABLE_VIEW`
- `COM_JUGENDTRAINING_PARTICIPANTS`
- `COM_JUGENDTRAINING_STATUS_CANCELLED`
- `COM_JUGENDTRAINING_STATUS_PLANNED`
- `COM_JUGENDTRAINING_SEARCH_ATHLETES_EXTENDED`
- `COM_JUGENDTRAINING_FILTER_ALL_CLUBS`
- `COM_JUGENDTRAINING_FILTER_ALL_BOW_TYPES`
- `COM_JUGENDTRAINING_ALL_TRAINING_LOCATIONS`

## SQL-Änderungen

`#__jt_training_sessions` erhält entsprechend der Live-Datenbank:

- `training_unit_id int unsigned NULL`
- `cancelled tinyint NOT NULL DEFAULT 0`
- `cancelled_at datetime NULL`
- `cancelled_by int unsigned NOT NULL DEFAULT 0`
- `cancellation_reason text NULL`
- Index `idx_jt_training_cancelled (cancelled)`
- Index `idx_jt_training_unit (training_unit_id)`

Die Migration ist ausschließlich additiv. Es werden keine Tabellen oder Spalten gelöscht, ersetzt oder umbenannt und keine Daten verändert.

## Statische Prüfungen

- `xmllint` für Manifest, Changelog, Updatefeed und beide Trainingsformulare: erfolgreich.
- JSON-Parsing von `media/joomla.asset.json`: erfolgreich.
- `git diff --check`: erfolgreich.
- Vollständigkeit der 14 neuen Sprachschlüssel in DE/EN und Administrator/Site: erfolgreich.
- Abgleich der fünf Session-Spalten und zwei Indizes zwischen Installations- und Update-SQL: erfolgreich.
- PHP-CLI-Lint konnte nicht ausgeführt werden, da in der Arbeitsumgebung keine PHP-Runtime installiert ist.

## Funktionstestfälle

1. **Update:** Eine Kopie einer 2.2.3-Datenbank aktualisieren; Joomla-Schemastatus muss 2.6.0 anzeigen und alle fünf Spalten/zwei Indizes müssen vorhanden sein.
2. **Neuinstallation:** Komponente neu installieren und Session-Schema mit dem Updateergebnis vergleichen.
3. **Dashboard heute:** Trainer mit mehreren heutigen Gruppen-/Eigentrainings anmelden; Gruppe, Ort, Uhrzeit und alle sechs Kennzahlen prüfen.
4. **Dashboard Berechtigungen:** Training einer fremden Trainergruppe darf nicht erscheinen.
5. **Dashboard Stornierung:** `cancelled=1` darf nicht unter „Heutige Trainings“ erscheinen.
6. **Anwesenheitszahlen:** Gruppe mit offenen, anwesenden, entschuldigten, verspäteten und abwesenden Athleten vorbereiten; Zähler gegen Rohdaten prüfen.
7. **Filterlinks:** Jede Kennzahl anklicken; `trainertraining&layout=edit&id=...` muss mit dem passenden aktiven Filter öffnen.
8. **Rücksprung:** Vom Dashboard geöffnetes Training speichern, abbrechen und löschen; alle drei Wege müssen zum Trainerdashboard zurückführen.
9. **Trainingseinheit:** Session mit `training_unit_id` zeigt Button und öffnet `trainertrainingunit&id=...` mit Dashboard-Rücksprungparameter.
10. **Strafregister:** Einen offenen und einen erledigten Straffall anlegen; Dashboardzahl und Liste dürfen nur den offenen Fall enthalten.
11. **Anwesenheitsansichten:** Karten-/Tabellenumschaltung testen, Status per ✅/❌/⏰/☝️/◯ speichern und Reload durchführen.
12. **Sticky-Aktionen:** Auf Smartphonebreite scrollen; Speichern, Abbrechen und Löschen bleiben am unteren Rand erreichbar.
13. **Trainertrainings mobil:** Liste auf Smartphonebreite prüfen; jede Karte zeigt Datum, Uhrzeit, Gruppe, Ort, Teilnehmer, Status und Edit-Link.
14. **Self-Service:** Abgesagtes Training darf weder im Self-Check-in noch in der Selbstabmeldung angeboten werden.
15. **Athletensuche:** Treffer jeweils über Name, Verein, Klasse und Trainingsgruppe prüfen.
16. **Athletenfilter/-sortierung:** Verein und Bogenart einzeln/kombiniert filtern; Name, Verein und Bogenart auf-/absteigend sortieren; Gruppen-Spalte prüfen.
17. **Trainingsfilter:** Gruppe, Ort, geplant/abgesagt/unveröffentlicht und Von-/Bis-Datum einzeln sowie kombiniert prüfen.
18. **Trainingssortierung:** Datum auf- und absteigend sortieren und Pagination/Filterzustand kontrollieren.
19. **Darkmode:** Dashboardmetriken, Anwesenheitskarten/-tabelle, Sticky-Leiste und mobile Trainingskarten in Hell/Dunkel/Auto prüfen.
20. **Regression:** Training neu anlegen, Serie erzeugen, Anwesenheit per Autosave und Gesamtspeichern ändern sowie bestehendes Training löschen.
