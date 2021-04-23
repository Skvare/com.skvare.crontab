# com.skvare.crontab

![Screenshot](/images/screenshot.gif)

This Extension provides adavance Job scheduling functionality.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM (*FIXME: Version number*)

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl com.skvare.crontab@https://github.com/Skvare/com.skvare.crontab/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/Skvare/com.skvare.crontab.git
cv en crontab
```

## Usage

(* FIXME: Where would a new user navigate to get started? What changes would they see? *)

## Known Issues

We are modifying the Scheduled job Object by inheriting same class to override some functions. To achieve this we 
need to apply patch in core file.
```patch
diff --git a/CRM/Utils/Hook.php b/CRM/Utils/Hook.php
index 2c0d195862..3a40a0b366 100644
--- a/CRM/Utils/Hook.php
+++ b/CRM/Utils/Hook.php
@@ -2012,7 +2012,7 @@ abstract class CRM_Utils_Hook {
    * @return null
    *   The return value is ignored.
    */
-  public static function cron($jobManager) {
+  public static function cron(`&$jobManager) {
     return self::singleton()->invoke(['jobManager'],
       $jobManager, self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
       'civicrm_cron'
```