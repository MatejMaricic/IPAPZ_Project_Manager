METHODS: 

1.indexHandler() = renders view for index page, sends all needed forms, in case subscription is not paid
redirects admin to payment page. Also redirects owner to admin template where he can see all projects.

2.newProject() = creates new Project assigned to manager that created it.

3.addDeveloper() = adds new developer to database, developer is assigned to manager that added it
but not on any projects.

4.showProfile() = redirects user to his profile page where he can edit his data.

5.updateUser() = updates user data sent by showProfile() into database.

6.hoursManagementView() = renders view for project manager where he can choose certain
developer/project to update hours submitted by developers.

7.projectHoursManagement() = renders view with all hours spent on certain project
manager can filter data by month and can export that data in pdf format.

8.findHoursByCriteria() = filters hours for chosen user by project and time frame.

9.findHoursByDate() = filters hours for chosen project based on chosen month. 

10.userHoursManagement() = renders view with all commited hours for certain developer
this data can be edited.

11.editUserHours() = updates databes with edited hours for certain user.

12.myPayments() = renders view with all payment data for logged in manager.