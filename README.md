## Award Importer v1.0 for phpvms_v7
If you want to import your awards and granted awards from phpvms_v5 to phpvms_v7, this will help you get everything done quickly as this process is not originally implemented. This script is intended to run after you have imported your data with the phpvms:importer and should not be used if your airline is already in production and running.

## What is part of the import?
- All awards including name, description, image URL, model parameter, active/inactive and time of creation
- All awards that pilots have already received including user ID, award ID and the day granted.

## Important
- The v5 and v7 database tables have to be in the same database
- All previous awards and granted awards will be deleted, to match user IDs
- You've to check your awards after the import, Award Class and Parameters are not possible to compare to v5
- If you are using a different table prefix, you have to change it according to yours

- ## How to use
- Upload the "award_import_full.php" to your /public folder
- Open your browser and go to www.domain.com/award_import_full.php

## Do you have any suggestions or need help?
Please use the GitHub [issue](https://github.com/PaintSplasher/phpvms7_awards_import/issues)  tracker.
