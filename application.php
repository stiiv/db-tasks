<?php

 $data = "-- find all table that references drzava
SELECT DISTINCT TABLE_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE COLUMN_NAME IN ('drzava_id','drz_id', 'drzave_id')
				AND TABLE_NAME <> 'drzave'
        AND TABLE_SCHEMA='hkig_kom_nova_dev';
-- select all drzave which exist in more than one row 			
SELECT 
	*, 
	GROUP_CONCAT(drzava_id ORDER BY drzava_id) as ids,
	COUNT(*) AS duplih 
FROM drzave 
GROUP BY naziv 
HAVING COUNT(*) > 1 
ORDER BY duplih;";

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \App\Commands\RemoveDoubleRecord());
// ... register commands
$application->run();