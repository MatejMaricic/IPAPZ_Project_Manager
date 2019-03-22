METHODS:

1.mail() = using two foreach loops(one for all tasks and one for all subscribers on each task)
checks updated flag on task(updated is set to true when someone comments on task) and in case its true
sends email to all subscribers on that task.After all emails for task are sent, updated flag is reset to false.
This method is called in CronOnSubscribers console command that is triggered by cron that runs every minute.
