METHODS :

1. taskView() = renders view for single task, sends all needed forms(add comment, add hours, assign developer)
if subscription is not paid it will redirect manager to payment page.

2. editTask() = updates data for certain task

3. addComment() = adds comment on certain task, including one or more images.

4. assignDevToTask() = assigns one or more developers to a certain task, they are automatically subscribed

5. addHoursToTask() = developers assigne to task can commit hours they worked on that task
and write some kind of commit message, also hours can be billable and not billable.

6. taskCompleted() = changes completed flag on task to true, when completed task can only
be view with all its content and comments but nothing new can be added.

7. taskReopen() = changes completed flag on task to false, task can again be edited, commented on etc.

8. subscribeToTask() = allows user that is not assigned to task to subscribe so he can 
keep up to date (notified by email).

9. completedTaskView() = shows all completed tasks on certain project

10. showTasks() = renders view with all tasks on chosen project

11. addTask() = adds new task into database

12. newStatus() = adds new project status that tasks can be assigned to

13. taskEditor() = renders view with form for task editing

14. developerTasks() = renders view with tasks a certain developer is assigned to.

15. myTasks() = renders view with tasks logged in user is assigned to(only for "ROLE_USER")

