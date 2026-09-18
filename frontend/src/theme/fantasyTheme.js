import { createTheme } from '@mui/material/styles';

export const fantasyTheme = createTheme({
  palette: {
    mode: 'dark',
    background: {
      default: '#07090e',
      paper: 'rgba(15, 21, 34, 0.82)',
      surface: 'rgba(21, 30, 48, 0.75)',
      darkInset: 'rgba(5, 7, 12, 0.85)',
    },
    primary: {
      main: '#d4af37', // Antique Gold
      light: '#ffd700',
      dark: '#997a15',
      contrastText: '#07090e',
    },
    secondary: {
      main: '#38bdf8', // Arcane Mana Cyan
      light: '#7dd3fc',
      dark: '#0284c7',
      contrastText: '#07090e',
    },
    error: {
      main: '#ef4444', // Vitality / Blood Crimson
      light: '#f87171',
      dark: '#b91c1c',
    },
    warning: {
      main: '#f59e0b', // Energy Amber
      light: '#fbbf24',
      dark: '#d97706',
    },
    success: {
      main: '#10b981', // Nature / Regeneration Emerald
      light: '#34d399',
      dark: '#059669',
    },
    text: {
      primary: '#f1f5f9',
      secondary: '#94a3b8',
      disabled: '#475569',
      gold: '#ffd700',
    },
    divider: 'rgba(212, 175, 55, 0.18)',
    rarities: {
      common: '#94a3b8',
      rare: '#38bdf8',
      epic: '#c084fc',
      legendary: '#fbbf24',
      cursed: '#ef4444',
    },
  },
  typography: {
    fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
    h1: {
      fontFamily: "'Cinzel', 'Uncial Antiqua', serif",
      fontWeight: 700,
      letterSpacing: '0.06em',
      color: '#ffd700',
      textShadow: '0 0 20px rgba(212, 175, 55, 0.35)',
    },
    h2: {
      fontFamily: "'Cinzel', 'Uncial Antiqua', serif",
      fontWeight: 700,
      letterSpacing: '0.05em',
      color: '#f8fafc',
    },
    h3: {
      fontFamily: "'Cinzel', 'Uncial Antiqua', serif",
      fontWeight: 600,
      letterSpacing: '0.04em',
      color: '#ffd700',
    },
    h4: {
      fontFamily: "'Cinzel', 'Uncial Antiqua', serif",
      fontWeight: 600,
      letterSpacing: '0.03em',
      color: '#f1f5f9',
    },
    h5: {
      fontFamily: "'Cinzel', 'Uncial Antiqua', serif",
      fontWeight: 600,
      letterSpacing: '0.02em',
      color: '#f1f5f9',
    },
    h6: {
      fontFamily: "'Cinzel', 'Uncial Antiqua', serif",
      fontWeight: 600,
      letterSpacing: '0.02em',
      color: '#d4af37',
    },
    subtitle1: {
      fontFamily: "'Inter', sans-serif",
      letterSpacing: '0.01em',
    },
    button: {
      fontFamily: "'Cinzel', serif",
      letterSpacing: '0.06em',
      fontWeight: 600,
    },
  },
  shape: {
    borderRadius: 10,
  },
  components: {
    MuiCssBaseline: {
      styleOverrides: {
        body: {
          backgroundColor: '#07090e',
          backgroundImage: 'radial-gradient(ellipse at 50% -20%, rgba(212, 175, 55, 0.08) 0%, transparent 70%)',
          minHeight: '100vh',
          scrollbarColor: '#d4af37 #0c111c',
          '&::-webkit-scrollbar': {
            width: '8px',
            height: '8px',
          },
          '&::-webkit-scrollbar-track': {
            backgroundColor: '#07090e',
          },
          '&::-webkit-scrollbar-thumb': {
            backgroundColor: 'rgba(212, 175, 55, 0.35)',
            borderRadius: '4px',
            border: '1px solid rgba(212, 175, 55, 0.1)',
            '&:hover': {
              backgroundColor: 'rgba(212, 175, 55, 0.65)',
            },
          },
        },
      },
    },
    MuiCard: {
      styleOverrides: {
        root: {
          backgroundColor: 'rgba(15, 21, 34, 0.82)',
          backgroundImage: 'linear-gradient(145deg, rgba(23, 32, 51, 0.6) 0%, rgba(11, 15, 26, 0.85) 100%)',
          backdropFilter: 'blur(16px)',
          border: '1px solid rgba(212, 175, 55, 0.22)',
          boxShadow: '0 8px 32px 0 rgba(0, 0, 0, 0.55), inset 0 1px 0 0 rgba(255, 255, 255, 0.06)',
          transition: 'all 0.25s cubic-bezier(0.16, 1, 0.3, 1)',
          '&:hover': {
            borderColor: 'rgba(212, 175, 55, 0.45)',
            boxShadow: '0 12px 40px 0 rgba(0, 0, 0, 0.65), 0 0 20px rgba(212, 175, 55, 0.12)',
          },
        },
      },
    },
    MuiPaper: {
      styleOverrides: {
        root: {
          backgroundImage: 'none',
        },
      },
    },
    MuiButton: {
      styleOverrides: {
        root: {
          textTransform: 'none',
          padding: '8px 20px',
          transition: 'all 0.2s ease-in-out',
        },
        containedPrimary: {
          background: 'linear-gradient(135deg, #d4af37 0%, #b8860b 100%)',
          color: '#07090e',
          fontWeight: 700,
          boxShadow: '0 4px 16px rgba(212, 175, 55, 0.35)',
          '&:hover': {
            background: 'linear-gradient(135deg, #ffd700 0%, #d4af37 100%)',
            boxShadow: '0 6px 24px rgba(212, 175, 55, 0.55)',
            transform: 'translateY(-1px)',
          },
        },
        outlinedPrimary: {
          borderColor: 'rgba(212, 175, 55, 0.4)',
          color: '#ffd700',
          backgroundColor: 'rgba(212, 175, 55, 0.05)',
          '&:hover': {
            borderColor: '#ffd700',
            backgroundColor: 'rgba(212, 175, 55, 0.15)',
            boxShadow: '0 0 16px rgba(212, 175, 55, 0.25)',
          },
        },
      },
    },
    MuiTextField: {
      styleOverrides: {
        root: {
          '& .MuiOutlinedInput-root': {
            backgroundColor: 'rgba(7, 10, 16, 0.75)',
            borderRadius: '8px',
            '& fieldset': {
              borderColor: 'rgba(212, 175, 55, 0.25)',
              transition: 'border-color 0.2s ease',
            },
            '&:hover fieldset': {
              borderColor: 'rgba(212, 175, 55, 0.55)',
            },
            '&.Mui-focused fieldset': {
              borderColor: '#d4af37',
              borderWidth: '1.5px',
              boxShadow: '0 0 10px rgba(212, 175, 55, 0.35)',
            },
          },
        },
      },
    },
    MuiLinearProgress: {
      styleOverrides: {
        root: {
          height: 10,
          borderRadius: 5,
          backgroundColor: 'rgba(10, 14, 22, 0.85)',
          border: '1px solid rgba(255, 255, 255, 0.08)',
        },
      },
    },
    MuiTooltip: {
      styleOverrides: {
        tooltip: {
          backgroundColor: 'rgba(10, 14, 22, 0.95)',
          border: '1px solid rgba(212, 175, 55, 0.35)',
          boxShadow: '0 4px 20px rgba(0, 0, 0, 0.8)',
          color: '#f8fafc',
          backdropFilter: 'blur(8px)',
          fontSize: '0.82rem',
          padding: '8px 12px',
        },
      },
    },
  },
});
