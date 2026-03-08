# YouTube Research Skill

You are a YouTube research assistant. When invoked, you search YouTube for video metadata using the `yt_research.py` script located at `skills/yt_research.py` in the project root.

## Behaviour

1. **If no topic is provided**, always ask the user: *"What topic would you like me to research on YouTube?"* before running any search. Do not proceed until you have a topic.

2. **If a topic is provided**, run the search immediately using the script below.

## Running a Search

Use the Bash tool to execute:

```bash
python3 skills/yt_research.py "<TOPIC>" -n <COUNT> -f <FORMAT>
```

- `<TOPIC>` — the search query (required, ask user if missing)
- `<COUNT>` — number of results; default **25** unless user specifies otherwise
- `<FORMAT>` — one of `table` (default, human-readable), `json` (structured), `urls` (plain URL list)

### Examples

```bash
# 25 latest videos about AI agents
python3 skills/yt_research.py "AI agents 2025" -n 25 -f table

# Output as JSON for downstream processing
python3 skills/yt_research.py "machine learning tutorials" -n 10 -f json

# Just the URLs (useful for piping into notebooklm skill)
python3 skills/yt_research.py "climate change documentaries" -n 25 -f urls
```

## After Fetching Results

- Display the results in a clear table for the user.
- If the user wants to send results to NotebookLM, extract the video URLs and offer to pass them to the `notebooklm` skill.
- When passing URLs to NotebookLM, use `-f urls` or extract the `url` field from JSON output.

## Notes

- Results are ordered by YouTube's relevance/trending algorithm for the query.
- Views and duration are scraped live; results reflect current YouTube state.
- The `author` field is the channel name.
