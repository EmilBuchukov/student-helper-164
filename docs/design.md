# 164 ГПИЕ — design brief

Editorial-meets-app: a well-made modern product with a Spanish/Cervantine point of view.
Warm paper, ink-black type, Spanish red used sparingly for action, gold as a highlight.

## Palette

All colours are CSS variables (`resources/css/tokens.css`) exposed to Tailwind as semantic names
(`bg-canvas`, `text-ink`, `bg-primary`, …). Dark mode only swaps the variables (`.dark` on `<html>`).

### Brand scales

| step | Red (`red`) | Gold (`gold`) | Sand (warm neutral) |
| --- | --- | --- | --- |
| 50 | `#FDF3F2` | `#FFFBEA` | `#FBF6EC` (paper) |
| 100 | `#FBE4E2` | `#FFF3C4` | `#F4ECDD` |
| 200 | `#F6C6C2` | `#FFE585` | `#E8DDCB` |
| 300 | `#EE9C95` | `#FFD43D` | `#D3C5AE` |
| 400 | `#E2665E` | `#F1BF00` (flag gold) | `#AE9F88` |
| 500 | `#CC3A33` | `#D4A200` | `#8A7B68` |
| 600 | `#AA151B` (flag red) | `#A87C00` | `#6A5D4E` |
| 700 | `#8E1117` | `#7E5B03` | `#4F443A` |
| 800 | `#741016` | `#5E450A` | `#352D27` |
| 900 | `#5E1115` | `#4A370D` | `#241E1A` |
| 950 | `#340608` | `#2A1E04` | `#1A1412` (ink) |

### Semantic tokens

| token | light | dark | use |
| --- | --- | --- | --- |
| `canvas` | `#FBF6EC` | `#17120F` | page background |
| `surface` | `#FFFDF8` | `#211A16` | raised panels |
| `sunken` | `#F4ECDD` | `#2A221D` | inputs-on-surface, zebra, sidebars |
| `line` / `line-strong` | `#E8DDCB` / `#D3C5AE` | `#3A302A` / `#54473D` | rules, borders |
| `ink` / `ink-muted` / `ink-subtle` | `#1A1412` / `#4F443A` / `#6A5D4E` | `#F5EEE3` / `#CDBFAE` / `#A89A8B` | text |
| `primary` / `primary-hover` / `on-primary` | `#AA151B` / `#8E1117` / `#FFFFFF` | `#E0473F` / `#EA6159` / `#1A0707` | primary actions |
| `primary-ink` | `#AA151B` | `#F28B82` | red text & active nav |
| `primary-soft` | `#FBE4E2` | `#3A1917` | tinted backgrounds |
| `accent` / `on-accent` | `#F1BF00` / `#1A1412` | `#F1BF00` / `#1A1412` | gold badges, highlights |
| `accent-soft` / `accent-ink` | `#FFF3C4` / `#6B4D00` | `#3A2F0C` / `#F5CF3D` | accepted answers, highlights |
| `success` / `warning` / `danger` / `info` (ink) | `#2F6B3A` / `#8A4B00` / `#B3261E` / `#1D5C80` | `#86D094` / `#F0B15A` / `#FF8A80` / `#8CC4E6` | status text |
| `*-soft` | `#E6F2E3` / `#FCEFD9` / `#FDE7E4` / `#E3EEF5` | `#1C2E1F` / `#35260F` / `#3B1714` / `#172833` | status backgrounds |
| `focus` | `#AA151B` | `#F1BF00` | focus rings |

Rules: gold is never body text on light backgrounds (it fails contrast) — use `accent-ink` instead.
Red is reserved for primary actions, active states and small accents; large areas stay neutral.
Info uses an azulejo blue so it harmonises with the Spanish palette instead of default Tailwind blue.
All text/UI pairs above meet WCAG AA (checked with a contrast script; body pairs ≥ 7:1, muted ≥ 4.5:1).

## Typography

- **Display — Source Serif 4** (variable, `wght`). A literary text-serif with real Bulgarian `locl`
  forms (checked in the font's GSUB: `cyrl/BGR` → `locl`). It gives headings the bookish, Cervantine
  voice without the costume feel of Playfair/Cormorant (Playfair and Literata have no Bulgarian `locl`).
- **Text/UI — Geologica** (variable, `wght`). A Cyrillic-first grotesque with a slightly geometric,
  contemporary voice and Bulgarian `locl`; far more distinctive than Inter, still very legible at 14 px.
- Self-hosted from `@fontsource-variable/*`, only the `latin` + `cyrillic` subsets, `font-display: swap`.
  `<html lang="bg">` turns on the Bulgarian alternates (д, ж, з, и, й, к, л, т, ю …).
- Fluid scale (`clamp()`): `display` 2.5→5.25 rem, `title-1` 2→3.25 rem, `title-2` 1.5→2.125 rem,
  `title-3` 1.25→1.5 rem, body 1 rem / 1.6. Large headings use −0.02 em tracking; reading text is capped
  at ~68 ch.
- Kicker labels: small, uppercase, letter-spaced, red, with a short gold rule — the recurring "editorial" cue.

## Space, shape, depth

- 4 px spacing grid (Tailwind default); sections breathe with 48–96 px vertical rhythm.
- Radius system: **sharp by default** — `rounded` = 4 px for controls/panels, `rounded-sm` = 2 px for
  tags; the only soft shapes are pills (badges, avatars, chat bubbles at 14 px) and dialogs (10 px).
- Depth comes from thin rules and paper tones, not shadows. Two warm shadows only: `shadow-hairline`
  (panels) and `shadow-pop` (menus, dialogs, toasts).
- Accents: 3-band rojigualda strip (red/gold/red) on the top edge of the layout; an 8-point azulejo star
  pattern (inline SVG, < 1 KB) used only on brand panels (landing, auth aside, error pages), never under text.

## Components (`resources/js/Components/ui/`)

Button, IconButton, Input, Textarea, Select (native, styled), Checkbox, Badge, Avatar, Surface,
Tabs, Dialog, ConfirmDialog (+ `useConfirm()`), DropdownMenu, Toaster (+ `toast()`), Tooltip, Skeleton,
EmptyState, Pagination, DataTable, FormField, PageHeader, plus brand pieces: Wordmark, Azulejo, DateBlock,
Kicker, FileDrop, ThemeToggle. Variants via `class-variance-authority`, class merging via `cn()`
(`clsx` + `tailwind-merge`). Accessible primitives (focus trap, menus, tabs, tooltips, toasts) come
from **Reka UI**; icons from **Lucide** (1.75 px stroke everywhere).

A dev-only `/styleguide` page (local env or admins) shows every token, type style and component.

## Motion

- Durations 150 / 200 / 280 ms. Enter: `cubic-bezier(.2,0,0,1)` (quick decelerate). Exit:
  `cubic-bezier(.4,0,1,1)`, always shorter than enter.
- Page changes use the View Transitions API (cross-fade + 6 px rise) where supported, with a CSS
  enter animation fallback. Lists use `<TransitionGroup>` (fade + slide, FLIP on move).
- Micro-interactions: 1 px press-down on buttons, animated tab indicator, menu/dialog scale-fade,
  optimistic votes / RSVP / club membership, toasts for flash messages.
- `prefers-reduced-motion: reduce` disables all transforms and animations globally.
