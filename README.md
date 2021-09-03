# com.skvare.crontab

![Screenshot](/images/screenshot.gif)

This Extension provides advanced Job scheduling functionality.

Admin can set Linux CRONTAB-like expressions for each scheduled job.


The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.6+
* CiviCRM 5.27+

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

* Go to Scheduled job listing -> Edit any job -> select checkbox `Advanced Job
 Scheduling`, then additional details get displayed
* Set you Job frequency either using Basic Setting or Advanced Setting.
* Set `Run Frequency Time Margin` for at least 5 min (default value is 5)
    > * We are using Linux Cron tab like format to run the cron job on 
    specific time, but this is not actual crontab, we are totally relying on CiviCRM Job, CiviCRM job may be executed by crontab like feature.
    > * We may run a CiviCRM job every 5 or 10 minutes. But this will not be 
    executed on set scheduled time. So we have to give some relaxation using this margin setting.
    > * Provide margin in number of minutes based on what is crontab frequency of main civicrm Job. If empty we will use 5 minute as the default margin.
* Set scheduled start and end date.
    > * The Scheduled job only gets executed during the specified date.
* Set Active Time of each scheduled job.
    > * We can run scheduled jobs every 30 minutes but only execute in business hours from 9 AM to 5 PM, Or run after business hours from 8 PM to next morning 7 AM..
