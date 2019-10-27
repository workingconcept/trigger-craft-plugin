![Trigger](resources/hero.svg)

<h1 align="center">Trigger Craft CMS 3 Plugin</h1>
<h4 align="center">Kick off static deployments only when you need to.</h4>

---

## Overview

A simple plugin to kick off builds asynchronously, ideal for something like a headless [GatsbyJS](https://www.gatsbyjs.org/) or [Gridsome](https://gridsome.org/) frontend deployed to [Netlify](https://www.netlify.com/) or [Zeit](https://zeit.co/). Use it to add a glorified build buton, or tie it to cron so that changes are grouped and pushed on whatever schedule you define.

## Features

Quick setup for defining a deploy webhook and switching things on or off:
![control panel settings sreenshot](resources/settings.png)

Dashboard widget for instant deploys:
![control panel settings](resources/widget.png)

Run checks or trigger deploys from the command line:

```shell
trigger/deploy/check # Triggers a build if changes are pending.
trigger/deploy/go    # Immediately triggers a deploy build.
```

## Setup

1. Require with `composer require workingconcept/craft-trigger`, then install via CLI or control panel.
2. Visit Settings, set your deploy webhook URL.
3. Set up a cron job to run `craft trigger/deploy/check`, which will call your webhook URL only if changes have been made.
4. Optionally add the Dashboard widget to your layout for quick one-click builds.

## How it Works

Publishing, editing, and deleting Entries will switch on the plugin's _Deploy Waiting_ setting. If that setting is enabled the next time a check runs, a deploy will be triggered and the setting will be switched off again.

You can manually flip on the setting and it'll work the same way, and you can turn it off to cancel the next build if it's not yet been triggered.

Draft edits won't be flagged for changes.

---

## Support

File an issue and I'll try to respond promptly and thoughtfully. This is a free-time project, so I appreciate your patience.

---

This plugin is brought to you by [Working Concept](https://workingconcept.com).
