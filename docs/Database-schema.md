# Database Schema (Fritsion CMS)

Het huidige database schema (versie 0.1.8) gaat uit van het volgende basis schema met table prefix `fcms_` (aanpasbaar via setup).

| Tabelnaam | Doel / Inhoud |
| --- | --- |
| `fcms_settings` | Algemene configuratie-instellingen van de site. (Systeemvariabelen: naam, domein, taal, layouts). |
| `fcms_users` | Admin- en gebruikersaccounts. Bevat het veilig gehashte paswoord en toegangsrol. |
| `fcms_sessions` | Optionele tabel voor geavanceerd sessie-beheer en account-veiligheid. |
| `fcms_pages` | De content en pagina's van het CMS, gekoppeld aan templates. Bevast JSON content blokken. |
| `fcms_templates` | Layout configuraties. Bestuurt hoe de componenten (header, blocks, footer) in een grid structuur zijn opgebouwd. |
| `fcms_themes` | De visuele themering: kleuren, lettertypes, spacing. Omvat een Actief thema waarmee de gehele look & feel via CSS root properties kan worden omgeschakeld. |

## 1. Tabel: `fcms_themes`
Deze nieuwe structuur is ingericht voor het opslaan de styling.

- `id`: (INT, Auto Increment)
- `name`: (VARCHAR 100) Leesbare themanaam
- `slug`: (VARCHAR 100) Systeem URL
- `is_default`: (TINYINT) Beschermde status (1=Standaard, niet te verwijderen)
- `is_active`: (TINYINT) Huidig geselecteerde weergave
- `settings_json`: (LONGTEXT) De JSON string met alle CSS configuraties (kleuren, lettertypes, spacing)
- Gebruikelijke `created_at` en `updated_at` timestamps.

**(Voor de volledige details van actuele datatypes per kolom, zie `database/schema.sql`)*.*
