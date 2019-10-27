# Trigger Craft Plugin

Kick off static deployments only when you need to. 

Tie to cron for scheduled checks, and instantly trigger builds from a Dashboard Widget.

## Setup

1. Install it.
2. Visit Settings, set your deploy webhook URL.
3. Set up a cron job to run `php craft trigger/deploy/check`, which will call your webhook URL only if changes have been made.

## Usage

Edit Entries and a build will be triggered automatically within whatever cron interval you set. 

Hit The Button if you need to immediately trigger a build.

Trigger a check from the command line if you need to with `php craft trigger/deploy/go`.

## TODO

- [x] add `trigger/deploy/go` console command
- [x] add `trigger/deploy/check` console command
- [x] set pending flag for Element save, delete, and restore
- [x] make sure draft edits don't trigger build
- [x] add ability to cancel build flag
- [ ] create instant trigger button widget
