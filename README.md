z-wave.Me - Developer Console
===============

## v1.0.1
#### New features
- Comments: send email when new comment has been posted.
- Module detail: added statistics for apps downloaded, commented etc.
- Modules: added new comments icon.

#### Fixes
- ???

#### Changes
- New column "isnew" added into comments table.
- ALTER TABLE `comments` ADD COLUMN `isnew` TINYINT NOT NULL DEFAULT '1' AFTER `name`;

## v1.0.0
- Released.