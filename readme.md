# Trigger Craft Plugin

Kick off deployments only when you need them. 

Tie to cron for scheduled checks, and instantly trigger builds from a Dashboard Widget.

## Setup

1. Install it.
2. Visit Settings, set your deploy webhook URL.
3. Set up a cron job to run `php craft trigger/deploy/check`, which will call your webhook URL only if changes have been made.
4. Hit The Button if you need to immediately trigger a build.

## TODO

- [x] add `trigger/deploy/go` console command
- [x] add `trigger/deploy/check` console command
- [x] set pending flag for Element save, delete, and restore
- [ ] create instant trigger button widget