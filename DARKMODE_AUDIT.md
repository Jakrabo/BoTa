# BoTa Dark Mode Audit

Stand: 1. August 2026

## Ergebnis

Der Dark Mode der Komponente wird nicht mehr durch view-spezifische Darkmode-Regeln gesteuert. `media/css/site.css` enthält ein zentrales Variablesystem für Hell und Dunkel. Der vorhandene Theme-Resolver bleibt unverändert und setzt weiterhin `data-bota-theme-resolved="light"` beziehungsweise `data-bota-theme-resolved="dark"`; die Einstellung `auto` wird dadurch weiterhin aus `prefers-color-scheme` aufgelöst.

Außerhalb der zentralen Token-Deklarationen existieren in den CSS-Dateien keine direkten Hex-, RGB- oder RGBA-Farbwerte mehr. `media/css/admin.css` bindet die BoTa-Tokens an die nativen Joomla-Administratorvariablen an und folgt damit automatisch dem Backend-Farbschema.

## Geänderte CSS-Dateien

- `media/css/site.css`
  - zentrales Light-/Dark-Variablesystem ergänzt
  - doppelte historische Theme-Bridges entfernt
  - view-spezifische Darkmode-Blöcke für Dashboard, Anwesenheit, Trainingslisten und Trainingseinheiten entfernt
  - direkte Farben in Oberflächen-, Status-, Diagramm- und Interaktionsregeln durch Variablen ersetzt
  - generische Regeln für Cards, Tabellen, Formulare, Dropdowns, Modals, Tabs, Pagination, Alerts, Badges und Charts ergänzt
- `media/css/admin.css`
  - BoTa-Variablen an Joomla-Backendvariablen angebunden
  - Dashboard, Cards, Tabellen und Formulare auf Variablen umgestellt

## Ersetzte Farben

Ersetzt wurden sämtliche direkten Farbdeklarationen außerhalb des zentralen Tokenbereichs, insbesondere:

- Seiten-, Card- und Surface-Hintergründe
- normale, abgeschwächte und kontrastierende Textfarben
- Border- und Formularrahmenfarben
- Header-, Navigation- und Hoverfarben
- Button-, Status- und Fokusfarben
- Tabellenkopf-, Tabellenzeilen-, Zebra- und Hoverfarben
- Modal-, Dropdown-, Pagination- und Tabfarben
- Alert-, Badge- und Fortschrittsfarben
- Anwesenheitsstatus `anwesend`, `abwesend`, `verspätet`, `entschuldigt` und `offen`
- Diagrammtext, Achsen, Raster, Balken, Linien und Markierungen
- Schatten und halbtransparente Overlays

Die vorhandene Farbpalette wurde beibehalten. Es wurden keine zusätzlichen fachlichen Statusfarben eingeführt.

## Zentrale CSS-Variablen

### Grundflächen und Text

- `--bota-bg`
- `--bota-card`
- `--bota-card-hover`
- `--bota-surface`
- `--bota-surface-raised`
- `--bota-surface-subtle`
- `--bota-border`
- `--bota-border-strong`
- `--bota-text`
- `--bota-text-soft`
- `--bota-text-muted`
- `--bota-text-on-accent`

### Interaktion und Status

- `--bota-primary`, `--bota-primary-hover`, `--bota-primary-soft`
- `--bota-secondary`, `--bota-secondary-hover`, `--bota-secondary-soft`
- `--bota-accent`, `--bota-accent-soft`
- `--bota-success`, `--bota-success-strong`, `--bota-success-soft`, `--bota-success-text`
- `--bota-danger`, `--bota-danger-strong`, `--bota-danger-soft`, `--bota-danger-text`
- `--bota-warning`, `--bota-warning-strong`, `--bota-warning-soft`, `--bota-warning-text`
- `--bota-info`, `--bota-info-soft`, `--bota-info-text`
- `--bota-neutral`, `--bota-neutral-soft`

### Komponenten

- `--bota-page-background`
- `--bota-header-background`, `--bota-header-bg`, `--bota-header-text`, `--bota-header-dropdown`
- `--bota-input-bg`, `--bota-input-border`, `--bota-input-text`
- `--bota-table-header`, `--bota-table-row`, `--bota-table-hover`
- `--bota-modal-bg`
- `--bota-dropdown-bg`, `--bota-dropdown-hover`
- `--bota-avatar-bg`, `--bota-avatar-text`
- `--bota-chart-text`, `--bota-chart-grid`, `--bota-chart-axis`, `--bota-chart-bar`, `--bota-chart-line`, `--bota-chart-accent`
- `--bota-shadow-sm`, `--bota-shadow`, `--bota-shadow-hover`
- Alpha-, Tint-, Ring- und Overlay-Tokens für vorhandene halbtransparente Farben

Die bisherigen `--jt-*`-Variablen bleiben aus Kompatibilitätsgründen als Aliase auf die neuen `--bota-*`-Tokens bestehen.

## Entfernte Inline-Styles

Keine. Gemäß Vorgabe wurden keine PHP-Dateien geändert. Deshalb konnten Inline-Styles in Templates nicht entfernt oder durch zusätzliche Klassen ersetzt werden.

## Verbleibende Problemstellen

Folgende Inline-Styles verbleiben außerhalb der CSS-Dateien:

- dynamische Kalender-Kategoriefarben in Frontend und Backend
- dynamische Breiten von Fortschrittsbalken
- dynamische Ampelfarben im Trainerdashboard
- feste Tabellenbreiten und Scrollhöhen im Import-Backend
- einzelne Bildgrößen in Achievement-Templates

Die Kalender-, Dashboard- und Fortschrittswerte werden zur Laufzeit aus Daten berechnet. Eine vollständige Entfernung erfordert Änderungen an PHP-Markup oder Datenübergabe und war deshalb nicht Bestandteil dieses reinen CSS-Refactorings.

Verbleibende `!important`-Deklarationen betreffen ausschließlich Layout, Responsive-Verhalten, reduzierte Bewegung oder historische Achievement-Größen. Es verbleiben keine farbbezogenen `!important`-Deklarationen und keine Darkmode-spezifischen `!important`-Regeln.

## Statische Prüfungen

- keine direkten Hex-/RGB-/RGBA-Werte außerhalb der zentralen Token-Deklarationen
- keine direkten Farbwerte in `media/css/admin.css`
- keine undefinierten `--bota-*`-Variablen
- keine direkten Farbwerte als `var()`-Fallback
- ausgeglichene CSS-Klammerstruktur
- `git diff --check` ohne Fehler
- keine PHP-, JavaScript-, SQL-, Manifest- oder Sprachdatei geändert

## Noch manuell zu testende Views

Alle Prüfungen jeweils in `Hell`, `Dunkel` und `Auto`, auf Desktop und Mobile:

- Trainerdashboard einschließlich heutiger Trainings, Strafregister und Kalender
- Trainertraining in Karten- und Tabellenansicht einschließlich Sticky Footer
- Trainertrainings im Desktop- und mobilen Kartenlayout
- Trainerathletes und Trainerathleteedit
- Achievements, Achievement-Definitionen und Athlete-Dashboard-Vorschau
- Strafen
- Trainingstagebuch
- Trainingseinheiten: Liste, didaktisches Raster und Bearbeitung
- Übungen
- Kalender einschließlich dynamischer Kategoriebadges
- Formulare, Dropdowns, Tabs, Modals, Alerts, Badges und Pagination
- Diagramme und Statistiken
- sämtliche Backend-Listen und Backend-Edit-Views

Besonderes Augenmerk ist auf Kontrast, Fokuszustände, native Select-Optionen sowie Fremdstyles des verwendeten Joomla-Templates zu legen.
