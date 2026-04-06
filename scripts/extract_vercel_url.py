#!/usr/bin/env python3
import argparse
import re
import sys


def extract_vercel_url(text: str) -> str | None:
    patterns = [
        r'https://[a-zA-Z0-9_-]+-[a-zA-Z0-9_-]+-[^\s\x1b\[;]+\.vercel\.app',
        r'https://[a-zA-Z0-9_-]+\.vercel\.app',
        r'Production:\s+(https://[^\s]+)',
        r'Preview:\s+(https://[^\s]+)',
        r'https://[^\s\x1b\[;]+\.vercel\.app',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, text)
        if match:
            url = match.group(0) if '(' not in pattern else match.group(1)
            url = url.rstrip('/')
            if '.vercel.app' in url:
                return url
    
    return None


def main() -> int:
    parser = argparse.ArgumentParser(
        description='Extract Vercel deployment URL from deploy output or file'
    )
    parser.add_argument(
        'input',
        nargs='?',
        help='Path to file containing deploy output, or - for stdin'
    )
    
    args = parser.parse_args()
    
    if args.input is None or args.input == '-':
        text = sys.stdin.read()
    else:
        try:
            with open(args.input, 'r', encoding='utf-8') as f:
                text = f.read()
        except FileNotFoundError:
            print(f"Error: File not found: {args.input}", file=sys.stderr)
            return 1
        except IOError as e:
            print(f"Error reading file: {e}", file=sys.stderr)
            return 1
    
    url = extract_vercel_url(text)
    
    if url is None:
        print("Error: Could not find Vercel URL in input", file=sys.stderr)
        return 1
    
    print(url)
    return 0


if __name__ == '__main__':
    sys.exit(main())
