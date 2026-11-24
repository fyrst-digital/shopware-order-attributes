# 1.2.1

**Changelog**
- CSS-Klasse `order-attributes-form` zum Bestellattribute-Formular bei Warenkorb- und Offcanvas-Positionen hinzugefügt, um das Styling und die Theme-Anpassung zu erleichtern

# 1.2.0

**Changelog**
- Konfigurierbare Eingabefeldgröße (`Standard`, `Klein`, `Groß`) für Bestellattribute in der Administration hinzugefügt, die in der Storefront mit Bootstrap-Größenklassen (`form-control-sm`, `form-control-lg`) dargestellt wird
- Anonyme Twig-UX-Komponenten `OrderAttributes:LineItem:Form` und `OrderAttributes:LineItem:Input` für Drittanbieter-Themes (Shopware 6.7+) hinzugefügt
- Veraltete Kommentare entfernt sowie Administrations- und Storefront-Assets ohne Administrations-Source-Maps neu gebaut (#14)

# 1.1.1

**Changelog**
- Fehler behoben, bei dem das Zusatzfeld-Icon im Bestellpositionen-Grid der Administration auch angezeigt wurde, wenn alle Zusatzfeld-Werte leer waren (#13)

# 1.1.0

**Changelog**
- Aktive Bestellattribute werden nun auch auf der Checkout-Bestätigungsseite geladen (#10)
- Übrig gebliebene Debug-Konsolenausgaben entfernt und Composer-Metadaten für die Shopware-Store-Validierung aktualisiert

# 1.0.0

**Changelog**
- Konfigurierbare Bestellattribute in der Shopware-Administration (**Einstellungen > Erweiterungen > Bestellattribute**) mit übersetzbaren Bezeichnungen und Beschreibungen, Sortierposition, Aktiv-/Pflichtfeld-Schaltern sowie Inline-Bearbeitung hinzugefügt (#2)
- Storefront-AJAX-Formular mit automatischem Speichern für Warenkorb- und Offcanvas-Positionen hinzugefügt; bereinigte Attributwerte werden beim Checkout in den Zusatzfeldern der Bestellpositionen gespeichert
- Hinweis-Icon und Detail-Modal im Bestellpositionen-Grid der Administration zur Anzeige gespeicherter Attributwerte hinzugefügt
