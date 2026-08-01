# BoTa Dark Mode – Root-Cause-Analyse

Stand: 1. August 2026
Analysierter Repository-Stand: `2444bdb` / BoTa `2.6.1`

## Kurzfazit

Die Theme-Erkennung von BoTa funktioniert. Die Regression liegt in der fehlenden Kopplung zwischen dem BoTa-Theme und dem Theme-System von Bootstrap 5.3.

BoTa setzt `data-bota-theme`, `data-bota-theme-resolved`, `color-scheme` und Body-Klassen. Es setzt **nicht** `data-bs-theme`. Gleichzeitig definiert BoTa nur eigene `--bota-*`-Tokens und – mit Ausnahme lokaler Tabellenvariablen – keine globalen Bootstrap-Tokens. Bootstrap-Komponenten können deshalb weiterhin ihre Light-Werte aus `--bs-*` verwenden.

Das vorherige CSS verdeckte diesen Architekturfehler durch hochspezifische Regeln wie `html[data-bota-theme-resolved="dark"] .card` plus `!important`. Commit `df31d4f` entfernte diese Bridge. Die neuen generischen Regeln `.card`, `.card-body`, `.dropdown-menu` usw. haben nur Bootstrap-übliche Spezifität und können von später geladenem Template-/Bootstrap-CSS oder dessen Variablen überstimmt werden.

### Verifizierter Live-Nachtrag

Die Seite `https://training.jugendcup-nrw.de/index.php/trainer-dashboard?view=trainerdashboard` wurde anschließend direkt untersucht. Sie liefert Cassiopeia mit Bootstrap **5.3.8** aus. Die tatsächliche CSS-Reihenfolge war:

1. `joomla-fontawesome.min.css`
2. Cassiopeia `template.min.css`
3. Cassiopeia `colors_standard.min.css`
4. `joomla-alert.min.css`
5. Cassiopeia `user.css`
6. ein kleiner Inline-`:root`-Block

`media/com_jugendtraining/css/site.css` war trotz vorhandener Datei, aktueller `joomla.asset.json` und `useStyle()`-Aufruf **nicht im Dokument geladen**. Das ist die primäre operative Ursache. Cassiopeia berechnete deshalb `.card` zu weiß (`rgb(255,255,255)`), Text zu dunkel (`rgb(34,38,42)`) und Border zu hell (`rgb(223,227,231)`). Zusätzlich fehlte `data-bs-theme`.

Das produktive Cassiopeia-CSS verwendet für Komponenten hauptsächlich unpräfixierte Variablen wie `--body-bg`, `--card-bg`, `--border-color`, `--dropdown-bg` und `--list-group-bg`. Eine alleinige Änderung von `data-bs-theme` wäre in diesem Build nicht ausreichend gewesen.

## 1. Bootstrap-Version

### Repository-Befund

BoTa liefert Bootstrap nicht selbst aus und enthält deshalb keine eigene, festgeschriebene Bootstrap-Version. Das Manifest adressiert Joomla 6 (`com_jugendtraining.xml`, Zeile 2; `updates.xml`, Zielplattform Joomla 6).

Der aktuellste offizielle Joomla-6.0-Core-Tag `6.0.112` deklariert in `package.json` und `package-lock.json` **Bootstrap 5.3.3**. Das ist somit die belegte Bootstrap-Baseline für die vom Repository adressierte Joomla-Version.

### Einschränkung

Nach Öffnung des Testsystems konnte die produktive Testversion verifiziert werden:

- Zielplattform des Repositorys: Joomla 6
- offizielle Joomla-6.0-Baseline: Bootstrap 5.3.3
- im Testsystem tatsächlich ausgelieferte Version: Bootstrap 5.3.8

## 2. Geladene CSS-Dateien

Das Repository enthält genau zwei CSS-Dateien:

- `media/css/site.css`
- `media/css/admin.css`

Nicht vorhanden sind:

- `media/css/theme.css`
- `media/css/dark.css`
- Bootstrap-CSS
- Cassiopeia-CSS
- Astroid-CSS
- Template-CSS

`media/joomla.asset.json`, Zeilen 21–25, registriert `media/css/site.css` als `com_jugendtraining.site`. `site/src/Controller/DisplayController.php`, Zeile 49, aktiviert dieses Asset für das Frontend. `media/css/admin.css` wird separat im Administrator aktiviert.

Bootstrap und Template-CSS stammen aus der Joomla-Installation beziehungsweise dem aktiven Template und sind nicht Bestandteil des BoTa-Repositorys.

## 3. Ladereihenfolge

### Repository-seitig beweisbar

BoTa fordert sein Frontend-Stylesheet während des Komponenten-Dispatchs über den WebAssetManager an. Die Assetdefinition nennt keine CSS-Abhängigkeiten und keine feste Position relativ zum Template-CSS.

### Nicht repository-seitig beweisbar

Die vollständige produktive Reihenfolge wurde im Live-Nachtrag oben verifiziert. Allgemein kann sie je Template abweichen; im Testsystem fehlte das BoTa-Stylesheet vollständig.

Eine mögliche Reihenfolge wäre beispielsweise

1. Bootstrap-CSS,
2. `template.css` oder `template.min.css`,
3. Template-`user.css`,
4. `media/com_jugendtraining/css/site.css`

kann nur am gerenderten Dokument festgestellt werden. Im Testsystem wurde dieser Nachweis erbracht.

Das beobachtete Symptom – dunkler Seitenhintergrund, aber weiße Bootstrap-Karten – ist mit folgendem Laufzeitbild konsistent:

- Die BoTa-Body-Regel gewinnt für die Seite.
- Bootstrap-/Template-Regeln oder weiterhin helle `--bs-*`-Tokens gewinnen für Komponenten.

Vor einer Implementierung sollte die Reihenfolge einmal im Livesystem über `document.styleSheets` oder das Netzwerk-Panel protokolliert werden.

## 4. Theme-Selektor

`site/src/Controller/DisplayController.php`, Zeilen 58–78, setzt:

- `data-bota-theme`
- `data-bota-theme-resolved`
- `document.documentElement.style.colorScheme`
- `body.bota-theme-dark` oder `body.bota-theme-light`

Es gibt im gesamten Repository **keinen** Schreibzugriff auf `data-bs-theme` und keine CSS-Regel für `[data-bs-theme="dark"]`.

Aktiv verwendet werden:

1. BoTa-Konfiguration: `data-bota-theme="auto|light|dark"`
2. aufgelöstes BoTa-Theme: `data-bota-theme-resolved="light|dark"`
3. Body-Kompatibilitätsklassen: `bota-theme-light` / `bota-theme-dark`
4. Browserhinweis: `color-scheme`
5. BoTa-CSS-Variablen: `--bota-*` und kompatible `--jt-*`-Aliase

Von diesen Systemen steuert nur `data-bota-theme-resolved` die BoTa-Farbvariablen. Die Body-Klassen werden aktuell von keinem Komponenten-CSS-Selektor verwendet. `color-scheme` beeinflusst native Browsercontrols, aktiviert aber keinen Bootstrap-Darkmode.

## 5. Bootstrap-Variablen

### Im BoTa-Repository

Folgende globalen Bootstrap-Variablen werden von BoTa weder in `:root` noch im Dark-Block gesetzt:

- `--bs-body-bg`
- `--bs-body-color`
- `--bs-card-bg`
- `--bs-card-color`
- `--bs-border-color`
- `--bs-secondary-bg`
- `--bs-tertiary-bg`

Nur auf `.table` setzt `media/css/site.css`, Zeilen 287–292, lokale Tabellenvariablen:

- `--bs-table-bg`
- `--bs-table-color`
- `--bs-table-border-color`
- `--bs-table-striped-bg`
- `--bs-table-hover-bg`

### Erwartete Werte im fehlerhaften Darkmode

Da `data-bs-theme="dark"` fehlt, bleiben die globalen `--bs-*`-Werte die Werte des Standard-/Light-Themes des aktiven Bootstrap-/Template-Builds. Ihre exakten Hexwerte stehen nicht im BoTa-Repository und müssen im Livesystem mit `getComputedStyle(document.documentElement)` erfasst werden.

Der wesentliche Befund ist nicht ein bestimmter Hexwert, sondern der Eigentümer: Die Werte werden vom externen Bootstrap-/Template-CSS gesetzt und von BoTa nicht in den Dark-Zustand geschaltet.

## 6. CSS-Kaskade und gewinnende Regeln

### Innerhalb von `media/css/site.css`

Für `data-bota-theme-resolved="dark"` ergeben die BoTa-Tokens folgende beabsichtigte Werte:

| Element | Hintergrund | Text | Border | Gewinnende BoTa-Regel |
|---|---|---|---|---|
| `.card` | `rgba(27,31,35,.97)` | `#e8eaed` | `#343a40` | `site.css` Zeilen 449–455 plus 212–229 |
| `.card-body` | `#1b1f23` | `#e8eaed` | `#343a40` | `site.css` Zeilen 212–229 |
| `.card-header` | Gradient über `#24292f` | `#e8eaed` | `#343a40` | `site.css` Zeilen 472–478 plus 231–242 |
| `.btn` | nicht zentral gesetzt | geerbt/Variantentoken | variantenspezifisch | Basis Zeilen 533–542; Varianten 552–587 |
| `.table` | `#1b1f23` | `#e8eaed` | `#343a40` | `site.css` Zeilen 287–307, 669–701 |
| `.form-control` | `#1b1f23` | `#e8eaed` | `#4b5560` beziehungsweise Fokusfarbe | `site.css` Zeilen 264–279 und 614–630 |
| `.dropdown-menu` | `#1b1f23` | `#e8eaed` | `#343a40` | `site.css` Zeilen 244–254 |
| `.nav` / Tabs | normal transparent; aktiv `#24292f` | `#aab0b6`, aktiv `#e8eaed` | `#343a40` | `site.css` Zeilen 309–330 |
| `.list-group-item` | `#1b1f23` | `#e8eaed` | später `#343a40`/Subtle-Token | `site.css` Zeilen 212–229 und 782–785 |

### Im vollständigen Livesystem

Die endgültige gewinnende externe Datei und Zeilennummer kann ohne gerenderte Live-Seite nicht seriös angegeben werden. Genau diese Information fehlt im Repository. Bei später geladenem Bootstrap-/Template-CSS gelten insbesondere folgende Konflikte:

- `.card` gegen Bootstrap `.card`
- `.card-body` gegen Bootstrap `.card-body`
- `.card-header` gegen Bootstrap `.card-header`
- `.form-control` gegen Bootstrap `.form-control`
- `.dropdown-menu` gegen Bootstrap `.dropdown-menu`
- `.list-group-item` gegen Bootstrap `.list-group-item`
- Buttonvarianten gegen Bootstrap `.btn-*`

Die Selektoren besitzen gleiche oder vergleichbare Spezifität. Bei gleicher Spezifität gewinnt die später geladene Regel. Zusätzlich beziehen Bootstrap-Regeln ihre Werte aus den weiterhin hellen `--bs-*`-Variablen.

## 7. Feste Farben, Inline-Styles und `!important`

### CSS

Direkte Farbwerte in `media/css/site.css` stehen nur im zentralen Light-/Dark-Tokenblock, Zeilen 15–189. Außerhalb dieses Blocks wurden keine Hex-/RGB-/RGBA-Farben gefunden. `media/css/admin.css` enthält keine direkten Farbwerte.

Es existieren 35 `!important`-Vorkommen in `site.css`. Sie betreffen Responsive-/Layoutregeln, reduzierte Bewegung, Achievement-Größen und SVG-Geometrie. Es wurde keine farbsetzende Darkmode-Regel mit `!important` gefunden.

### Inline-Styles

Es wurden 21 PHP-Zeilen mit `style=` gefunden. Farblich relevant sind:

- `site/tmpl/trainerdashboard/default.php`, Zeile 77: Ampelfarben und Border
- `site/tmpl/calendar/default.php`, Zeile 35: dynamische Badge-Hintergrund- und Textfarbe
- `site/tmpl/dashboard/default.php`, Zeile 26: dynamische Badge-Hintergrund- und Textfarbe
- `administrator/tmpl/calendar/default.php`, Zeile 12: dynamische Badge-Hintergrundfarbe
- `site/tmpl/athleteperformance/default.php`, Zeilen 41, 44 und 46: SVG-Farben
- `site/tmpl/dashboard/default.php`, Zeilen 376, 379 und 381: SVG-Farben
- `site/tmpl/trainerathletedetail/default.php`, Zeilen 106, 114, 123, 125, 127 und 128: per JavaScript erzeugte SVG-Farben
- `administrator/tmpl/import/default.php`, Zeile 557: Standardfarbe eines dynamisch erzeugten Color-Inputs

Weitere Inline-Styles steuern Breiten, Höhen, Scrollbereiche und dynamische Fortschrittsbreiten, aber keine Themefarben.

### Fachlich gespeicherte Farben

Kalenderkategorien besitzen absichtlich datengetriebene Farbwerte. Fundstellen sind:

- `site/src/Service/CalendarService.php`, Zeilen 26–36
- `administrator/src/Model/CalendarModel.php`, Zeile 6
- `administrator/src/Model/ImportModel.php`, Zeilen 29–37

Diese Werte sind nicht die Ursache weißer Bootstrap-Karten.

## 8. Exakte Root Cause

Die Root Cause besteht aus drei zusammenwirkenden Punkten:

1. **Das Komponentenstylesheet wurde im Testsystem nicht geladen.** Damit konnten weder BoTa-Komponentenregeln noch das vollständige neue Token-System wirken.
2. **Bootstrap wird nicht über seinen Theme-Selektor aktiviert.** BoTa setzte kein `data-bs-theme="dark"`. `color-scheme: dark` und `data-bota-theme-resolved="dark"` sind Bootstrap unbekannt.
3. **Cassiopeia verwendet eigene Komponentenvariablen.** Der ausgelieferte Build liest unter anderem `--body-bg`, `--card-bg` und `--border-color`; diese waren nicht zentral an BoTa gekoppelt. Der frühere Schutz gegen Bootstrap-Light-Regeln bestand aus hochspezifischen `!important`-Regeln und wurde in Commit `df31d4f` entfernt.

Damit ist das Symptom vollständig erklärt:

- `body` wird über BoTa-Tokens dunkel.
- Bootstrap-Komponenten lesen weiterhin Light-`--bs-*`-Werte oder werden durch späteres Template-CSS hell gesetzt.
- Die neuen generischen Selektoren sind nicht stark genug, eine ungünstige Assetreihenfolge zuverlässig zu überstimmen.

Die Root Cause liegt nicht in einzelnen Views und nicht in der Speicherung oder Auflösung der Benutzereinstellung.

## 9. Empfohlene kleinste Änderung

Nach dem Livebefund ist die kleinste vollständige Änderung eine zentrale Kombination aus drei Maßnahmen:

1. `site.css` neben der WebAsset-Aktivierung zuverlässig und versioniert über den Display-Controller registrieren.
2. Den vorhandenen Resolver um den Bootstrap-Selektor ergänzen:

```javascript
document.documentElement.setAttribute('data-bs-theme', resolved);
```

3. Cassiopeias zentrale Komponentenvariablen (`--body-bg`, `--card-bg`, `--border-color`, `--dropdown-bg`, `--list-group-bg` usw.) im bestehenden Light-/Dark-Tokenblock auf `--bota-*` abbilden.

Die Selektorzeile gehört in dieselbe zentrale `apply()`-Funktion, die bereits `data-bota-theme-resolved` setzt. Die Variablenbrücke bleibt vollständig zentral und erzeugt weder View-Regeln noch Inline-Styles oder Darkmode-Hacks.

## 10. Alternative Lösungen

### A. Bootstrap-Tokens zentral auf BoTa-Tokens abbilden

Im bestehenden BoTa-Light-/Dark-Variablenblock könnten unter anderem `--bs-body-bg`, `--bs-body-color`, `--bs-card-bg`, `--bs-card-color`, `--bs-border-color`, `--bs-secondary-bg` und `--bs-tertiary-bg` auf `--bota-*` abgebildet werden.

Vorteil: funktioniert auch mit einem Bootstrap-5.3-Build, dessen Dark-Variablen unvollständig sind.
Nachteil: BoTa übernimmt dauerhaft Verantwortung für Bootstrap-interne Tokens und muss deren Umfang pflegen.

### B. Kombination aus `data-bs-theme` und wenigen Token-Bridges

Bootstrap wird über `data-bs-theme` aktiviert; nur nach einem Laufzeittest nachweislich fehlende Templatevariablen werden zentral überbrückt.

Vorteil: robust und weiterhin zentral.
Nachteil: geringfügig größer als die Minimaländerung.

### C. Assetreihenfolge explizit festlegen

Das Komponentenstylesheet könnte über WebAsset-Abhängigkeiten bewusst nach dem Template-/Bootstrap-Stylesheet geladen werden.

Vorteil: generische BoTa-Regeln gewinnen häufiger.
Nachteil: Templateabhängig, behebt die fehlende Bootstrap-Themeaktivierung nicht und ist daher allein nicht ausreichend.

### Nicht empfohlen

- erneute `html[data-bota-theme-resolved="dark"] .card`-Sammelregeln
- `!important` für einzelne Views
- separate Dark-CSS-Dateien je View
- Inlinefarben
- ausschließlich höhere Selektorspezifität

Diese Varianten würden das Symptom erneut verdecken, aber Bootstrap und BoTa weiterhin als getrennte, konkurrierende Theme-Systeme belassen.

## Benötigter Laufzeitnachweis vor Umsetzung

Für die endgültige Bestätigung im Livesystem sind einmalig zu protokollieren:

1. `document.styleSheets` in tatsächlicher Reihenfolge
2. Bootstrap-Version aus ausgelieferter Datei oder Source-Map/Header
3. `getComputedStyle(document.documentElement)` für die sieben genannten `--bs-*`-Variablen
4. DevTools „Matched CSS Rules“ für eine repräsentative `.card`, `.form-control` und `.dropdown-menu`
5. Vorhandensein eines `[data-bs-theme="dark"]`-Blocks im geladenen CSS

Bis dahin sind die konkrete externe Dateizeile und die exakten produktiven `--bs-*`-Hexwerte als **nicht verifiziert** zu behandeln; die fehlende Bootstrap-Theme-Kopplung im Repository ist dagegen eindeutig belegt.
