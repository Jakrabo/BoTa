# 🏹 Joomla Jugendtraining

Eine Joomla-Komponente zur digitalen Organisation und Dokumentation des Jugendtrainings im Bogensport.

Die Komponente wurde speziell für die Anforderungen von Bogensportvereinen entwickelt und ermöglicht es Trainern und Jugendlichen, Trainingsdaten, Ergebnisse, Ausrüstung und persönliche Entwicklungen zentral über die Joomla-Webseite zu verwalten.

Die Anwendung kommt ohne Fabrik oder andere Form-Builder aus und ist als eigenständige Joomla-Komponente umgesetzt.

> **Status:** Aktive Entwicklung
> Die Komponente befindet sich derzeit noch in Entwicklung. Funktionen und Datenstrukturen können sich zwischen den Versionen ändern.

---

## 🎯 Ziel des Projekts

Im Jugendtraining entstehen viele Informationen, die häufig auf Papier, in Excel-Tabellen oder in verschiedenen Apps verteilt sind:

* Wer war beim Training?
* Welche Ergebnisse hat ein Schütze erzielt?
* Welche Einstellungen wurden am Bogen verwendet?
* Wie hat sich ein Schütze über die Saison entwickelt?
* Welche Alters- und Wettkampfklasse gilt?
* Welche Aufgaben stehen für die Trainer noch an?

**Joomla Jugendtraining** bündelt diese Informationen in einer zentralen Webanwendung innerhalb von Joomla.

Dabei steht eine einfache Bedienung auf Smartphones im Vordergrund, sodass die Anwendung direkt auf dem Bogenplatz oder in der Halle verwendet werden kann.

---

# ✨ Features

## 👤 Athletenverwaltung

Zentrale Verwaltung der Jugendlichen mit den für das Training relevanten Stammdaten.

Unter anderem können verwaltet werden:

* Vor- und Nachname
* Geburtsjahr
* Wettkampfklasse
* Bogenklasse
* Geschlecht
* Aktiv/Inaktiv
* Verknüpfung mit einem Joomla-Benutzer
* Zuordnung von Trainern

Die Wettkampfklasse kann anhand von Geburtsjahr und aktuellem Sportjahr automatisch bestimmt werden.

---

## 🗓️ Sportjahr & Altersklassen

Das aktuell verwendete Sportjahr wird zentral konfiguriert.

Auf Basis von

**Sportjahr − Geburtsjahr**

wird das für die Klasseneinteilung relevante Alter ermittelt.

Die hinterlegten Klassen können zusätzlich Informationen wie

* Altersbereich
* Distanz
* Auflagengröße
* Hallen-Einstellungen
* WA-Einstellungen
* Bogenklasse
* Geschlecht

enthalten.

Damit lassen sich die für einen Athleten relevanten Trainings- und Wettkampfparameter automatisch ableiten.

---

## 🏹 Trainingseinheiten

Trainer können Trainingseinheiten anlegen und verwalten.

Dadurch entsteht langfristig eine nachvollziehbare Trainingshistorie der Jugendlichen.

---

## ✅ Anwesenheit

Für jede Trainingseinheit kann die Anwesenheit der Jugendlichen dokumentiert werden.

So lässt sich nachvollziehen,

* wer regelmäßig trainiert,
* wie viele Trainingseinheiten absolviert wurden und
* welche Jugendlichen an bestimmten Terminen teilgenommen haben.

Die Erfassung ist für die Nutzung direkt während des Trainings optimiert.

---

## 📈 Ergebnisse & Leistungsentwicklung

Wettkampf- und Trainingsergebnisse können direkt über die Weboberfläche erfasst werden.

Unterstützt werden beispielsweise:

* Vereinsmeisterschaft
* Bezirksmeisterschaft
* Landesmeisterschaft
* Deutsche Meisterschaft
* weitere Turniere und Trainingswertungen

Neben dem Gesamtergebnis können Kennzahlen wie der **Ringdurchschnitt** gespeichert und für die langfristige Leistungsentwicklung verwendet werden.

---

## 🎯 Persönlicher Bereich

Angemeldete Schützen erhalten einen eigenen Bereich innerhalb der Webseite.

Dort stehen – abhängig von den Berechtigungen – beispielsweise folgende Bereiche zur Verfügung:

**Meine Daten · Ergebnis melden · Setup · Visier**

Jugendliche sehen dabei nur die für sie freigegebenen beziehungsweise eigenen Daten.

---

## 🔧 Bogen-Setup & Ausrüstung

Ausrüstungsinformationen können direkt beim Athleten hinterlegt werden.

Damit lassen sich beispielsweise Änderungen am Setup nachvollziehen und wichtige Einstellungen jederzeit abrufen.

Das ist besonders praktisch, wenn Einstellungen nach einem Umbau oder einer Änderung am Material rekonstruiert werden müssen.

---

## 🎯 Visiereinstellungen

Visiereinstellungen können für unterschiedliche Entfernungen dokumentiert werden.

Damit steht das persönliche Visierbuch jederzeit über Smartphone oder Tablet zur Verfügung.

---

## 🏅 Badges & Trainingsziele

Die Komponente unterstützt ein Badge-System zur Motivation der Jugendlichen.

Badges können mit definierten Anforderungen versehen und anschließend einzelnen Athleten zugeordnet werden.

Damit lassen sich beispielsweise

* Leistungsziele,
* Trainingsfortschritte,
* besondere Leistungen oder
* erreichte Meilensteine

sichtbar machen.

---

## 📋 Trainer-Aufgaben

Über Trainer-Todos können offene Aufgaben innerhalb des Trainerteams dokumentiert werden.

Dadurch können organisatorische oder sportliche Themen direkt einem Athleten beziehungsweise dem Trainingsbetrieb zugeordnet werden.

---

# 🔐 Benutzer & Berechtigungen

Die Komponente integriert sich in das Joomla-Benutzer- und Berechtigungssystem.

Vorgesehen sind insbesondere unterschiedliche Rollen für:

### 🏹 Schützen

Schützen erhalten Zugriff auf ihren persönlichen Bereich und können ausschließlich die für sie bestimmten Daten einsehen beziehungsweise bearbeiten.

### 🧑‍🏫 Trainer

Trainer erhalten erweiterte Funktionen zur Verwaltung der Athleten und des Trainingsbetriebs.

Dadurch kann die Anwendung verwendet werden, ohne Trainern Zugriff auf das Joomla-Backend geben zu müssen.

Die Rechteverwaltung baut auf der Joomla ACL auf.

---

# 🌙 Dark Mode

Die Benutzeroberfläche unterstützt einen hellen und dunklen Darstellungsmodus.

Je nach Konfiguration kann die Darstellung

**☀️ Hell · 🌙 Dunkel · 🖥️ Automatisch**

gewählt werden.

Im automatischen Modus orientiert sich die Darstellung an der Einstellung des verwendeten Gerätes beziehungsweise Browsers.

---

# 📱 Mobile First

Ein wesentliches Ziel des Projekts ist die Nutzung direkt während des Trainings.

Die Oberfläche wird daher insbesondere für

* Smartphones,
* Tablets und
* Touch-Bedienung

optimiert.

Trainer müssen für typische Aufgaben während des Trainings nicht auf das Joomla-Backend wechseln.

---

# 🗄️ Datenmodell

Die Komponente verwendet eigene Joomla-Datenbanktabellen und benötigt keine externen Form-Builder.

Zu den zentralen Bereichen gehören unter anderem:

```text
Athleten
├── Stammdaten
├── Wettkampfklasse
├── Joomla-Benutzer
├── Ausrüstung
├── Visiereinstellungen
├── Ergebnisse
└── Badges

Training
├── Trainingseinheiten
├── Anwesenheit
└── Trainingsblöcke

Organisation
├── Sportjahr
├── Klassen
├── Trainer
└── Trainer-Todos
```

---

# 🧩 Technisches Konzept

Die Anwendung ist als native Joomla-Komponente umgesetzt.

Ziel ist eine möglichst geringe Abhängigkeit von Erweiterungen anderer Anbieter.

**Grundprinzip:**

```text
Joomla
   │
   ├── Benutzerverwaltung
   ├── ACL / Benutzergruppen
   │
   └── Jugendtraining-Komponente
           │
           ├── Athleten
           ├── Training
           ├── Anwesenheit
           ├── Ergebnisse
           ├── Ausrüstung
           ├── Visier
           ├── Badges
           └── Trainer-Aufgaben
```

Dadurch kann die Komponente unabhängig von Lösungen wie Fabrik betrieben und weiterentwickelt werden.

---

# 🚀 Installation

> Die Installationsanleitung wird mit Erreichen einer stabilen Release-Version ergänzt.

Grundsätzlich wird die Komponente als normales Joomla-Installationspaket bereitgestellt und kann über

**System → Installieren → Erweiterungen**

installiert beziehungsweise aktualisiert werden.

---

# 🛠️ Entwicklungsstatus

Das Projekt befindet sich in aktiver Entwicklung.

Die Komponente wird zunächst für den praktischen Einsatz im Jugendtraining eines Bogensportvereins entwickelt. Dadurch entstehen neue Funktionen vor allem aus Anforderungen des tatsächlichen Trainingsbetriebs.

GitHub Releases sollten daher hinsichtlich ihrer Stabilität und ihres vorgesehenen Einsatzzwecks geprüft werden.

---

# 💡 Ideen & Feature Requests

Ideen zur Weiterentwicklung sind willkommen.

Insbesondere Rückmeldungen von

* Bogensportvereinen,
* Jugendtrainern,
* Joomla-Entwicklern und
* Anwendern aus der Vereinsorganisation

sind hilfreich.

Feature Requests und Fehler können über die **GitHub Issues** des Projekts gemeldet werden.

---

# 🤝 Mitwirken

Beiträge zum Projekt sind willkommen.

Mögliche Beiträge sind beispielsweise:

* Fehlerberichte
* Verbesserungsvorschläge
* Übersetzungen
* UI/UX-Verbesserungen
* Joomla-Entwicklung
* Dokumentation
* neue Funktionen für den Trainingsbetrieb

Pull Requests können gerne über GitHub eingereicht werden.

---

# ⚠️ Hinweis

Die Software befindet sich in Entwicklung.

Vor Updates sollte insbesondere bei produktiv genutzten Installationen ein vollständiges Backup der Joomla-Installation und der Datenbank erstellt werden.

---

# 📄 Lizenz

Die Lizenzierung des Projekts wird im Repository über die Datei `LICENSE` dokumentiert.

---

## 🏹 Für Jugendtraining entwickelt

**Joomla Jugendtraining** soll keine allgemeine Vereinsverwaltung ersetzen.

Das Ziel ist eine schlanke Anwendung für einen ganz konkreten Einsatzbereich:

> **Jugendtrainer sollen weniger Zeit mit Listen und Verwaltung verbringen und mehr Zeit für das eigentliche Training haben.**
