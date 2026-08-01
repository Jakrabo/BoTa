## 2.6.2

- Komponentenstylesheet wird zusätzlich zur WebAsset-Registry zentral und versioniert geladen.
- Theme-Resolver setzt synchron `data-bota-theme-resolved` und `data-bs-theme`.
- Cassiopeia-/Bootstrap-Variablen für Body, Cards, Borders, Dropdowns, List-Groups, Tabs und Oberflächen an die BoTa-Tokens gekoppelt.
- Light- und Dark-Kontraste für Cards, Formulare, Tabellen, Dropdowns und Buttons gegen das produktive Cassiopeia-CSS geprüft.

## 2.6.1

- Zentrales CSS-Variablesystem für Hell, Dunkel und Auto eingeführt.
- Direkte Farbwerte außerhalb der zentralen Token-Deklarationen entfernt.
- View-spezifische Darkmode-Hacks durch gemeinsame Komponentenoberflächen ersetzt.
- Cards, Tabellen, Formulare, Dropdowns, Modals, Tabs, Pagination, Alerts, Badges und Charts an das Designsystem angebunden.
- Backend-Styles an die nativen Joomla-Administratorvariablen angebunden.

## 2.6.0

- Trainerdashboard um heutige Trainings, verlinkte Anwesenheitsfilter und Trainingseinheiten-Link erweitert.
- Mobile Karten-/Tabellenumschaltung und Sticky-Aktionen in der Trainer-Anwesenheit ergänzt.
- Mobile Trainingsliste als Kartenlayout umgesetzt.
- Athleten- und Trainingsfilter im Backend erweitert.
- Live-Schemafelder für Trainingseinheiten-Zuordnung und Stornierungsstatus synchronisiert.
- Darkmode-Oberflächen der Dashboard-, Anwesenheits- und mobilen Trainingskacheln vervollständigt.
- Didaktisches Trainingseinheiten-Raster mit responsiver Detail- und Bearbeitungsansicht wiederhergestellt.
- Dashboard-Zähler für offene Strafen und sichtbare Kalendertermine korrigiert.
- Karten-/Tabellenumschaltung der Anwesenheit stabilisiert und gemeinsame Darkmode-Oberflächen ergänzt.

## 2.2.3

- Recovery-Version zur Synchronisierung von Manifest, Updatefeed, Changelog und WebAsset-Version.
- Fehlende ACL- und Konfigurations-Sprachschlüssel ergänzt.
- Neuinstallationsschema mit den vorhandenen Update-Indizes synchronisiert.
- Deinstallationsschema um bislang nicht entfernte Komponententabellen vervollständigt.
- Administrator-WebAssets werden über den WebAssetManager geladen.

## 2.2.2
- GPS-Check-in-JavaScript wird in der Self-Check-in-View direkt geladen und ist damit nicht mehr von einer möglicherweise veralteten Joomla-WebAsset-Registry abhängig.
- Behebt den Fall, dass beim Klick auf „Per GPS einchecken“ keinerlei Reaktion erfolgt.
- Vorhandene HTTPS-, Berechtigungs-, Positions- und Timeout-Fehlermeldungen bleiben erhalten.

## 2.2.1
- GPS-Check-in robuster angebunden und mit verständlichen Browser-/HTTPS-/Berechtigungsfehlern versehen.
- Check-in-Vorlauf konfigurierbar (Standard 60 Minuten).
- Check-in endet bei vorhandener Trainings-Endzeit mit der Endzeit.
- Ohne Endzeit gilt eine konfigurierbare Check-in-Dauer ab Trainingsbeginn (Standard 120 Minuten).

# Changelog

## 2.2.0

- GPS Self-Check-in für Schützen mit konfigurierbarem Geofencing und ohne Speicherung der GPS-Position.
- Trainingsorte als neue Stammdaten mit Koordinaten; Trainings referenzieren einen Trainingsort.
- Trainingsprogramme können demselben Schützen mehrfach als getrennte Aufgaben zugewiesen werden; Fortschritt bleibt je Zuweisung getrennt.
- Prüfstatus-Fallback für Altdaten und Bereinigung ungültiger/leerer Prüfstatuswerte.
- Neuinstallationsschema mit bisher fehlenden Feldern synchronisiert.
- Achievement-CSV-Import korrigiert (`note` statt `notes`).

## 2.1.1 — 2026-07-29

- Namenssortierung in `view=trainerathletes` alphabetisch nach Vorname und Nachname korrigiert
- fehlende Übersetzungen für Ergebnis-Prüfstatus ergänzt
- neue Trainingstagebuch-Einträge erhalten automatisch das aktuelle Datum in Joomla-Zeitzone
- Achievements ohne Bild erhalten ein einheitliches Stern-Fallback im Schützen-, Trainer- und Backendbereich

## 2.1.0 — 2026-07-29

- automatischer Klassenwechsel beim Wechsel des aktuellen Sportjahres
- Klassen werden anhand von Sportjahr, Geburtsjahr, Geschlecht und den konfigurierten Altersgrenzen neu zugeordnet
- automatische Klassenumstellung wird je Sportjahr nur einmal ausgeführt und im Audit-Log protokolliert
- Sportjahresübersicht erhält eine manuelle Funktion zum erneuten Berechnen der Klassen
- `view=trainertrainings` zeigt standardmäßig die kommenden 14 Tage
- Filter für Zeitraum und Trainingsgruppe in der Trainer-Trainingsübersicht
- `view=trainerathletes` erhält sortierbare Spaltenüberschriften
- Backend-Titel werden explizit übersetzt, damit keine `COM_JUGENDTRAINING_*`-Platzhalter mehr angezeigt werden

## 2.0.3 — 2026-07-28

- Warnhinweis bei verspäteter Abmeldung im Schützendashboard entfernt
- bei aktivierter automatischer Strafe bleibt der normale Abmeldebutton nach Fristablauf sichtbar
- neue Backend-Option: verspätete Abmeldung trotz Strafe als `excused` übernehmen
- neue Option ist standardmäßig aktiviert
- bei deaktivierter Option wird nur die Strafe erzeugt und Attendance bleibt unverändert
- erfolgreiche verspätete Abmeldungen werden gesondert im Audit-Log protokolliert

## 2.0.2 — 2026-07-28

- reine Datumsfelder werden nicht mehr über `user_utc` in UTC umgerechnet
- behebt den Sprung eines ausgewählten Tages auf den Vortag bei Zeitzone Europe/Berlin
- Korrektur gilt konsistent für Trainings-, Ergebnis-, Tagebuch-, Ziel-, Notiz-, Programm- und Geburtsdatumsfelder
- bei aktivierter automatischer Strafe bleibt der Abmeldebutton auch nach Ablauf der Frist sichtbar
- verspäteter Klick ändert Attendance weiterhin nicht, kann aber einmalig die konfigurierte Strafe erzeugen
- Warn- und Bestätigungstexte für verspätete Abmeldeversuche ergänzt

## 2.0.1 — 2026-07-28

- Backend-Fehler bei Training → Neu behoben
- `getCurrentSessionId()` liefert bei neuen Trainings jetzt sicher `0` statt `null`
- `getCurrentGroupId()` ruft bei neuen Datensätzen nicht mehr unnötig `getItem(0)` auf

## 2.0.0 — 2026-07-28

- Schützen können sich selbst von kommenden Trainings abmelden
- Schützen dürfen serverseitig ausschließlich ihren eigenen Attendance-Status auf `excused` setzen
- Standard-Abmeldeschluss 60 Minuten vor Trainingsbeginn, im Backend konfigurierbar
- Trainingszuordnung wird über die Trainingsgruppe serverseitig geprüft
- neue konfigurierbare Dashboard-Kachel „Trainings – Abmeldung“
- nach Ablauf der Frist ist die Selbstabmeldung gesperrt
- optional kann ein verspäteter Abmeldeversuch einmalig eine konfigurierte Strafe erzeugen
- Selbstabmeldungen und verspätete Versuche werden im Audit-Log protokolliert
- Trainer können Attendance weiterhin korrigieren

## 1.10.10 — 2026-07-28

- Benutzer-Theme wird jetzt über data-bota-theme-resolved auch tatsächlich auf das Layout angewendet
- Cassiopeia Header und Navigation für Hell/Dunkel überschrieben
- Cards, Formulare, Selects, Tabellen und Modals an die Benutzerwahl gekoppelt
- Diagrammtexte, Raster, Balken und Ringdurchschnitt an den gewählten Modus gekoppelt
- expliziter Hellmodus kann jetzt auch einen dunklen Betriebssystemmodus überschreiben
- expliziter Dunkelmodus funktioniert unabhängig von prefers-color-scheme

## 1.10.9 — 2026-07-28

- Theme-Anwendung vollständig von Joomla WebAsset-JavaScript entkoppelt
- Auto / Hell / Dunkel wird direkt im Dokumentkopf gesetzt
- externer Theme-JavaScript-Asset nicht mehr erforderlich
- Benutzerparameter und bota_theme-Cookie bleiben unverändert
- behebt den Fall, dass data-bota-theme-resolved trotz korrektem Cookie nicht gesetzt wurde

## 1.10.8 — 2026-07-28

- Theme-Übertragung von Joomla scriptOptions auf First-Party-Cookie umgestellt
- `bota_theme` enthält ausschließlich auto / light / dark
- Cookie wird bei jeder BoTa-Anfrage aus dem Joomla-Benutzerparameter synchronisiert
- Cookie wird direkt nach dem Speichern der Benutzerparameter aktualisiert
- Theme-JavaScript ist vollständig unabhängig von Joomla.getOptions und dessen Ladezeitpunkt
- Auto-Modus folgt weiterhin prefers-color-scheme

## 1.10.7 — 2026-07-28

- Theme-Script wird jetzt mit `defer` geladen
- Joomla-Scriptoptionen werden erst bei der Theme-Anwendung gelesen
- verhindert den Fallback auf `auto`, wenn Joomla.getOptions beim Parsen noch nicht verfügbar ist
- Auto / Hell / Dunkel reagieren jetzt auf den gespeicherten Benutzerparameter

## 1.10.6 — 2026-07-28

- Theme-Auswahl Auto / Hell / Dunkel liest jetzt direkt Joomla scriptOptions
- Race-Condition zwischen Inline-Attribut und Theme-JavaScript entfernt
- erzwungener Hell- bzw. Dunkelmodus funktioniert unabhängig von der Systemeinstellung
- Auto-Modus folgt weiterhin dynamisch prefers-color-scheme
- Asset-Version angehoben, damit Browser und Joomla die korrigierte Theme-JS neu laden

## 1.10.5 — 2026-07-28

- persönliche Darstellung Auto / Hell / Dunkel ergänzt
- Auswahl wird direkt in den Joomla-Benutzerparametern als `bota_theme` gespeichert
- Benutzerparameter-Frontendansicht und Menütyp ergänzt
- Darstellungslink in Schützen- und Trainerdashboard ergänzt
- BoTa-Formulare, Tabellen, Karten und Diagramme auf erzwungenen Darkmode vorbereitet
- Auto-Modus reagiert auf Änderungen der Betriebssystemeinstellung

## 1.10.4 — 2026-07-28

- Testrelease für die Joomla-Updatefunktion
- keine funktionalen Änderungen gegenüber 1.10.3

## 1.10.3 — 2026-07-28

- einzelnen Kalender-Link im Schützendashboard entfernt
- Kalender als konfigurierbare Dashboard-Kachel ergänzt
- Kachel zeigt die nächsten drei für den Schützen sichtbaren Termine
- Kategorie-Farben werden auch in der Dashboard-Kachel verwendet
- Position und Sichtbarkeit unter Konfiguration → Schützendashboard steuerbar

## 1.10.2 — 2026-07-28

- rollenabhängiges Login-Ziel ergänzt
- Trainer werden nach Login auf das Trainerdashboard geleitet
- Schützen werden nach Login auf das Schützendashboard geleitet
- sonstige Benutzer fallen auf die Startseite zurück
- token-geschützte Logout-Task mit Weiterleitung auf Home ergänzt
- Joomla-Menütyp „Login-Ziel BoTa“ ergänzt

## 1.10.1 — 2026-07-28

- fehlenden Import `Joomla\\CMS\\Router\\Route` im Schützendashboard ergänzt
- alle Frontend-Templates auf unqualifizierte `Route::_()`-Aufrufe geprüft

## 1.10.0 — 2026-07-28

- gemeinsamer Frontend-Kalender für Schützen und Trainer
- mehrtägige Termine mit Start-/Enddatum und Start-/Endzeit
- trainerinterne Termine, optional auf eine Trainingsgruppe beschränkt
- serverseitige Sichtbarkeitsprüfung für trainerinterne und gruppenbezogene Termine sowie PDF-Anhänge
- Kategorien mit konfigurierbarer Farbe und Aktivstatus
- Kategorie-Badges verwenden die konfigurierte Farbe und automatische Kontrastschrift
- Datumsfilter berücksichtigt Überschneidungen mehrtägiger Veranstaltungen
- alte athletecalendar-/trainercalendar-Views leiten auf den gemeinsamen Kalender weiter

## 1.9.1 — 2026-07-28

- Backend-Kalender akzeptiert leere Von-/Bis-Filter ohne TypeError
- Frontend-Kalenderfilter ebenfalls defensiv gegen leere Datumswerte abgesichert
- Joomla-Menütypen für Schützenkalender und Trainerkalender ergänzt
- Menütyp-Sprachwerte in Deutsch und Englisch ergänzt

## 1.9.0 — 2026-07-28

- Saisonkalender im Frontend für Trainer und Schützen
- Kalenderverwaltung im Joomla-Backend
- Filter nach Zeitraum, Zukunft/Vergangenheit, Kategorie und Ort
- Mehrere PDF-Anhänge pro Termin mit geschützter Datenbankablage
- Kalender-Kategorien in der Konfiguration pflegbar
- fehlende Backend-Sprachplatzhalter ergänzt
- aktuelles Sportjahr wird bei Komponentenaufruf automatisch anhand des Datums synchronisiert
- GitHub-Updatequelle und update.xml/changelog.xml ergänzt

## 1.8.6 — 2026-07-28

- Anwesenheitsstatus in der Trainer-Schützendetailseite übersetzt
- Objektberechtigungen für Trainingstagebuch, Bogensetup und Trainingsaufgaben gehärtet
- Backend-Zugriff explizit mit Joomla-ACL abgesichert
- Eingabevalidierung für Kontaktdaten, Notizen, Strafen und Sprachwerte erweitert
- Badge-Uploads stärker validiert und optional sicher neu kodiert
- sensible Traineransichten gegen Browser-/Proxy-Caching abgesichert
- Sicherheits- und Datenschutzprüfung als SECURITY_AUDIT.md dokumentiert

## 1.8.5 — 2026-07-28

- Strafen-Kachel in der Trainer-Schützendetailseite dauerhaft sichtbar
- Detailseite zeigt ausschließlich offene Strafen des Schützen
- Anzahl offener Strafen als Badge ergänzt
- Button „Strafe hinzufügen“ ergänzt
- Schütze wird im Strafregister beim Aufruf aus der Detailseite vorausgewählt

## 1.8.4 — 2026-07-28

- SVG-Farben des Ergebnisdiagramms direkt an den Elementen gesetzt
- Gesamtringe werden unabhängig vom Template blau dargestellt
- Ringdurchschnitt wird unabhängig vom CSS als orange-rote Linie dargestellt
- orange-rote Datenpunkte und grafische Legende ergänzt
- Darstellung ist nicht mehr von Joomla-CSS-Variablen oder CSS-Klassen abhängig

## 1.8.3 — 2026-07-28

- Ergebnisbalken im Trainer-Schützendetail explizit blau eingefärbt
- Ringdurchschnitt als deutlich sichtbare orange-rote Linie dargestellt
- Durchschnittspunkte farblich an die Linie angepasst
- Diagrammfarben von Joomla-Templatevariablen entkoppelt
- Zeichenreihenfolge der Durchschnittslinie korrigiert

## 1.8.2 — 2026-07-28

- Ringdurchschnittslinie in der Trainer-Schützendetailseite sichtbar gemacht
- explizite CSS-Klasse für Linie und Datenpunkte ergänzt
- Ringdurchschnitt auf Skala 0–10 begrenzt und Punkte mit Tooltip ergänzt
- Ergebnisdiagramm wird vor dem Rendern sauber neu aufgebaut

## 1.8.1 — 2026-07-28

- technischer Manifestname wieder auf COM_JUGENDTRAINING stabilisiert
- sichtbarer Komponentenname bleibt über die Sprachdateien BoTa
- veraltete und doppelte Administrator-Menüeinträge werden vor dem Update bereinigt
- Fehler beim Erstellen des Joomla-Administratormenüs behoben

## 1.8.0 — 2026-07-28

- sichtbarer Komponentenname auf BoTa geändert
- Duplicate-Key-Fehler bei erneuter Programmzuordnung behoben
- Achievement-Auszug im Trainer-Cockpit als drei Bootstrap-Kacheln umgesetzt
- Kurzzeitfilter der Trainer-Schützendetailseite korrigiert
- Schützennamen in Trainer-Ampel und Klassenwechsel verlinkt

## 1.7.0 — 2026-07-28

- Achievement-Cockpit zeigt einen Auszug von maximal sechs Kacheln
- Notizen aus der Schützendetailseite anlegbar und bearbeitbar
- Trainingsaufgaben aus der Detailseite anlegbar und bearbeitbar
- Schützendaten durch zugeordnete Trainer bearbeitbar
- Leistungs- und Pfeildiagramm der Trainerdetailseite vervollständigt
- Zeitraumfilter um letzte Woche und letzten Monat erweitert
- Sprachplatzhalter COM_JUGENDTRAINING_TRAINING und Filterbeschriftung ergänzt
- Backend-Konfiguration um Sprachübersicht und Joomla-Overrides erweitert

## 1.6.0 — 2026-07-28

- Trainer-Notizen mit Anlage, Gruppen-/Schützenfilter und Status ergänzt
- Standardfilter für aktuelle Notizen ergänzt
- Achievement-Cockpit als responsives Kachelraster umgesetzt
- Schützenliste mit Trainer-Detailansicht verknüpft
- Detailansicht mit Kontakt, Teilnahmen, Notizen, Aufgaben und Diagrammen ergänzt
- Tagebuchstatistik an den gemeinsamen Zeitraumfilter angebunden

## 1.5.0 — 2026-07-28

- Achievement-Vorschau als drei Kacheln und auf die letzten drei Erfolge begrenzt
- Reihenfolge und Sichtbarkeit des Schützen- und Trainerdashboards konfigurierbar
- Zeitraumfilter wirkt auf Ergebnis- und Pfeildiagramm
- Trainingsgruppen im Trainerdashboard mit gefilterter Schützenansicht verlinkt

## 1.4.0 — 2026-07-28

- Diagramm für Pfeile pro Monat ergänzt
- Umschaltung auf Pfeile pro Kalenderwoche ergänzt
- Zeitraumfilter mit letzten 12 Monaten als Standard ergänzt
- vorhandene Sportjahre als auswählbare Zeiträume ergänzt
- fehlende Monate und Kalenderwochen werden mit 0 Pfeilen dargestellt

## 1.3.0 — 2026-07-28

- abgeschlossene Trainingsprogramme werden 14 Tage nach Abschluss aus dem Schützendashboard ausgeblendet
- offene Strafen in Schützen- und Trainerdashboard ergänzt
- monetäre Strafbilanz und Reset-Zeitpunkt ergänzt
- Tagebuchstatistiken in der Leistungsentwicklung ergänzt
- Badge-Grafiken im Dashboard hart auf 48 × 48 Pixel begrenzt

## 1.2.0 — 2026-07-28

- Strafdefinitionen im Konfigurationstab ergänzt
- monetäre und nichtmonetäre Strafen ergänzt
- Frontend-Strafregister für Trainer ergänzt
- Berechtigungsprüfung auf zugeordnete Trainingsgruppen ergänzt
- offene und erledigte Registereinträge ergänzt
- Achievement-Vorschau im Schützen-Dashboard verkleinert

## 1.1.1 — 2026-07-28

- fehlende Sprachkonstante COM_JUGENDTRAINING_SELECT_OPTION ergänzt
- Backend-Menüpunkt in „Konfiguration“ umbenannt
- Konfigurationsseite in die Tabs „CSV-Import“ und „Tagebuch“ gegliedert
- zuletzt verwendeter Tab wird innerhalb der Sitzung beibehalten

## 1.1.0 — 2026-07-28

- CSV-Import-Cockpit ergänzt
- Vorlagen für Ergebnisse, Tagebuch und Achievements ergänzt
- konfigurierbare Tagebuch-Dropdowns ergänzt
- aktives Bogensetup als Standard ergänzt

## 1.0.2 — 2026-07-28

- integriertes Hilfesystem vollständig entfernt
- CSV-Export für Tagebuchdaten und Ergebnisse ergänzt
- rollenbasierte Exporte für Schützen und Trainer ergänzt
- UTF-8-BOM, deutsches Zahlenformat und CSV-Injection-Schutz ergänzt

## 1.0.1 — 2026-07-27

- Fehler beim Öffnen der Backend-Hilfe ohne article-Parameter behoben
- HelpService null-sicher gemacht

## 1.0.0 — 2026-07-27

- rollenabhängiges Hilfecenter im Frontend und Backend ergänzt
- Markdown-Dokumentation, Suche, Navigation und Druckansicht ergänzt
- Schützen-, Trainer-, Backend-Trainer- und Administratorhandbuch erstellt

## 0.9.5 — 2026-07-27

- touch-optimierten Trainer-Modus für Anwesenheit ergänzt
- Dropdowns durch große Statusflächen ersetzt
- Autosave-Endpunkt und AJAX-Speicherung ergänzt
- Filter, Statusfarben und einklappbare Kommentare ergänzt

## 0.9.4 — 2026-07-27

- Anwesenheitserfassung in das Trainer-Frontend übernommen
- Status, Kommentare und Sammelaktion ergänzt
- Speichern in #__jt_attendance ergänzt
- mobile Anwesenheitsansicht ergänzt

## 0.9.3 — 2026-07-27

- Badge-Upload ohne Joomla Filesystem Folder/File umgesetzt
- Badge-Speicherort auf Joomla /images verschoben
- Auswahl vorhandener Badge-PNGs ergänzt
- Uploadlimit auf 3 MB erhöht

## 0.9.2 — 2026-07-27

- bestätigte Ergebnisse für Wettkampf-Achievements verpflichtend gemacht
- Achievement-Verwaltung im Backend und Trainer-Frontend ergänzt
- Kriterieneditor und PNG-Upload ergänzt

## 0.9.1 — 2026-07-27

- Speichern leerer optionaler Temperatur- und Windfelder korrigiert
- leere Dezimalfelder werden als NULL gespeichert
- deutsches Dezimalkomma wird normalisiert

## 0.9.0 — 2026-07-27

- Achievement-System ergänzt
- automatische und manuelle Vergabe ergänzt
- PNG-Badges und Schützen-Galerie ergänzt
- Trainer-Cockpit mit Vergabe und Widerruf ergänzt

## 0.8.2 — 2026-07-27

- Frontend-CSS vollständig neu aufgebaut
- Fabrik-spezifische Altlasten entfernt
- modernes responsives Jugend- und Trainerdesign ergänzt
- Farben, Cards, Tabellen, Formulare, Buttons und Ampeln vereinheitlicht

## 0.8.1 — 2026-07-27

- fehlende Frontend-Sprachschlüssel korrigiert
- sichtbare Ampelfarben im Trainer-Dashboard korrigiert
- Frontend-Styles explizit geladen und Asset-Cache erneuert

## 0.8.0 — 2026-07-27

- Trainer-Ampeldashboard ergänzt
- Klassenwechselvorschau für das nächste Sportjahr ergänzt
- Statistik- und Korrelationsmodul ergänzt
- Wetterdaten an Ergebnissen ergänzt
- Setup-, Pfeilschaft-, Standhöhen- sowie Visier-/Wetterauswertung ergänzt

## 0.7.2 — 2026-07-27

- Language-Cleanup für Frontend und Formulare durchgeführt
- Joomla-Core-Schlüssel JEDIT, JDELETE, JSAVE und JCANCEL aus sichtbaren Komponentenansichten entfernt
- einheitliche Komponenten-Schlüssel für Aktionen, Spalten und Status ergänzt
- Löschbestätigungen vereinheitlicht

## 0.7.1 — 2026-07-27

- dynamische Visiereinstellungszeilen ergänzt
- Setupformular nach Komponenten strukturiert
- optische Trennung verbessert

## 0.7.0 — 2026-07-27

- Setup-Revisionssystem ergänzt
- Visiertabelle je Setup ergänzt
- Setups mit Ergebnissen verknüpft
- Trainingstagebuch für Schützen und Trainer ergänzt

## 0.6.5 — 2026-07-27

- Frontend-Sprachschlüssel für Trainingsformular vervollständigt
- Trainingstitel und Formularfelder korrekt übersetzt
- Komponenten-Sprache im Trainerformular explizit geladen

## 0.6.4 — 2026-07-27

- Trainingsstruktur vollständig auf Trainingsgruppen umgestellt
- gruppenbasierte Teilnehmerlisten und Anwesenheitsprüfung ergänzt
- Frontend-CRUD für Trainings der eigenen Trainergruppen ergänzt

## 0.6.3 — 2026-07-27

- altes Säulendiagramm aus der Schützen-Gesamtübersicht entfernt
- kombinierte Leistungsdarstellung mit Ringdurchschnittslinie beibehalten

## 0.6.2 — 2026-07-27

- fehlende Modelle für modulare Frontend-Menütypen ergänzt
- Null-Zuweisungen an typisierte Array-Eigenschaften verhindert
- Schützen- und Traineransichten gegen leere Ergebnisse abgesichert

## 0.6.1 — 2026-07-27

- Installationsfehler bei der automatischen Benutzergruppenerstellung behoben
- nicht verfügbare Methode Usergroup::setLocation() entfernt
- Joomla-Nested-Set für Benutzergruppen korrekt aktualisiert

## 0.6.0 — 2026-07-27

- Trainer-Frontend und Joomla-Rollen ergänzt
- Trainingsgruppen und gruppenbasierte Datenabgrenzung ergänzt
- modulare Frontend-Menütypen ergänzt
- Ringdurchschnittslinie im Leistungsdiagramm ergänzt

## 0.5.5 — 2026-07-27

- Administrator-Sprachdateien Joomla-konform eingebunden
- fehlende Backend-Titelübersetzungen ergänzt
- Toolbar-Titel verwenden explizite Text-Übersetzung

## 0.5.4 — 2026-07-27

- Sichtbarkeit von GoalsModel::getItems() korrigiert und 500-Fehler behoben
- Backend-Übersetzungen für Listen, Module und Toolbar-Titel vervollständigt
- Administrator-Sprachdateien im Manifest ausdrücklich aufgenommen

## 0.5.3 — 2026-07-27

- Zielmetriken automatisiert
- Anwesenheit, Bestleistung, Ringdurchschnitt und Programmfortschritt als Datenquellen ergänzt
- integrierte Metrik-Dokumentation im Ziele-Modul ergänzt
- automatische und manuelle Berechnung auswählbar
- Zielabschluss bei erreichtem Sollwert automatisiert

## 0.5.2 — 2026-07-27

- Fehler beim Speichern leerer Dezimal- und Zahlenfelder behoben
- optionale Zielwerte serverseitig normalisiert
- leere Datumswerte werden als NULL gespeichert
- fehlenden Toolbar-Sprachtext ergänzt

## 0.5.1 — 2026-07-27

- Leistungsentwicklung im Frontend ergänzt
- persönliche Bestleistungen ergänzt
- Ziele und Fortschrittsanzeige ergänzt
- Trainernotizen mit Sichtbarkeit ergänzt

## 0.5.0 — 2026-07-27

- Übungskatalog ergänzt
- Trainingsprogramme und Athletenzuordnung ergänzt
- Frontend-Fortschrittsverwaltung ergänzt
- fünf neue Datenbanktabellen ergänzt

## 0.4.6 — 2026-07-27

- fehlende Übersetzungen für Frontend-Ergebnisse ergänzt
- Sprachschlüssel für Formularfelder, Ereignisarten und Tabellenüberschriften ergänzt
- Site-Sprachordner im Installationsmanifest abgesichert

## 0.4.5 — 2026-07-27

- `site/forms` in das Joomla-Installationsmanifest aufgenommen
- Fehler `Form::loadForm could not load file` behoben

## 0.4.4 — 2026-07-27

- falsche Abfrage auf `joomla_user_id` korrigiert
- Frontend verwendet nun das vorhandene Athletenfeld `user_id`
- Berechtigungsprüfung für eigene Ergebnisse korrigiert

## 0.4.3 — 2026-07-27

- Joomla-FormController-Berechtigung für Frontend-Ergebnisse ergänzt
- angemeldete Benutzer mit zugeordnetem Athleten dürfen Ergebnisse anlegen
- Bearbeitung weiterhin ausschließlich für eigene Ergebnisse erlaubt

## 0.4.2 — 2026-07-27

- fehlende Spalte `joomla_user_id` in `#__jt_athletes` per Migration ergänzt
- Index für Joomla-Benutzerzuordnung ergänzt
- Frontend-Ergebnisansichten gegen unvollständige Altinstallationen abgesichert

## 0.4.1 — 2026-07-27

- Frontend-Ergebnisverwaltung für Athleten ergänzt
- serverseitige Eigentumsprüfung für Anzeigen, Speichern und Löschen ergänzt
- Prüfstatus für Ergebnisse ergänzt
- Backend-Freigabe durch Trainer oder Administrator ergänzt

## 0.4.0 — 2026-07-27

- Ergebnisverwaltung ergänzt
- Filter und Suche ergänzt
- Durchschnitt je Pfeil wird automatisch berechnet
- Datenbankmigration für Ergebnisse ergänzt

## 0.3.2 — 2026-07-27

- Trainingsformular vollständig neu aufgebaut
- Serienfelder sicher in das gerenderte Feldset `details` aufgenommen
- Joomla-ShowOn-Skript für bedingte Felder eingebunden

## 0.3.1 — 2026-07-27

- Serientermine ergänzt
- Intervall in Tagen frei wählbar
- 2 bis 100 Termine möglich

## 0.3.0 — 2026-07-27

- Trainingsverwaltung mit Liste und Bearbeitungsformular ergänzt
- Anwesenheitserfassung je Training und Athlet ergänzt
- Status anwesend, verspätet, entschuldigt und unentschuldigt ergänzt
- Massenaktion „Alle anwesend“ ergänzt
- Dashboard um kommende Trainings erweitert
- Datenbankmigration für Trainings- und Anwesenheitstabellen ergänzt

## 0.2.5 — 2026-07-27

- Frontend-Menüeintragstyp `Meine Daten` ergänzt
- fehlende Layout-Metadaten unter `site/tmpl/dashboard/default.xml` ergänzt
- deutsche und englische Sprachtexte für den Menüeintrag hinzugefügt

## 0.2.4 — 2026-07-27

- Sportjahrabfrage an das reale Schema mit `date_end` angepasst
- Altersklassenabfrage auf `min_age` und `max_age` korrigiert
- automatische Klassenzuordnung im Formular und beim Speichern repariert
- fehlende Altersobergrenze wird als unbegrenzt behandelt

## 0.2.3 — 2026-07-27

- PHP-Fatal-Error im Athletenformular durch doppelten Factory-Import behoben
- Datenbankzugriff auf DatabaseInterface umgestellt
- gesamte PHP-Codebasis erneut syntaktisch geprüft

## 0.2.2 — 2026-07-27

- sichtbare `JOPTION_SELECT`-Platzhalter durch übersetzte Auswahltexte ersetzt
- Altersklasse wird anhand von Geburtsjahr, Geschlecht und aktivem Sportjahr vorgeschlagen
- automatische Aktualisierung im Athletenformular ergänzt
- serverseitige Klassenermittlung beim Speichern ergänzt

## 0.2.1 — 2026-07-27

- Formular-ID aller Bearbeitungsansichten auf Joomla-Standard `adminForm` korrigiert
- Formularaktionen um die jeweilige View ergänzt
- Joomla-Formularvalidierung und Keepalive eingebunden
- Speichern, Speichern & Neu sowie Übernehmen repariert

## 0.2.0 — 2026-07-27
- CRUD-Verwaltung für Athleten, Vereine, Klassen und Sportjahre
- Dashboard mit Kennzahlen und zuletzt angelegten Athleten
- Joomla-Benutzer- und Trainerzuordnung
- Frontend-Profil „Meine Daten"
- Datenbankmigration für neue Athletenfelder
