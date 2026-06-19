# Group Monitoring (local_group_monitoring)

## Short description

Group Monitoring is a Moodle local plugin that provides a course-level dashboard to monitor groups, manage polos and link students to pedagogical tutors.

## Main features

- Course dashboard with metrics for groups, polos and tutors.
- Creation of a custom user profile field for polo information when needed.
- Creation and enrolment of tutor users with a reduced course role.
- Creation of monitored groups using the convention `Polo_TutorFirstName_LastNameInitial`.
- Manual addition of students to monitored groups.
- CSV import using one username per line.

## Supported Moodle versions

This plugin requires Moodle 4.1 or later (Build: 2022112800).

Tested during development with Moodle 4.1, 4.2, 4.3 and 4.5.

## Database compatibility

The plugin uses Moodle DML and XMLDB APIs and is intended to be compatible with databases supported by Moodle, including MySQL/MariaDB and PostgreSQL.

## Installation

1. Download the plugin zip file.
2. Extract the contents and make sure the folder is named `group_monitoring`.
3. Upload the `group_monitoring` folder to the `/local/` directory of your Moodle installation.
4. Log in as an administrator and go to **Site administration > Notifications** to complete installation.

No third-party libraries or Composer dependencies are required.

## Configuration

By default, the plugin uses a custom user profile field named `polo`.

To use a different existing custom profile field:

1. Go to **Site administration > Plugins > Local plugins > Group monitoring settings**.
2. Select the user profile field to use as the polo field.
3. Save changes.

## Capabilities

The plugin defines separate capabilities for reading and writing actions:

- `local/group_monitoring:view`
- `local/group_monitoring:creategroup`
- `local/group_monitoring:addmember`
- `local/group_monitoring:createtutor`
- `local/group_monitoring:managepolo`
- `local/group_monitoring:deletegroup`

Write actions are checked again when submitted, not only when buttons are displayed.

## Tutor role

The plugin creates a reduced `tutor` role. It does not copy the Moodle manager role.

The reduced role is limited to course context and receives only the capabilities needed for course/group visibility and access to the plugin dashboard.

## Security and privacy

The plugin follows Moodle security practices by using:

- `require_login()` for course access.
- Course-context capability checks before displaying management actions and before executing submitted actions.
- `require_sesskey()` for submitted actions.
- Moodle parameter cleaning through `required_param()` and `optional_param()`.
- Moodle DML placeholders for custom SQL queries.
- Generic user-facing error messages for unexpected failures.

The plugin stores data in its own tables:

- `local_groupmon_groups`
- `local_groupmon_members`
- `local_groupmon_polos`

Because those tables include user-related data, the plugin implements Moodle's Privacy API in `classes/privacy/provider.php`.

## Source control and issue tracker

Before submitting to the Moodle plugins directory, replace these placeholders with public project URLs:

- Source control: `https://github.com/consulguia-oficial/local_group_monitoring/tree/master`
- Issue tracker: `https://github.com/consulguia-oficial/local_group_monitoring/issues`

A public issue tracker is required for Moodle plugins directory approval.

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or, at your option, any later version.

© 2026 Élson Silva and Júlio Prof / ConsulGuia Tech.
