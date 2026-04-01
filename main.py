#!/usr/bin/env python3
"""
main.py — Entry point for the Transit Shift Schedule Generator.

Usage:
    python main.py [path/to/csv]

If no CSV path is provided, defaults to data/schedule_26_01_2026.csv.
"""

import sys
import os

# Allow imports from src/ regardless of working directory
_ROOT = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, os.path.join(_ROOT, "src"))

from schedule_parser import parse_csv
from block_generator import generate_all_blocks
from shift_scheduler import assign_shifts, MORNING, DAY, NIGHT
from validator import validate
from output_formatter import (
    format_shift,
    format_summary_table,
    format_coverage_matrix,
    format_validation_report,
    format_counts,
)
from diagram_generator import generate_diagram


def main():
    csv_path = sys.argv[1] if len(sys.argv) > 1 else os.path.join(_ROOT, "data", "schedule_26_01_2026.csv")

    print(f"Reading timetable: {csv_path}")
    segments = parse_csv(csv_path)
    print(f"Parsed {len(segments)} route segments:")
    for seg in segments:
        print(
            f"  {seg.route_id:<16}  {seg.stops[0].station}({seg.stops[0].time_str()}) → "
            f"{seg.stops[-1].station}({seg.stops[-1].time_str()})"
            f"  [{len(seg.stops)} stops]"
        )

    print("\nGenerating driving blocks...")
    all_blocks = generate_all_blocks(segments)
    print(f"Generated {len(all_blocks)} blocks")

    print("\nAssigning blocks to shifts...")
    shifts = assign_shifts(all_blocks)

    morning_count = sum(1 for s in shifts if s.shift_type == MORNING)
    day_count = sum(1 for s in shifts if s.shift_type == DAY)
    night_count = sum(1 for s in shifts if s.shift_type == NIGHT)
    print(f"Created {len(shifts)} shifts: {morning_count}xMorning + {day_count}xDay + {night_count}xNight")

    # Print shift details
    print("\n" + "=" * 65)
    print("MORNING SHIFTS (С)")
    print("=" * 65)
    for s in sorted(shifts, key=lambda x: x.start_time()):
        if s.shift_type == MORNING:
            print(format_shift(s))

    print("\n" + "=" * 65)
    print("DAY SHIFTS (Д)")
    print("=" * 65)
    for s in sorted(shifts, key=lambda x: x.start_time()):
        if s.shift_type == DAY:
            print(format_shift(s))

    print("\n" + "=" * 65)
    print("NIGHT SHIFTS (Н)")
    print("=" * 65)
    for s in sorted(shifts, key=lambda x: x.start_time()):
        if s.shift_type == NIGHT:
            print(format_shift(s))

    # Summary
    print(format_counts(shifts))
    print(format_summary_table(shifts))

    # Coverage matrix
    all_route_ids = [seg.route_id for seg in segments]
    print(format_coverage_matrix(shifts, all_route_ids))

    # Validation
    print("\nValidating schedule...")
    result = validate(shifts, all_blocks)
    print(format_validation_report(result))

    # Diagram
    print("\nGenerating shift diagram...")
    diagram_path = os.path.join(_ROOT, "output", "shift_diagram.png")
    generate_diagram(shifts, segments=segments, output_path=diagram_path)

    return 0 if result.ok else 1


if __name__ == "__main__":
    sys.exit(main())
