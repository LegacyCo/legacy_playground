#!/usr/bin/env python3
"""
YouTube Research Tool — powered by yt-dlp
Scrapes video metadata (title, views, author, duration, URL) for a given search query.
"""

import argparse
import json
import sys

import yt_dlp


def format_duration(seconds):
    if seconds is None:
        return "N/A"
    h = seconds // 3600
    m = (seconds % 3600) // 60
    s = seconds % 60
    if h:
        return f"{h}:{m:02d}:{s:02d}"
    return f"{m}:{s:02d}"


def format_views(views):
    if views is None:
        return "N/A"
    if views >= 1_000_000:
        return f"{views / 1_000_000:.1f}M"
    if views >= 1_000:
        return f"{views / 1_000:.1f}K"
    return str(views)


def search_youtube(query: str, count: int = 25, output_format: str = "table") -> list[dict]:
    """
    Search YouTube and return metadata for the top `count` videos.

    Args:
        query:  Search term, e.g. "AI agents 2025"
        count:  Number of results to fetch (default 25)
        output_format: "table" | "json" | "urls"

    Returns:
        List of dicts with keys: rank, title, author, views, duration, url
    """
    ydl_opts = {
        "quiet": True,
        "no_warnings": True,
        "extract_flat": "in_playlist",
        "skip_download": True,
        "playlist_items": f"1-{count}",
    }

    search_url = f"ytsearch{count}:{query}"

    results = []
    with yt_dlp.YoutubeDL(ydl_opts) as ydl:
        info = ydl.extract_info(search_url, download=False)
        entries = info.get("entries", [])

        for i, entry in enumerate(entries, start=1):
            video_id = entry.get("id", "")
            results.append(
                {
                    "rank": i,
                    "title": entry.get("title", "N/A"),
                    "author": entry.get("uploader") or entry.get("channel") or "N/A",
                    "views": entry.get("view_count"),
                    "views_formatted": format_views(entry.get("view_count")),
                    "duration": entry.get("duration"),
                    "duration_formatted": format_duration(entry.get("duration")),
                    "url": f"https://www.youtube.com/watch?v={video_id}" if video_id else entry.get("url", "N/A"),
                    "video_id": video_id,
                }
            )

    return results


def print_table(results: list[dict], query: str) -> None:
    print(f"\n YouTube Search Results for: \"{query}\"")
    print(f" Found {len(results)} video(s)\n")
    print(f"{'#':<4} {'Title':<55} {'Author':<25} {'Views':<10} {'Duration':<10} URL")
    print("-" * 140)
    for r in results:
        title = r["title"][:52] + "..." if len(r["title"]) > 55 else r["title"]
        author = r["author"][:22] + "..." if len(r["author"]) > 25 else r["author"]
        print(
            f"{r['rank']:<4} {title:<55} {author:<25} {r['views_formatted']:<10} "
            f"{r['duration_formatted']:<10} {r['url']}"
        )
    print()


def main():
    parser = argparse.ArgumentParser(
        description="Search YouTube and return video metadata using yt-dlp"
    )
    parser.add_argument("query", help="Search query string")
    parser.add_argument(
        "-n", "--count", type=int, default=25, help="Number of results (default: 25)"
    )
    parser.add_argument(
        "-f",
        "--format",
        choices=["table", "json", "urls"],
        default="table",
        help="Output format (default: table)",
    )
    args = parser.parse_args()

    results = search_youtube(args.query, args.count, args.format)

    if args.format == "json":
        print(json.dumps(results, indent=2))
    elif args.format == "urls":
        for r in results:
            print(r["url"])
    else:
        print_table(results, args.query)


if __name__ == "__main__":
    main()
