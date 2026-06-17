# Tech Spec — LegendOfAetheria Battle System

## Component Inventory

### shadcn/ui Components
- **Button** — Action buttons with custom dark fantasy styling overrides
- **DropdownMenu** — Attack type selection dropdown
- **Progress** — Stat bars (HP/MP/EP) with custom gradient fills
- **ScrollArea** — Battle log scrollable panel
- **Tooltip** — Action button hover descriptions

### Lucide Icons
- **Swords** — Attack action
- **Flame** — Burn spell
- **Snowflake** — Frost spell
- **HeartPlus** — Heal spell
- **Skull** — Monster avatar
- **Shield** — Player avatar / DEF stat
- **Zap** — Energy (EP) stat
- **Droplets** — Mana (MP) stat
- **Activity** — HP stat
- **Sword** — STR stat
- **AlertTriangle** — Low HP warning
- **Trophy** — Victory
- **RotateCcw** — Retry / reset

## Animation Implementation Plan

| Animation | Library | Implementation Approach | Complexity |
|-----------|---------|------------------------|------------|
| Entity card entrance (staggered fade-in) | GSAP | timeline with stagger, opacity 0→1 + translateY, 500ms, power2.out | Low |
| Turn indicator text transition | GSAP | scale 1.3→1.0 + opacity 0→1, 400ms, power2.out | Low |
| Damage number float-up | GSAP | translateY(0)→translateY(-40px) + opacity 1→0, 800ms, power2.out | Medium |
| Attack slash line | CSS | @keyframes diagonal line draw, 200ms, pseudo-element width 0→100% | Low |
| Burn spell flash | GSAP | overlay opacity 0→0.3→0, 400ms | Low |
| Frost spell effect | CSS + GSAP | cyan overlay flash + pseudo-element frost cracks, scale 0.8→1 | Medium |
| Heal spell effect | GSAP | green overlay opacity 0→0.25→0 + particle burst | Medium |
| Screen shake | GSAP | translateX oscillation ±3px, 50ms intervals, 300ms total | Low |
| Stat bar width transition | CSS | transition: width 600ms cubic-bezier(0.4, 0, 0.2, 1) | Low |
| Stat number count-up/down | Custom hook | requestAnimationFrame tween over 400ms | Medium |
| Log entry slide-in | GSAP | translateY(-10px)→0 + opacity 0→1, 300ms, power2.out | Low |
| Victory/Defeat overlay | GSAP | text scale 0.5→1.0, 600ms, back.out(1.7); overlay opacity 0→1, 400ms | Medium |
| Action button ripple | CSS | @keyframes radial gradient expand from click point, 300ms | Low |
| Dropdown menu scale | GSAP | scaleY 0→1 from top origin, 200ms, power2.out | Low |
| Card damage/heal flash | CSS | @keyframes background-color flash, 200ms | Low |
| Active turn border pulse | CSS | @keyframes box-shadow opacity oscillation, 1.5s infinite | Low |
| Ember particle system | Canvas 2D | requestAnimationFrame loop, 15-20 particles, sinusoidal drift | Medium |
| Button loading spinner | CSS | @keyframes rotate, 1s linear infinite | Low |

**Total: 18 animations — Low: 12, Medium: 6** — Total animation complexity: **Low-Medium**

## State & Logic Plan

### Combat State Machine
Implement as a React useReducer with strict state transitions:

```
States: 'idle' | 'player_turn' | 'processing' | 'enemy_turn' | 'animating' | 'resolved'

Valid transitions:
  idle → player_turn (mount)
  player_turn → processing (action selected)
  processing → animating (server response received)
  animating → player_turn (animations complete, battle continues)
  animating → resolved (animations complete, HP ≤ 0)
  resolved → idle (reset/new battle)
```

Guard conditions prevent invalid transitions (e.g., cannot go from processing to player_turn directly).

### State Management
- **useReducer** for combat state machine (current state, turn, selected action, last result)
- **useState** for local UI state (dropdown open, hovered action, battle log entries)
- **useRef** for animation controls (GSAP timeline refs, animation frame IDs)
- **useCallback** for action handlers and backend communication

### Backend Communication
- `executeAction(action: string, type: string): Promise<CombatResult>` — POST to /battle
- `fetchHUD(): Promise<HUDData>` — GET from /hud
- Both return parsed data; the component renders the combat narrative from the response

### Animation Orchestration
Each combat action triggers a GSAP timeline:
1. Hit stop (100ms freeze)
2. Spell/attack visual effect
3. Screen shake (if applicable)
4. Damage number popup
5. Stat bar update
6. Log entry slide-in
7. Turn transition

Use GSAP timeline with labels for precise sequencing. Kill active timelines on unmount.

## Other Key Decisions

### No Router
Single embeddable component — no routing needed. Mounts into a single DOM element.

### Embedding Strategy
Expose a global mount function for iframe or direct DOM embedding:
```typescript
window.AetheriaBattle = {
  mount: (container: HTMLElement, config: BattleConfig) => void,
  unmount: () => void,
};
```

### CSS Scoping
Use CSS Modules via Vite's built-in `.module.css` support. All styles scoped to component, no global CSS pollution. Class names prefixed automatically.

### Canvas Particle System
Canvas 2D for ember particles (background) and spell particle bursts. Single canvas element positioned absolutely behind content. requestAnimationFrame loop, cleaned up on unmount.

### Battle Log HTML Parsing
The PHP backend returns HTML fragments. Parse using DOMParser to extract text content and styling classes, then reconstruct as React elements with the component's design tokens.
