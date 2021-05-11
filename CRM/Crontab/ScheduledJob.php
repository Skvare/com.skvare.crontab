<?php
//use Cron;
require_once 'vendor/autoload.php';

class CRM_Crontab_ScheduledJob extends CRM_Core_ScheduledJob {

  public $version = 3;

  public $name = NULL;

  public $apiParams = [];

  public $remarks = [];

  /**
   * @return bool
   */
  public function needsRunning() {
    // CRM-17686
    // check if the job has a specific scheduled date/time
    if (!empty($this->scheduled_run_date)) {
      if (strtotime($this->scheduled_run_date) <= time()) {
        $this->clearScheduledRunDate();
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    // Custom Code start
    if ($this->crontab_frequency && $this->crontab_frequency != '* * * * *') {
      $cron = Cron\CronExpression::factory($this->crontab_frequency);
      // check cron is Due to Run.
      $isDue = $cron->isDue('now', $this->crontab_offset);
      if ($isDue) {
        // if cron job is recent ran,
        // check its no run within offset time period (+/ - offset minutes from last run date time.
        // default offset time is 5 min.
        $crontab_offset = $this->crontab_offset ?? 5;
        if (!empty($this->last_run)) {
          $lastTime = strtotime($this->last_run);
          $currentDate = date('Y-m-d H:i:s');
          $currentTime = strtotime($currentDate, date('Y-m-d H:i:s'));
          // add + - time to avoid repeat executing of cron job
          $crontab_offset += $crontab_offset;
          $lastRunTimeDiff = $currentTime >= $lastTime && (($currentTime - $lastTime) <= ($crontab_offset * 60));
          if ($lastRunTimeDiff) {
            return FALSE;
          }
        }

        return TRUE;
      }

      return FALSE;
    }
    // Custom Code End

    // run if it was never run
    if (empty($this->last_run)) {
      return TRUE;
    }

    // run_frequency check
    switch ($this->run_frequency) {
      case 'Always':
        return TRUE;

      // CRM-17669
      case 'Yearly':
        $offset = '+1 year';
        break;

      case 'Quarter':
        $offset = '+3 months';
        break;

      case 'Monthly':
        $offset = '+1 month';
        break;

      case 'Weekly':
        $offset = '+1 week';
        break;

      case 'Daily':
        $offset = '+1 day';
        break;

      case 'Hourly':
        $offset = '+1 hour';
        break;
    }

    $now = strtotime(CRM_Utils_Date::currentDBDate());
    $lastTime = strtotime($this->last_run);
    $nextTime = strtotime($offset, $lastTime);

    return ($now >= $nextTime);
  }


}
