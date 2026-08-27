# Site snapshot (opcionális)

Ez a mappa a `php artisan site:export-snapshot` parancs kimenetének helye.

A **Fügefa** projekt alapértelmezetten **nem** tölti be a snapshotot – csak a `FugefaBaselineSeeder` üres kiinduló állapotát.

Ha később exportált tartalmat szeretnél verziókövetni:

1. Lokálisan állítsd össze az oldalt az adminban.
2. Futtasd: `php artisan site:export-snapshot`
3. Commitold a JSON fájlokat.
4. Deploy után: `php artisan db:seed --class=CurrentSiteSnapshotSeeder`

Képek: `public/images/site` és `storage/app/public`.
