METHODS:

1. projectHandler() = renders view for certain project, on this page you can choose between 
tasks and discussions on this project. In case subscription is not paid, manager is redirected to payment page.

2. editProject() = updates project data into db.

3. assignDev() = manager can assign one or more of his developers to a certain project.

4. statusChange() = changes status of a task on project by choosing new status with selector
this is done with ajax. Also updates data into db.

5. projectEditor() = renders view with edit form for chosen project

6. completeProject() = sets completed flag on project to true, while completed 
all project data can be viewed but cannot be changed.

7. projectReopen() = sets completed flag to false, project can be used again.

8. completedProjectsView() = renders view with all completed projects.

9. deleteProject() = deletes project using ajax.

