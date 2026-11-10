# Native Leads

A WordPress theme that gives your team a private **leads hub** for the [native-leads landing pages](https://github.com/MohGumaa/native-leads), with a matching public site. It is based on the [Leadora](https://github.com/MohGumaa/leadora-dashboard) theme and [Underscores](https://underscores.me/).

## What it reads

The landing pages (`global`, `invest`, `prospect`…) save every sign-up to the **`wpcq_leads`** table, using the Affiliate API column names (`FirstName`, `Email`, `Phone`, `Country`, `Source`, `UtmSource`, `UtmCampaign`, `Description`, `CrmStatus`, `CreatedDate`…). The theme reads that table directly through `$wpdb`. It doesn't need a sync step or a plugin.

- The table name does not follow the WordPress prefix (`wp_`). To use a different table:

  ```php
  add_filter( 'native_leads_leads_table', fn() => 'my_leads' );
  ```

- `Description` (`Age: 34 | Occupation: Engineer | Contact: whatsapp`) is split into age, occupation and preferred contact.
- `CreatedDate` is written by MySQL, so "today" and "last 7 days" are worked out with the database's own clock (`SELECT NOW()`), not the WordPress timezone.

## Dashboard

Create a page (e.g. "Dashboard"), choose the **Leads Dashboard** template and publish it.

- **Sidebar**: overview and leads links, CSV export, and every landing page with its count (click one to filter).
- **Header**: a date range (all time, today, yesterday, 7 / 30 days, this month, custom) that applies to everything.
- **KPIs**: leads in range, today vs yesterday, last 7 days vs the week before, and the CRM delivery rate with a link to leads that need attention.
- **Leads per day**: an area chart with exact counts on hover.
- **CRM delivery**: delivered / pending / failed / not sent, each one a filter.
- **Breakdowns**: traffic sources (`utm_source`, or Direct), top campaigns, and countries. Click a row to filter.
- **Table**: search (press `/`), filters for landing page, traffic, country and CRM status, sorting and paging. On phones each row becomes a card.
- **Lead drawer**: contact, profile, the full UTM trail, spcid, referrer, CRM attempts and the CRM's last answer. The Call / WhatsApp / Email buttons highlight the method the lead asked for.
- **CSV export**: exports the filtered leads in UTF-8 with a BOM, so Arabic shows correctly in Excel. It is protected against formula injection.

**Access**: administrators always get in. To let another role in, give it the `native_leads_view_leads` capability (e.g. with the Members plugin). The page is never indexed or cached.

## Design

Built around the brand colour **#215073** (deep steel blue) with a tonal scale, a deep-blue sidebar and a split-screen sign-in. Light is the default; dark mode is one click away and remembers each viewer's choice. Fonts are Plus Jakarta Sans (headings), Inter, Tajawal (Arabic) and JetBrains Mono.

| File | Purpose |
| --- | --- |
| `inc/leads.php` | Queries, filters, breakdowns, CSV export, access rules |
| `page-templates/dashboard.php` | The "Leads Dashboard" page template |
| `template-parts/dashboard/` | Sign-in, no-access and the dashboard screen |
| `assets/css/base.css` | Design tokens (light/dark), buttons, badges, brand |
| `assets/css/dashboard.css`, `assets/js/dashboard.js` | Dashboard styles and behaviour |
| `assets/css/site.css`, `assets/js/site.js` | Public site |
| `front-page.php` | Public introduction page |

## License

GPL v2 or later. Based on Underscores, (C) 2012-2020 Automattic, Inc.
