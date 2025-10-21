# Obsidian to Base44 Sync

Automated sync system that pushes your Obsidian notes to GitHub and triggers updates in your Base44 App.

## Overview

This repository serves as a bridge between your Obsidian vault and your Base44 application. Every time you save a note in Obsidian, it automatically syncs to GitHub, which then triggers an update to your Base44 app within 1-2 minutes.

**Flow:** Obsidian Vault → GitHub → Base44 App

## Prerequisites

- [GitHub Desktop](https://desktop.github.com) (easiest way to manage the repository)
- [Git](https://git-scm.com/downloads) (usually included with GitHub Desktop)
- [Obsidian](https://obsidian.md)
- A Base44 App account

## Setup Instructions

### Step 1: Clone This Repository

1. Click the **Code** button on GitHub and select **Open with GitHub Desktop**
2. Choose a location on your computer to save the repository
3. Click **Clone**

### Step 2: Set Up Your Obsidian Vault

1. Copy all your existing Obsidian notes (`.md` files) into the cloned repository folder
2. Open Obsidian
3. Use **"Open another vault"** → **"Open folder as vault"**
4. Select the cloned repository folder

### Step 3: Install the Obsidian Git Plugin

1. In Obsidian, go to **Settings** → **Community plugins**
2. Turn off **"Safe mode"** if prompted
3. Click **Browse** and search for **"Obsidian Git"**
4. Click **Install**, then **Enable**

### Step 4: Configure the Obsidian Git Plugin

1. Go to **Settings** → **Obsidian Git** (in the Community plugins section)
2. Configure the following settings:
   - **Vault backup interval (minutes)**: Enable and set to `2`
   - **Auto push**: Enable this option
   - **Pull updates on startup**: Enable (recommended)
3. Close settings

### Step 5: Configure GitHub Secrets

For the automated sync to work, you need to add two secrets to your GitHub repository:

1. Go to your repository on GitHub
2. Click **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add the following two secrets:

#### Secret 1: BASE44_APP_ID
- **Name:** `BASE44_APP_ID`
- **Value:** `687b720654af3fa6118f9c96`

#### Secret 2: BASE44_FUNCTION_TOKEN
- **Name:** `BASE44_FUNCTION_TOKEN`
- **Value:** Get this from your Base44 Dashboard
  - Navigate to: **Dashboard** → **Code** → **Functions** → **syncNoteFromGit** → **API & Webhook URLs** → **Bearer Token**
  - Copy the entire token

### Step 6: First Sync

1. Open **GitHub Desktop**
2. You should see all your note files listed under "Changes"
3. In the bottom-left, enter a commit message like: `Initial notes commit`
4. Click **Commit to main**
5. Click **Push origin** at the top of the window

### Step 7: Verify the Setup

1. Make a small change to any note in Obsidian and save it
2. Wait 2 minutes for the auto-sync
3. Check your GitHub repository's **Actions** tab to see the workflow running
4. Verify the note appears in your Base44 app

## How It Works

### Automatic Sync Flow

1. **You save a note** in Obsidian
2. **Obsidian Git plugin** detects the change and commits it every 2 minutes
3. **GitHub receives** the commit and triggers the workflow
4. **GitHub Actions** identifies changed `.md` files and sends them to Base44
5. **Base44 app** receives and processes the updates

### What Gets Synced

- All `.md` (markdown) files in your vault
- File status: `added`, `modified`, or `removed`
- File content (base64 encoded)

### What Doesn't Get Synced

The `.gitignore` file excludes:
- Obsidian workspace and cache files
- Temporary files
- System files (`.DS_Store`, `Thumbs.db`, etc.)

## Troubleshooting

### Notes aren't syncing

1. Check if Obsidian Git is enabled and configured correctly
2. Look for the Git icon in Obsidian's left sidebar
3. Manually trigger a sync by clicking the Git icon
4. Check GitHub Desktop for any sync errors

### GitHub Actions failing

1. Go to the **Actions** tab on GitHub
2. Click on the failed workflow
3. Check the error messages
4. Verify your secrets are set correctly in repository settings

### Notes not appearing in Base44

1. Verify `BASE44_APP_ID` is correct
2. Verify `BASE44_FUNCTION_TOKEN` is valid and not expired
3. Check the webhook URL is accessible
4. Review the GitHub Actions logs for HTTP errors

## Manual Sync

You can manually trigger a sync:

1. **In Obsidian:** Click the Git icon in the sidebar, or use the command palette (Ctrl/Cmd + P) and search for "Obsidian Git: Commit and push"
2. **In GitHub:** Go to the Actions tab → "Sync Obsidian Notes to Base44" → "Run workflow"

## Repository Structure

```
.
├── .github/
│   └── workflows/
│       └── sync-to-base44.yml    # GitHub Actions workflow
├── .obsidian/                     # Obsidian configuration
├── .gitignore                     # Files to exclude from sync
├── README.md                      # This file
└── your-notes.md                  # Your markdown notes
```

## Security

- This repository should be set to **Private** to protect your notes
- The `BASE44_FUNCTION_TOKEN` is stored as a GitHub secret and never exposed
- All communication with Base44 uses HTTPS
- Notes are base64 encoded during transmission

## Support

If you encounter issues:

1. Check the [GitHub Actions logs](../../actions)
2. Review the Obsidian Git plugin settings
3. Verify your Base44 webhook is properly configured
4. Check your GitHub secrets are set correctly

## License

This is a private repository for personal note synchronization.
