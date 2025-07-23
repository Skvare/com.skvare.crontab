# com.skvare.crontab

![Screenshot](/images/screenshot.gif)

This extension provides advanced job scheduling functionality.

Admins can set Linux CRONTAB-like expressions for each scheduled job.


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

## What is the CiviCRM Crontab Extension?

The Crontab Extension for CiviCRM brings Linux crontab-style scheduling directly into your CiviCRM environment. Instead of being limited to basic frequency settings, you can now define exactly when your scheduled jobs should run using familiar cron expressions.

**Key Features:**
- **Advanced Scheduling**: Use Linux CRONTAB-like expressions for precise job timing
- **Time-Based Execution**: Set specific start and end dates for your jobs
- **Business Hours Control**: Run jobs only during business hours or after hours
- **Flexible Time Margins**: Account for CiviCRM's job execution variations

## Why You Need Advanced Job Scheduling

Standard CiviCRM job scheduling often falls short when you need:
- Jobs to run at specific times (not just intervals)
- Different schedules for different periods
- Business hours-only execution
- Complex scheduling patterns that basic frequency settings can't handle

This extension solves these limitations by giving you granular control over your job execution timing.

## How It Works

### Getting Started
1. **Download and Install**: The extension works with CiviCRM 5.63 or higher
2. **Configure Jobs**: Navigate to your Scheduled Job Listing and edit any job
3. **Enable Advanced Scheduling**: Check the "Advanced Job Scheduling" checkbox to reveal additional options

### Setting Up Your Schedule

**Basic vs. Advanced Settings**
You can choose between basic frequency settings or dive into advanced cron-style expressions for more complex scheduling needs.

**Time Margin Configuration**
Set a "Run Frequency Time Margin" of at least 5 minutes (default). This crucial setting accounts for the fact that while the extension uses cron-like formatting, it still relies on CiviCRM's job execution system. Since your main CiviCRM job might run every 5-10 minutes, this margin ensures your scheduled jobs execute within the expected timeframe.

**Date Range Control**
Define specific start and end dates for your scheduled jobs. This feature is perfect for:
- Campaign-specific automations
- Seasonal job scheduling
- Time-limited processes

**Active Time Windows**
Perhaps the most powerful feature - you can set specific hours when jobs should run. For example:
- Run membership renewal reminders only during business hours (9 AM to 5 PM)
- Process large data imports during off-hours (8 PM to 7 AM)
- Execute email campaigns during optimal engagement times

## Real-World Use Cases

**Membership Organizations**
Schedule renewal reminders to go out only during business hours, ensuring members can immediately call with questions.

**Fundraising Campaigns**
Set donation processing and thank-you emails to run during times that maximize donor engagement.

**Event Management**
Coordinate event reminder emails with registration deadlines and business hour availability.

**Data Processing**
Run heavy database maintenance tasks during off-hours to avoid impacting user experience.

## Technical Considerations

Remember that this extension enhances CiviCRM's existing job system rather than replacing it. Your main CiviCRM cron job still needs to run regularly (every 5-10 minutes is recommended) for the scheduled jobs to execute properly. The time margin setting helps accommodate any variations in your cron execution timing.

## Getting the Extension

The Crontab Extension is available for download and works with CiviCRM 5.63 and higher versions.

## Transform Your CiviCRM Workflow

Stop settling for basic job scheduling that doesn't match your organization's needs. The Crontab Extension puts you in control of when and how your CiviCRM processes run, leading to better user experiences, more efficient operations, and greater peace of mind.

Whether you're a small nonprofit managing member communications or a large organization with complex automation needs, this extension provides the scheduling flexibility that modern CiviCRM implementations demand.

---

**[Contact us](https://skvare.com/contact) for support or to learn more** about implementing the CiviCRM Crontab Extension in your organization.

---

## Related Extensions

You might also be interested in other Skvare CiviCRM extensions:

- **Database Custom Field Check**: Prevents adding custom fields when table limits are reached
- **Image Resize**: Resize images uploaded to CiviCRM with different dimensions
- **Registration Button Label**: Customize button labels on event registration pages

For a complete list of our open source contributions, visit our [GitHub organization page](https://github.com/orgs/Skvare/repositories).
