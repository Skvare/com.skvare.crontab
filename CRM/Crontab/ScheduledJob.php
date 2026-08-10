<?php

// Use Cron.
use CRM_Crontab_ExtensionUtil as E;
require E::path('vendor/autoload.php');

/**
 * Class CRM_Crontab_ScheduledJob.
 *
 * Extends CiviCRM's core scheduled job functionality to support crontab-style
 * frequency expressions. Provides fine-grained control over job scheduling including
 * date/time windows, custom cron expressions, and intra-day time ranges.
 */
class CRM_Crontab_ScheduledJob extends CRM_Core_ScheduledJob {

  public $version = 3;

  public $name = NULL;

  public $apiParams = [];

  public $remarks = [];

  public $crontab_frequency = '';

  public $crontab_date_time_start = '';

  public $crontab_date_time_end = '';

  public $crontab_time_from = '';

  public $crontab_time_to = '';

  public $crontab_apply = '';

  public $crontab_offset = '';

  /**
   * Determines if a scheduled job should run at the current time.
   *
   * Evaluates multiple scheduling strategies in order:
   * 1. One-time scheduled run (specific date/time override)
   * 2. Custom crontab expression (if enabled)
   * 3. Standard CiviCRM frequency settings (Hourly, Daily, Weekly, etc.)
   *
   * @return bool TRUE if job should run, FALSE otherwise
   */
  public function needsRunning() {
    // STRATEGY 1: ONE-TIME SCHEDULED RUN
    // CRM-17686: One-time execution at specific date/time overrides everything
    if (!empty($this->scheduled_run_date)) {
      if (strtotime($this->scheduled_run_date) <= time()) {
        // Scheduled time has arrived or passed - run job and clear the scheduled date
        $this->clearScheduledRunDate();
        return TRUE;
      }
      else {
        // Scheduled time not yet reached - skip this run
        return FALSE;
      }
    }

    // ====================================================================
    // STRATEGY 2: CUSTOM CRONTAB SCHEDULING LOGIC
    // ====================================================================
    // Allows fine-grained scheduling using cron expressions (e.g., "0 9 * * 1-5")
    // Also supports date/time windows and intra-day time range restrictions
    if ($this->crontab_apply && $this->crontab_frequency) {
      // Get current datetime for comparison
      $now = CRM_Utils_Date::currentDBDate();

      // Convert current datetime to timestamp.
      $nowTs = strtotime($now);

      // ────────────────────────────────────────────────────────────────
      // GATE 1: Check if current date is within job's scheduled window
      // ────────────────────────────────────────────────────────────────
      // Prevents job execution outside of its configured date range
      if (!$this->isWithinScheduledWindow($now)) {
        return FALSE;
      }

      // Parse and validate the cron expression
      try {
        $cron = Cron\CronExpression::factory($this->crontab_frequency);
      }
      catch (Exception $e) {
        // Invalid cron expression - log error and skip job
        CRM_Core_Error::debug_log_message('CRM_Crontab_ScheduledJob::needsRunning: Invalid crontab_frequency for job ID ' . $this->id . ' - ' . $this->crontab_frequency);
        return FALSE;
      }
      // Calculate when the job LAST should have run (previous scheduled time)
      // This timestamp represents the most recent time when the job was scheduled
      // to run based on the cron expression
      $lastCronTs = $cron->getPreviousRunDate($now, 0, TRUE)->getTimestamp();

      // ================================================================
      // CASE 2: Job has never run before (first-time execution)
      // ================================================================
      if (empty($this->last_run)) {
        // Get the next scheduled run time based on the cron expression
        $nextCronTs = $cron->getNextRunDate($now)->getTimestamp();

        /**
         * Calculate schedule frequency (gap between previous and next cron run).
         * Used to determine adaptive grace window for initial execution.
         * Allows job to run even if cron daemon is slightly delayed,
         * while preventing early execution.
         *
         * Example: Monthly job (31-day gap)
         * - Grace window = 1% of 31 days = ~7.4 hours
         * - Job can run up to 7.4 hours after scheduled time
         */
        $timeSinceLastCron = $nowTs - $lastCronTs;
        $scheduleGapSeconds = $nextCronTs - $lastCronTs;

        // ──────────────────────────────────────────────────────────────
        // Calculate adaptive grace window based on job frequency
        // ──────────────────────────────────────────────────────────────
        // Grace window prevents missing the scheduled time due to cron timing
        // variations while maintaining tight control for frequent jobs
        // 5 minute minimum grace window
        $minimumGraceSeconds = 300;

        if ($scheduleGapSeconds <= 3600) {
          // High-frequency jobs (every minute to hourly): 5% tolerance
          // Example: Hourly job gets 3-minute grace window
          $percentageGrace = $scheduleGapSeconds * 0.05;
        }
        elseif ($scheduleGapSeconds <= 86400) {
          // Medium-frequency jobs (daily to weekly): 2% tolerance
          // Example: Daily job gets 28-minute grace window
          $percentageGrace = $scheduleGapSeconds * 0.02;
        }
        else {
          // Low-frequency jobs (monthly or less): 1% tolerance
          // Example: Monthly job gets ~7.4 hour grace window
          $percentageGrace = $scheduleGapSeconds * 0.01;
        }

        // Use the larger of minimum or percentage-based grace window
        $graceWindow = max($minimumGraceSeconds, $percentageGrace);

        // ──────────────────────────────────────────────────────────────
        // Check if we're within the grace window
        // ──────────────────────────────────────────────────────────────
        // Ensures:
        // 1. Scheduled time has passed: timeSinceLastCron >= 0
        // 2. We're within acceptable grace period: timeSinceLastCron <= graceWindow
        // 3. Prevents double-runs: Only checks once when last_run is empty
        if ($timeSinceLastCron >= 0 && $timeSinceLastCron <= $graceWindow) {
          return TRUE;
        }
        return FALSE;
      }

      // ────────────────────────────────────────────────────────────────
      // CASE 3: Job has run before - check if it's due to run again
      // ────────────────────────────────────────────────────────────────
      // Compare last execution time against last scheduled time
      $lastRunTs = strtotime($this->last_run);

      // ──────────────────────────────────────────────────────────────
      // Job is due if it hasn't run since the last scheduled time
      // ──────────────────────────────────────────────────────────────
      // This check ensures the job runs after each scheduled time window
      if ($lastRunTs < $lastCronTs) {
        // Job is due - now check if it's within allowed execution hours

        // ────────────────────────────────────────────────────────────
        // GATE 2: Check if current time is within specified time range
        // ────────────────────────────────────────────────────────────
        // Useful for restricting job execution to specific hours of the day
        // Example: Only run overnight jobs between 21:00 and 06:00
        if (!empty($this->crontab_time_from) && !empty($this->crontab_time_to)) {
          // Convert ISO time format (e.g., '21:00:00') to MySQL format (e.g., '210000')
          $start = CRM_Utils_Date::isoToMysql($this->crontab_time_from);
          $end = CRM_Utils_Date::isoToMysql($this->crontab_time_to);
          // Check if current time is within the allowed range
          if (!$this->isWithinTimeRange($start, $end)) {
            // Current time is outside the allowed window - skip this run
            return FALSE;
          }
        }
        // All checks passed - job should run now
        return TRUE;
      }

      // Scheduled time hasn't arrived yet
      return FALSE;
    }
    // END CUSTOM CRONTAB SCHEDULING LOGIC

    // ====================================================================
    // STANDARD CIVICM FREQUENCY SCHEDULING
    // ====================================================================
    // Fallback to standard CiviCRM frequency settings if custom crontab not enabled

    // Run if it was never run.
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

      default:
        CRM_Core_Error::debug_log_message(
          'CRM_Crontab_ScheduledJob::needsRunning: Unknown run_frequency "' .
          $this->run_frequency . '" for job ID ' . $this->id
        );
        return FALSE;
    }

    $now = strtotime(CRM_Utils_Date::currentDBDate());
    $lastTime = strtotime($this->last_run);
    $nextTime = strtotime($offset, $lastTime);

    return ($now >= $nextTime);
  }

  /**
   * Check if current datetime is within the job's scheduled start/end window.
   *
   * Prevents job execution outside of its configured active date range.
   * Useful for enabling/disabling jobs for specific periods without deletion.
   *
   * @param string $nowYmdHis Current date in YmdHis format
   * @return bool TRUE if within scheduled window, FALSE otherwise
   */
  private function isWithinScheduledWindow(string $nowYmdHis): bool {
    // GATE A: Check start date of job
    // Job should not run before this date (activation date)
    if (!empty($this->crontab_date_time_start)) {
      // Convert start date to YmdHis format for comparison
      $startDate = CRM_Utils_Date::processDate($this->crontab_date_time_start);

      // If start date is in the future, job is not yet active
      if ($startDate !== NULL && $startDate >= $nowYmdHis) {
        return FALSE;
      }
    }
    // GATE B: Check end date of job
    // Job should not run after this date (deactivation date)
    if (!empty($this->crontab_date_time_end)) {
      // Convert start date to YmdHis format for comparison
      $endDate = CRM_Utils_Date::processDate($this->crontab_date_time_end);
      // end date should be greater than current date
      if ($endDate !== NULL && $endDate <= $nowYmdHis) {
        return FALSE;
      }
    }
    // All date checks passed - within scheduled window
    return TRUE;
  }

  /**
   * Check if current time falls within the specified time range.
   *
   * Supports time ranges within a single day or across midnight.
   * Useful for restricting job execution to specific hours (e.g., off-hours).
   *
   * Examples:
   * - Start: 09:00, End: 17:00 → Runs during business hours
   * - Start: 21:00, End: 06:00 → Runs overnight (crosses midnight)
   *
   * @param string $start Start time in His format (e.g., '090000')
   * @param string $end End time in His format (e.g., '170000')
   * @return bool TRUE if current time is within range, FALSE otherwise
   */
  private function isWithinTimeRange(string $start, string $end): bool {

    // Get current time in hour minute second format (e.g., '193045' for 19:30:45)
    $now = date("His");

    // Handle time frame that rolls over midnight (e.g., 21:00 to 06:00)
    if ($start > $end) {
      // Current time is within range if:
      // - It's after start time (e.g., after 21:00), OR
      // - It's before end time (e.g., before 06:00)
      // Note: End time is exclusive to avoid duplicate execution at boundary
      if ($now >= $start || $now < $end) {
        return TRUE;
      }
    }
    // Handle time range within the same day
    // Example: 09:00 to 17:00 (business hours)
    elseif ($now >= $start && $now < $end) {
      // Current time is within the specified range
      return TRUE;
    }
    // Current time is outside the allowed range
    return FALSE;
  }

}
