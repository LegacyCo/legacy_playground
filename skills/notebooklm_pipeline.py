#!/usr/bin/env python3
"""
NotebookLM Pipeline — powered by notebooklm-py
Creates a notebook, uploads YouTube URLs as sources, generates deliverables,
and downloads the output files.
"""

import argparse
import asyncio
import json
import sys
from pathlib import Path

from notebooklm import NotebookLMClient


ARTIFACT_TYPES = {
    "infographic": "generate_infographic",
    "slide-deck": "generate_slide_deck",
    "flashcards": "generate_flashcards",
    "audio": "generate_audio_overview",
    "quiz": "generate_quiz",
    "mind-map": "generate_mind_map",
}

DOWNLOAD_METHODS = {
    "infographic": ("download_infographic", "png"),
    "slide-deck": ("download_slide_deck", "pdf"),
    "flashcards": ("download_flashcards", "json"),
    "audio": ("download_audio_overview", "mp3"),
    "quiz": ("download_quiz", "json"),
    "mind-map": ("download_mind_map", "json"),
}


async def create_notebook_with_sources(
    title: str,
    urls: list[str],
    artifacts: list[str],
    output_dir: str = ".",
    infographic_style: str | None = None,
    flashcard_quantity: str = "default",
    notebook_id: str | None = None,
) -> dict:
    """
    Full pipeline: create notebook → add sources → generate artifacts → download.

    Returns a dict with notebook_id and paths to downloaded files.
    """
    results = {"notebook_id": None, "title": title, "sources_added": [], "artifacts": {}}

    async with await NotebookLMClient.from_storage() as client:
        # ── 1. Create or reuse notebook ──────────────────────────────────────
        if notebook_id:
            print(f"[notebooklm] Reusing existing notebook: {notebook_id}")
            nb_id = notebook_id
        else:
            print(f"[notebooklm] Creating notebook: \"{title}\"")
            nb = await client.notebooks.create(title)
            nb_id = nb.id
            print(f"[notebooklm] Notebook created → id={nb_id}")

        results["notebook_id"] = nb_id

        # ── 2. Add YouTube URLs as sources ────────────────────────────────────
        for url in urls:
            print(f"[notebooklm] Adding source: {url}")
            try:
                await client.sources.add_url(nb_id, url, wait=True)
                results["sources_added"].append(url)
                print(f"[notebooklm]   ✓ Added")
            except Exception as e:
                print(f"[notebooklm]   ✗ Failed: {e}", file=sys.stderr)

        print(f"[notebooklm] {len(results['sources_added'])}/{len(urls)} sources added successfully.")

        # ── 3. Generate requested artifacts ───────────────────────────────────
        out_path = Path(output_dir)
        out_path.mkdir(parents=True, exist_ok=True)

        for artifact in artifacts:
            if artifact not in ARTIFACT_TYPES:
                print(f"[notebooklm] Unknown artifact type '{artifact}', skipping.", file=sys.stderr)
                continue

            print(f"[notebooklm] Generating {artifact}…")
            generate_method = getattr(client.artifacts, ARTIFACT_TYPES[artifact])

            try:
                kwargs = {}
                if artifact == "infographic" and infographic_style:
                    kwargs["style"] = infographic_style
                if artifact == "flashcards" and flashcard_quantity != "default":
                    kwargs["quantity"] = flashcard_quantity

                status = await generate_method(nb_id, **kwargs)
                await client.artifacts.wait_for_completion(nb_id, status.task_id)
                print(f"[notebooklm]   ✓ {artifact} generated.")

                # ── 4. Download ────────────────────────────────────────────────
                dl_method_name, ext = DOWNLOAD_METHODS[artifact]
                dl_method = getattr(client.artifacts, dl_method_name)
                safe_title = "".join(c if c.isalnum() or c in " _-" else "_" for c in title)
                file_path = out_path / f"{safe_title}_{artifact}.{ext}"

                await dl_method(nb_id, str(file_path))
                results["artifacts"][artifact] = str(file_path)
                print(f"[notebooklm]   ✓ Downloaded → {file_path}")

            except Exception as e:
                print(f"[notebooklm]   ✗ Failed to generate/download {artifact}: {e}", file=sys.stderr)
                results["artifacts"][artifact] = None

    return results


def main():
    parser = argparse.ArgumentParser(
        description="NotebookLM pipeline: create notebook, add YouTube sources, generate deliverables"
    )
    parser.add_argument("--title", required=True, help="Notebook title")
    parser.add_argument(
        "--urls",
        nargs="+",
        help="YouTube URLs to add as sources (space-separated)",
    )
    parser.add_argument(
        "--urls-file",
        help="Path to a text file with one URL per line",
    )
    parser.add_argument(
        "--artifacts",
        nargs="+",
        default=[],
        choices=list(ARTIFACT_TYPES.keys()),
        help="Artifacts to generate (space-separated)",
    )
    parser.add_argument(
        "--output-dir",
        default=".",
        help="Directory to save downloaded files (default: current dir)",
    )
    parser.add_argument(
        "--infographic-style",
        default=None,
        help="Style hint for infographic generation, e.g. 'handwritten chalkboard'",
    )
    parser.add_argument(
        "--flashcard-quantity",
        default="default",
        choices=["default", "more", "fewer"],
        help="Number of flashcards to generate",
    )
    parser.add_argument(
        "--notebook-id",
        default=None,
        help="Reuse an existing notebook by ID instead of creating a new one",
    )
    parser.add_argument(
        "--json",
        action="store_true",
        help="Print final results as JSON",
    )

    args = parser.parse_args()

    # Collect URLs
    urls = list(args.urls or [])
    if args.urls_file:
        with open(args.urls_file) as f:
            urls += [line.strip() for line in f if line.strip()]

    if not urls:
        parser.error("Provide at least one URL via --urls or --urls-file")

    results = asyncio.run(
        create_notebook_with_sources(
            title=args.title,
            urls=urls,
            artifacts=args.artifacts,
            output_dir=args.output_dir,
            infographic_style=args.infographic_style,
            flashcard_quantity=args.flashcard_quantity,
            notebook_id=args.notebook_id,
        )
    )

    if args.json:
        print(json.dumps(results, indent=2))
    else:
        print("\n── Pipeline Complete ──────────────────────────────────────")
        print(f"Notebook ID : {results['notebook_id']}")
        print(f"Sources     : {len(results['sources_added'])} added")
        for artifact, path in results["artifacts"].items():
            status = path if path else "FAILED"
            print(f"{artifact:<14}: {status}")
        print("────────────────────────────────────────────────────────────\n")


if __name__ == "__main__":
    main()
