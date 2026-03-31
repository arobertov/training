.checkout
=========

A Symfony project created on May 17, 2017, 2:11 pm.

---

## Transit Shift Schedule Generator

A Python tool that reads a transit timetable CSV and produces a complete 24-hour shift diagram with coverage for all routes across three shift types: Morning (С), Day (Д), and Night (Н).

### Requirements

- Python 3.10 or later (no external dependencies — uses stdlib only)

### Quick Start

```bash
python main.py
# or with a custom CSV:
python main.py data/schedule_26_01_2026.csv
```

### Project Structure

```
main.py                      — Entry point; runs the full pipeline
data/
  schedule_26_01_2026.csv    — Timetable CSV (semicolon-separated)
src/
  schedule_parser.py         — Parses CSV into route segments
  block_generator.py         — Splits routes into driving blocks (max 2:30)
  shift_scheduler.py         — Assigns blocks to shifts
  validator.py               — Validates all schedule constraints
  output_formatter.py        — Formats output tables
```

### Input Data Format

The CSV uses semicolons as delimiters with three columns:

```
Train;Stations;Arrived
100;>18_1;5:00
100;14_1;5:07
...
```

- `14_1` / `14_2` — Station 14, Track 1 / Track 2 (crew change points)
- `>18_1`, `>05_2` — Terminal stations (route start/end)
- `Depo` — Depot (route start/end)

Routes 101 and 102 are split into morning and evening segments automatically when consecutive `Depo` entries are detected.

### Business Rules

| Rule | Value |
|------|-------|
| Max continuous driving per block | 2 h 30 min |
| Min rest between blocks (same shift) | 50 min |
| Morning shift total | ≤ 5 h |
| Day shift total | ≤ 11 h |
| Night shift total | ≤ 11 h |
| Crew change locations | Station 14 only (14_1 or 14_2) |

**Special combined night shifts** — 107 → 100 and 108 → 101:  
The last block of route 107/108 (evening) and the first block of route 100/101-morning (early morning next day) are covered by the same driver. The driver rests at the terminal station between the two route legs. Total elapsed time is approximately 9–10 hours.

**Routes 101-evening and 102-evening** are treated as Day-shift routes.

### Output

The program prints:

1. **Parsed route segments** — start/end times and stop counts
2. **Shift diagrams** — grouped by type (Morning / Day / Night), each showing boarding station, time, alighting station, driving duration, and rest periods
3. **Shift summary table** — compact overview of all shifts
4. **Coverage matrix** — which shift covers each block of every route
5. **Validation report** — confirms all constraints are satisfied

#### Example shift diagram

```
═════════════════════════════════════════════════════════════════
СМ2-Д (Day Shift)
─────────────────────────────────────────────────────────────────
  Route   │ Board       │ Time   │ Alight      │ Time   │ Drive
  ────────┼─────────────┼────────┼─────────────┼────────┼──────
  107     │ 14_2        │ 8:38   │ 14_1        │ 10:41  │ 2:03
          │ REST        │        │             │        │ 1:24
  103     │ 14_2        │ 12:05  │ 14_2        │ 14:29  │ 2:24
          │ REST        │        │             │        │ 1:18
  106     │ 14_1        │ 15:47  │ 14_1        │ 18:15  │ 2:28
─────────────────────────────────────────────────────────────────
  Total: 8:38 – 18:15 │ Drive: 6:55 │ Shift: 9:37
═════════════════════════════════════════════════════════════════
```

