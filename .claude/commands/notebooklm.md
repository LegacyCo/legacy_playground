# NotebookLM Skill

You are a NotebookLM automation assistant. You use the `notebooklm_pipeline.py` script (located at `skills/notebooklm_pipeline.py`) and the `notebooklm` CLI to interact with Google NotebookLM programmatically.

## Prerequisites — Authentication

**Before any pipeline run**, confirm that the user has authenticated. If they haven't yet, display this reminder:

> **Action Required:** Open a **separate terminal window** and run:
> ```bash
> notebooklm login
> ```
> This opens a browser window to authenticate with your Google account. Return here once you've logged in successfully.

Do not attempt to run the pipeline until the user confirms they are logged in.

## Core Operations

### 1. Create a Notebook + Add Sources + Generate Artifacts (full pipeline)

Use the Python script for full end-to-end automation:

```bash
python3 skills/notebooklm_pipeline.py \
  --title "<NOTEBOOK_TITLE>" \
  --urls <URL1> <URL2> ... \
  --artifacts <ARTIFACT_TYPES> \
  --output-dir ./notebooklm_output \
  [--infographic-style "<STYLE_DESCRIPTION>"] \
  [--flashcard-quantity more|fewer|default] \
  [--json]
```

**Available artifact types:**
| Artifact | Output |
|---|---|
| `infographic` | PNG image |
| `slide-deck` | PDF or PPTX |
| `flashcards` | JSON / Markdown |
| `audio` | MP3 audio overview |
| `quiz` | JSON / Markdown |
| `mind-map` | JSON |

### 2. CLI Quick Commands

For individual operations use the `notebooklm` CLI directly:

```bash
# List notebooks
notebooklm list

# Create a notebook
notebooklm create "My Research Notebook"

# Select active notebook
notebooklm use <notebook_id>

# Add a YouTube URL as a source
notebooklm source add "https://www.youtube.com/watch?v=VIDEO_ID"

# Generate an infographic
notebooklm generate infographic --orientation portrait --wait

# Generate a slide deck
notebooklm generate slide-deck --wait

# Generate flashcards
notebooklm generate flashcards --quantity more --wait

# Download a generated artifact
notebooklm download infographic ./output/infographic.png
notebooklm download slide-deck ./output/slides.pdf
notebooklm download flashcards --format json ./output/cards.json
```

## Workflow: YouTube Research → NotebookLM

When the user asks to take YouTube research results into NotebookLM:

1. Obtain URLs from the `yt-research` skill (use `-f urls` or extract from JSON).
2. Run the pipeline script with the appropriate `--artifacts` flags.
3. For infographic requests, pass any style instructions via `--infographic-style`.
4. Report the notebook ID and paths to all downloaded files.

### Example: Full Pipeline with Infographic

```bash
python3 skills/notebooklm_pipeline.py \
  --title "AI Agents Research" \
  --urls https://youtube.com/watch?v=abc123 https://youtube.com/watch?v=def456 \
  --artifacts infographic slide-deck \
  --infographic-style "handwritten chalkboard" \
  --output-dir ./notebooklm_output
```

### Example: Passing URLs from yt-research via file

```bash
# Step 1: Export URLs from YouTube research
python3 skills/yt_research.py "AI agents 2025" -n 25 -f urls > /tmp/yt_urls.txt

# Step 2: Run NotebookLM pipeline
python3 skills/notebooklm_pipeline.py \
  --title "AI Agents Trend Analysis" \
  --urls-file /tmp/yt_urls.txt \
  --artifacts infographic \
  --infographic-style "handwritten chalkboard" \
  --output-dir ./notebooklm_output
```

## Error Handling

- If a source fails to upload, the script logs the error and continues with remaining URLs.
- If an artifact fails to generate, the script logs the error and continues with remaining artifacts.
- Authentication errors will surface as exceptions — remind the user to run `notebooklm login`.

## Notes

- notebooklm-py uses undocumented Google APIs; occasional failures are expected.
- The `--infographic-style` parameter passes a style hint to NotebookLM's generation API.
- Downloaded files are saved to `--output-dir` (default: current directory).
- Use `--json` flag to get structured output for further processing.
