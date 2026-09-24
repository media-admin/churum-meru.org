# Media Lab Project Starter

Project-specific CPTs, taxonomies, and ACF fields for client websites.

## What's Included

- **Custom Post Types**: Team, Project, Job, Service, Testimonial, FAQ, Hero Slides, Carousel, Maps
- **Taxonomies**: Project Categories, Service Categories, Job Categories, Job Types, Job Locations, etc.
- **ACF Field Groups**: 11 field groups (65 fields total) stored as JSON

## Usage

### For Each New Client Project:

1. Duplicate this plugin folder
2. Rename to `client-name-project` (e.g., `acme-corp-project`)
3. Update plugin header in main PHP file
4. Activate for client site
5. Customize CPTs/ACF as needed

## Customization

Add/remove CPTs in `inc/custom-post-types.php`
Add/remove taxonomies in `inc/taxonomies.php`
ACF fields auto-load from `acf-json/`

## Dependencies

Requires: Media Lab Agency Core plugin

## Changelog

### 1.0.1
- Fix: `shortcode-artist-query.php` – Leer-Meldung („Keine Künstler·innen gefunden.") und automatische Trenn-Wellenlinie werden nicht mehr angezeigt, wenn die Künstler-Query kein Ergebnis liefert

## Version

1.0.1
