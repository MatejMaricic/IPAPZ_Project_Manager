METHODS:

1.singleDiscussionView() = renders template for single discussions and sends all needed forms
(add new comment on discussion and convert discussion to task).

2.addDiscussionComment() = adds new comment for logged in user on given discussion, if available uploads image.

3.convertToTask() = creates new Task object, populates it with data from submitted form(status, priority, estimated time),
adds data from chosen disscusion to that object(name, content, images)
removes discussionId in subscriptions and comments tables and adds taskId.

4.showDiscussion() = renders view for all discussions on certain project.

5.newDiscussion() = creates new discussion on project you are currently on

6.subscribeToDiscussion() = adds subscribe button for all users that are not already assigned to that
discussion

7.deleteDiscussion() = removes chosen discussion using ajax.
