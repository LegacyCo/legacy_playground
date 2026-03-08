# legacy_playground — Claude Code Project Context

## Custom Skills

Two custom skills are registered in `.claude/commands/` and are available for use in this project:

### `/yt-research` — YouTube Research Skill
Searches YouTube and returns video metadata (title, author, views, duration, URL).

**Script:** `skills/yt_research.py`
**Dependency:** `yt-dlp`

Quick usage:
```bash
python3 skills/yt_research.py "YOUR TOPIC" -n 25 -f table
python3 skills/yt_research.py "YOUR TOPIC" -n 25 -f json
python3 skills/yt_research.py "YOUR TOPIC" -n 25 -f urls
```

> **Important:** Always ask for a topic if none is provided before running a search.

---

### `/notebooklm` — NotebookLM Pipeline Skill
Creates notebooks, uploads YouTube URLs as sources, generates and downloads deliverables (infographic, slide deck, flashcards, audio overview, quiz, mind map).

**Script:** `skills/notebooklm_pipeline.py`
**Dependency:** `notebooklm-py[browser]` + `playwright` (chromium)

Quick usage:
```bash
python3 skills/notebooklm_pipeline.py \
  --title "My Research" \
  --urls https://youtube.com/watch?v=... \
  --artifacts infographic slide-deck \
  --infographic-style "handwritten chalkboard" \
  --output-dir ./notebooklm_output
```

> **Authentication required:** Run `notebooklm login` in a separate terminal before using this skill.

---

## Combined Research → Analysis Pipeline

The intended end-to-end workflow:

```bash
# Step 1: Scrape YouTube URLs for a topic
python3 skills/yt_research.py "YOUR TOPIC" -n 25 -f urls > /tmp/yt_urls.txt

# Step 2: Send to NotebookLM, generate infographic
python3 skills/notebooklm_pipeline.py \
  --title "YOUR TOPIC Research" \
  --urls-file /tmp/yt_urls.txt \
  --artifacts infographic \
  --infographic-style "handwritten chalkboard" \
  --output-dir ./notebooklm_output
```

You can trigger this whole flow by saying:
> *"Use the yt-research skill to find the 25 latest trending videos on [TOPIC]. Send them to NotebookLM and create an infographic in a handwritten/chalkboard style."*

---

## Setup

### Install dependencies (one-time)
```bash
pip install yt-dlp "notebooklm-py[browser]"
playwright install chromium
```

### Authenticate with NotebookLM (one-time per session)
Open a **separate terminal** and run:
```bash
notebooklm login
```
Follow the browser prompts to sign in with your Google account.
