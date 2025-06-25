Gitea Webhook
============

Connect Gitea webhook events to Kanboard automatic actions.

Author
------
- Frederic Guillot (original GogsWebhook plugin)
- Chris Metz
- License MIT

Requirements
------------

- Kanboard >= 1.2.31
- [Gitea](https://gitea.io/)
- Gitea webhooks configured for a project

Installation
------------

You have the choice between 3 methods:

1. Install the plugin from the Kanboard plugin manager in one click
2. Download the zip file and decompress everything under the directory `plugins/GiteaWebhook`
3. Clone this repository into the folder `plugins/GiteaWebhook`

Note: Plugin folder is case-sensitive.

Documentation
-------------

### List of supported events

- Gitea commit reference received
- Gitea commit close received

### List of supported actions

- Create a comment from an external provider
- Close a task

### Configuration

1. On Kanboard, go to the project settings and choose the section **Integrations**
2. Copy the Gitea webhook URL
3. On Gitea, go to the project settings and go to the section **Webhooks**
4. Add a new Gitea webhook and paste the Kanboard URL

### Using the plugin

#### Reference a commit to a Kanboard task

- Choose action: **Create a comment from an external provider**
- Choose the event: **Gitea commit reference received** (or **Gitea commit close received**, see below)

Use the keyword `refs` followed by the taks-reference: `refs #1234`.

The comment will contain the commit message and the URL to the commit. If the email of the commit author matches a Kanboard user, the comment will be attached to this user.


#### Close and/or move a Kanboard task when a commit is pushed to Gitea

- Choose action: **Close the task**
- Choose the event: **Gitea commit close received**

When one or more commits are sent to Gitea, Kanboard will receive the information, each commit message with a task number included will be closed.

The plugin detects the follwing keywords followed by a task-reference: `closes`, `implements`, `fixes`, example: `closes #1234`

If the task should be moved to another column, attach this event to the action **Move the task to another column when assigned to a user** as well. Please note that for this to work, the Kanboard task needs to be assigend to a user and live in the column defined during action setup.

If an additional comment should be created, you can also attach this event to the action **Create a comment from an external provider**
