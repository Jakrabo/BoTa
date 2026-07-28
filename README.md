# com_jugendtraining 1.0.1

Joomla-6-Komponente für die Jugendtrainingsverwaltung.

## Enthalten
- Dashboard
- Athletenverwaltung
- Vereinsverwaltung
- Klassenverwaltung
- Sportjahre
- Frontend „Meine Daten"
- Update-Migration von 0.1.x


## Version 1.0.1

- Trainingsplanung im Joomla-Backend
- Anwesenheitserfassung für alle aktiven Athleten
- Status: anwesend, verspätet, entschuldigt und unentschuldigt
- individueller Kommentar je Athlet und Training
- erweitertes Dashboard mit kommenden Trainingsterminen


## Version 1.0.1

- Ergebnisverwaltung
- Filter nach Athlet und Ergebnisart
- Ringzahl, Pfeilzahl, Distanz, 10er und X
- automatischer Durchschnitt je Pfeil


## Version 1.0.1

- Athleten können eigene Ergebnisse im Frontend anlegen, bearbeiten und löschen
- sichere serverseitige Zuordnung über den Joomla-Benutzer
- Prüfstatus ungeprüft, bestätigt oder zurückgewiesen


## Version 1.0.1

- fehlende Spalte `joomla_user_id` wird bei Updates ergänzt
- Frontend-Abfragen gegen unvollständige Altinstallationen abgesichert


## Version 1.0.1

- Frontend-Berechtigung für das Anlegen eigener Ergebnisse ergänzt
- Bearbeiten bleibt auf eigene Ergebnisse beschränkt


## Version 1.0.1

- Benutzerzuordnung der Frontend-Ergebnisse auf das bestehende Feld `user_id` korrigiert
- Anlegen und Bearbeiten eigener Ergebnisse funktioniert nun mit der Athletenzuordnung


## Version 1.0.1

- Frontend-Formularverzeichnis im Joomla-Manifest ergänzt
- Ergebnisformular wird bei Updates korrekt installiert


## Version 1.0.1

- fehlende Sprachtexte für Ergebnisliste und Ergebnisformular ergänzt
- deutsche und englische Site-Sprachdateien vervollständigt


## Version 1.0.1

- Übungskatalog mit Kategorien, Material, Bild und Video
- Trainingsprogramme aus mehreren Übungen
- Zuordnung von Programmen zu Athleten
- Athleten können Übungen im Frontend als erledigt markieren
- Fortschrittsbalken je Trainingsprogramm


## Version 1.0.1

- Leistungsentwicklung aus den letzten Ergebnissen
- persönliche Bestleistungen nach Entfernung und Pfeilzahl
- Ziele mit Fortschrittsanzeige
- private und sichtbare Trainernotizen

## Version 1.0.1

- leere numerische Zielfelder werden vor dem Speichern auf 0 gesetzt
- Dezimalwerte werden auch bei deutscher Kommaschreibweise normalisiert
- leeres Zieldatum wird als NULL gespeichert
- Übersetzung des Backend-Titels korrigiert

## Version 1.0.1

- automatische Zielberechnung für Anwesenheit, Ringzahl, Ringdurchschnitt und Trainingsprogramme
- manuelle Zielberechnung für freie Ziele weiterhin möglich
- integrierte Dokumentation direkt im Ziele-Modul
- kontextabhängige Erklärung je Zielart
- automatischer Abschluss eines Ziels bei erreichtem Zielwert

## Version 1.0.1

- 500-Fehler der Zielübersicht durch korrekte öffentliche getItems()-Methode behoben
- sämtliche Backend-Modul- und Toolbar-Übersetzungen ergänzt
- Installation der Administrator-Sprachdateien im Manifest abgesichert

## Version 1.0.1

- Administrator-Sprachinstallation Joomla-konform neu aufgebaut
- fehlende Titelübersetzungen nochmals vollständig ergänzt
- Toolbar-Titel übersetzen den Schlüssel nun explizit über Text::_()

## Version 1.0.1

- Joomla-Benutzergruppen „Jugendtraining - Trainer“ und „Jugendtraining - Schütze“
- Trainingsgruppen mit mehreren Schützen und Trainern
- Trainingsgruppen ausschließlich für Super Benutzer administrierbar
- Trainer-Frontend mit gruppenbasierter Datensicht
- modulare Menütypen für Schützen- und Trainerbereiche
- Leistungsdiagramm mit Ergebnisbalken und Linie des Ringdurchschnitts

## Version 1.0.1

- Installationsfehler durch nicht vorhandene Methode Usergroup::setLocation() behoben
- Joomla-Benutzergruppen werden transaktionsgesichert direkt im Nested-Set angelegt
- bereits vorhandene Gruppen werden erkannt und nicht doppelt angelegt

## Version 1.0.1

- eigene Joomla-Modelle für sämtliche modularen Schützen-Menütypen ergänzt
- eigene Joomla-Modelle für sämtliche modularen Trainer-Menütypen ergänzt
- Hauptmodelle für sichere Vererbung geöffnet
- alle modularen Views gegen leere Modellrückgaben abgesichert

## Version 1.0.1

- doppeltes Diagramm aus der Schützen-Gesamtübersicht entfernt
- ausschließlich das kombinierte Diagramm mit Ergebnisbalken und Ringdurchschnittslinie bleibt erhalten

## Version 1.0.1

- Trainings werden zwingend genau einer Trainingsgruppe zugeordnet
- Teilnehmerlisten entstehen automatisch aus den Schützen der Trainingsgruppe
- Einzelzuweisung von Schützen zu Trainings entfällt
- Trainer können Trainings für eigene Gruppen im Frontend anlegen, bearbeiten und löschen
- Serien übernehmen automatisch die gewählte Trainingsgruppe
- Anwesenheitsdaten außerhalb der aktuellen Gruppe werden beim Gruppenwechsel entfernt

## Version 1.0.1

- fehlende Frontend-Sprachschlüssel des Trainingsformulars ergänzt
- Seitentitel für Training anlegen und bearbeiten übersetzt
- explizites Laden der Komponenten-Sprache im Trainer-Trainingsformular ergänzt

## Version 1.0.1
- revisionssichere Bogen- und Visiereinstellungen
- optionale Detaileinstellungen
- Ergebnisverknüpfung mit konkreter Setup-Revision
- persönliches Trainingstagebuch mit Pfeilzahl, Dauer, Methode, Schwerpunkt und Notizen
- Traineransicht der Trainingstagebücher zugeordneter Schützen

## Version 1.0.1
- dynamische Anzahl von Visiereinstellungen
- Zeilen können hinzugefügt und entfernt werden
- Setupformular nach Komponenten gegliedert
- optische Trennlinien zwischen Einstellungsbereichen

## Version 1.0.1

- sämtliche sichtbaren Frontend-Aktionen auf eigene Komponenten-Sprachschlüssel umgestellt
- Bearbeiten, Löschen, Speichern und Abbrechen unabhängig von Joomla-Core-Schlüsseln
- Formularbezeichnungen für Titel, Status, Ja/Nein und Veröffentlichungsstatus vereinheitlicht
- zentrale Bestätigungs- und Statusmeldungen ergänzt
- deutsche und englische Sprachdateien vollständig synchronisiert

## Version 1.0.1
- Trainer-Ampel für Trainingsumfang und Ergebnisentwicklung
- Vorschau auf Klassenwechsel zum nächsten Sportjahr
- Statistikmodul für Training, Ergebnisse und Setup-Revisionen
- Auswertungen nach Pfeilschaft, Spine und Standhöhe
- Wetterdaten bei Ergebnissen und gemeinsame Visier-/Wetterauswertung
- Pearson-Korrelation zwischen monatlichem Trainingsumfang und Ringdurchschnitt

## Version 1.0.1

- fehlende Frontend-Sprachschlüssel für Schützen, Ergebnisse, Trainings und Trainingsgruppen ergänzt
- Komponenten-CSS im Trainer-Dashboard und Statistikmodul explizit geladen
- Ampelfarben zusätzlich direkt am Element abgesichert
- Webasset-Version zur Vermeidung alter Browser-Caches erhöht

## Version 1.0.1

- vollständiges modernes Frontend-Designsystem
- altes Fabrik-spezifisches CSS entfernt
- mobile Cards, Tabellen, Formulare und Buttons vereinheitlicht
- jugendliche Farbwelt aus Türkis, Blau, Violett und Orange
- verbesserte Ampeln, Fokuszustände und Touch-Bedienung
- Header, Dashboard, Setupformular und Statistikansichten überarbeitet

## Version 1.0.1
- Achievement-System mit automatischen und manuellen Erfolgen
- PNG-Badges und moderne Badge-Galerien
- neueste sechs Badges im Schützen-Dashboard
- vollständige Achievement-Übersicht für Schützen
- Trainer-Cockpit für Vergabe, Prüfung und Widerruf
- automatische Regeln für Tages-/Wochenpfeile, Tagebuch-Streaks und Meisterschaftsteilnahmen

## Version 1.0.1

- leere optionale Wetter-Zahlenfelder werden vor dem Speichern in SQL-NULL umgewandelt
- Dezimalwerte mit deutschem Komma werden akzeptiert
- Frontend- und Administrator-Ergebnisformular korrigiert

## Version 1.0.1
- Wettkampf-Achievements berücksichtigen nur bestätigte Ergebnisse
- Achievement-CRUD im Joomla-Backend
- Achievement-Verwaltung im Trainer-Frontend
- konfigurierbare automatische Kriterien und Grenzwerte
- frei definierbare Wettkampfbegriffe
- sicherer PNG-Upload bis 2 MB

## Version 1.0.1

- Uploadfehler durch nicht verfügbare Joomla-Filesystem-Klasse behoben
- native PHP-Dateiverarbeitung für PNG-Uploads
- Badges werden unter /images/jugendtraining/badges/ gespeichert
- vorhandene PNG-Dateien aus dem Badge-Ordner auswählbar
- Uploadlimit auf 3 MB erhöht
- bestehende Badge-Pfade werden automatisch migriert

## Version 1.0.1

- vollständige Anwesenheitserfassung im Trainer-Frontend
- Status: anwesend, verspätet, entschuldigt, abwesend oder nicht erfasst
- optionale Kommentare je Schütze
- Schaltfläche „Alle anwesend“
- bestehende Anwesenheitsdaten werden geladen und bearbeitet
- mobile Kartenansicht für die Teilnehmerliste
- Zugriff ausschließlich auf eigene Trainingsgruppen

## Version 1.0.1

- mobile Anwesenheitskarten statt gequetschter Tabelle
- große Touch-Schaltflächen für alle Anwesenheitsstatus
- automatische Speicherung je Schütze per AJAX
- farbliche Statusanzeige pro Karte
- Kommentare standardmäßig einklappbar
- Filter für offene und fehlende/verspätete Schützen
- Sammelaktion „Alle anwesend“
- Desktopansicht als zweispaltiges Kartenraster

## Version 1.0.1

- integriertes rollenabhängiges Hilfecenter
- separate Frontend-Dokumentation für Schützen und Trainer
- separate Backend-Dokumentation für Administratoren und Backend-Trainer
- Markdown-basierte Hilfeseiten unter media/com_jugendtraining/help
- Volltextsuche über Titel, Stichwörter und Inhalte
- Kapitel-Navigation und Themenkarten
- Druckansicht
- deutsche vollständige Grunddokumentation
- englische Basishilfe als Fallback

## Version 1.0.1

- Backend-Hilfecenter akzeptiert einen fehlenden Artikelparameter
- null-sichere Behandlung von article im Frontend und Backend
- zusätzliche Absicherung direkt im HelpService


## Version 1.0.2

- unvollständiges Hilfesystem vollständig entfernt
- CSV-Export für eigene Trainingstagebuchdaten
- CSV-Export für eigene Ergebnisse
- Trainerexport für alle zugeordneten Schützen
- Excel-kompatible UTF-8-Dateien mit Semikolon als Trennzeichen
- Schutz vor CSV-/Tabellenkalkulations-Formelinjektion

## Version 1.1.0

- Backend CSV-Import für Ergebnisse, Tagebuch und Achievement-Zuordnungen
- Downloadbare Template-CSV je Importtyp
- Fehlerprotokoll je CSV-Zeile
- Trainingsmethoden und Trainingsschwerpunkte im Backend konfigurierbar
- Aktives Bogensetup bei neuen Tagebucheinträgen vorausgewählt

## Version 1.2.0

- konfigurierbares Strafregister
- monetäre und nichtmonetäre Strafen
- dritter Konfigurationstab „Strafen“
- Frontend-Zuordnung durch Trainer für Schützen ihrer Trainingsgruppen
- Status offen/erledigt mit Erledigungsvermerk
- kompakte Achievement-Badges im Schützen-Dashboard

## Version 1.4.0

- Trainingsumfangdiagramm in der Leistungsentwicklung
- Umschaltung Monat/Kalenderwoche
- Zeitraumfilter für letzte 12 Monate und vorhandene Sportjahre

## Version 1.8.0

- sichtbarer Name: BoTa
- korrigierte Programmzuordnung
- korrigierte Achievement-Kacheln und Zeitraumfilter
- Detailseitenlinks im Trainerdashboard

## Version 1.8.1

- stabiler technischer Manifestname für Joomla-Updates
- automatische Bereinigung alter Administrator-Menüeinträge
- sichtbarer Name bleibt BoTa

## Version 1.9.0 – GitHub-Updates

Das Manifest verwendet:
`https://raw.githubusercontent.com/Jakrabo/BoTa/main/updates.xml`

Für automatische Updates:
1. `updates.xml` und `changelog.xml` aus diesem Paket in den Root des Branches `main` committen.
2. GitHub Release `v1.9.0` anlegen.
3. Das Installationspaket dort exakt als `com_bota-1.9.0.zip` als Release Asset hochladen.
4. Bei künftigen Releases Version und Download-URL in `updates.xml` anpassen.
