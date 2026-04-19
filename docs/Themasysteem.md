# Het Themasysteem

Met de introductie van versie 0.1.8 heeft Fritsion CMS een volledig geïntegreerd themasysteem gekregen. Dit systeem stelt gebruikers in staat om met één druk op de knop het volledige uiterlijk van hun website om te bouwen.

## Core Functionaliteit

1.  **Dynamische Themering**: Instellingen (kleuren, lettertypes, spacing) worden opgeslagen in de database. Deze worden vervolgens on-the-fly via CSS `var()` rules (Root Properties) toegewezen aan de voorkant van de applicatie.
2.  **Variabelen (`CSS Variables`)**: Vrijwel elke view in de frontend (`home.php`) maakt gebruik van variabelen als `--primary`, `--secondary`, `--bg`, en `--text` waardoor het ontwerp naadloos meeschaalt bij het wijzigen van een thema.
3.  **Toegankelijkheid**: Bij installatie is een vaste set aan defaults aanwezig (*Standaard thema*). Dit thema kan qua configuratie aangepast worden, maar is beveiligd tegen verwijdering (`is_default = 1`).

## Beheer Interface (Backoffice)
Binnen het dashboard via *Thema's* heb je de beheeromgeving voor de actieve styling:

-   **Kleurenpalet**: Direct een visuele color-picker voor zes anker-kleuren van het systeem (Primary, Secondary, Accent, Text, Background, Link).
-   **Achtergrondafbeelding**: Specificeer optioneel een absolute pad of geuploade file.
-   **Typografie & Spacing**: Bepalen van globale lettertypes voor koppen en normale body teksten, inclusief afstanden (padding en margin control panelen).

## Technisch: Hoe werkt de Frontend Injectie?
Via `FrontController.php` wordt bij een pagina request een Query uitgevoerd om het *Actieve Thema* in te laden (`SELECT settings_json FROM fcms_themes WHERE is_active = 1`).

Deze output string (JSON) wordt vertaald door PHP en vervolgens worden alle variabelen doorgezet in de `<style>` header van layout files (`app/Views/front/home.php`).
Als voorbeed ziet de resulterende header eruit als:
```css
:root {
  --primary: #3B2A8C;
  --bg: #FFFFFF;
}
```
Het ontwerp is daarmee volledig modulair gescheiden van de inhoud en HTML layout structuur.
