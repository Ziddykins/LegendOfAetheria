import React from 'react';
import { Box, Typography } from '@mui/material';
import FavoriteIcon from '@mui/icons-material/Favorite';
import AutoAwesomeIcon from '@mui/icons-material/AutoAwesome';
import FlashOnIcon from '@mui/icons-material/FlashOn';

export const VitalsBar = ({ type, current, max, showLabel = true, height = 10 }) => {
  const percent = Math.min(100, Math.max(0, Math.round((current / (max || 1)) * 100)));

  const configs = {
    hp: {
      label: 'HP',
      icon: <FavoriteIcon sx={{ fontSize: 14, color: '#ef4444' }} />,
      color: 'linear-gradient(90deg, #b91c1c 0%, #ef4444 100%)',
      glow: 'rgba(239, 68, 68, 0.45)',
      textColor: '#fca5a5',
      lowWarning: percent <= 25,
    },
    mp: {
      label: 'MP',
      icon: <AutoAwesomeIcon sx={{ fontSize: 14, color: '#38bdf8' }} />,
      color: 'linear-gradient(90deg, #0284c7 0%, #38bdf8 100%)',
      glow: 'rgba(56, 189, 248, 0.45)',
      textColor: '#bae6fd',
      lowWarning: false,
    },
    ep: {
      label: 'EP',
      icon: <FlashOnIcon sx={{ fontSize: 14, color: '#f59e0b' }} />,
      color: 'linear-gradient(90deg, #d97706 0%, #fbbf24 100%)',
      glow: 'rgba(245, 158, 11, 0.45)',
      textColor: '#fde68a',
      lowWarning: false,
    },
  };

  const config = configs[type] || configs.hp;

  return (
    <Box sx={{ width: '100%' }}>
      {showLabel && (
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 0.5 }}>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
            {config.icon}
            <Typography variant="caption" sx={{ fontWeight: 700, color: config.textColor, letterSpacing: '0.04em' }}>
              {config.label}
            </Typography>
          </Box>
          <Typography variant="caption" sx={{ color: '#cbd5e1', fontWeight: 600, fontVariantNumeric: 'tabular-nums' }}>
            {current} <span style={{ color: '#64748b' }}>/ {max}</span>
          </Typography>
        </Box>
      )}

      <Box
        sx={{
          height,
          backgroundColor: 'rgba(7, 10, 16, 0.85)',
          borderRadius: `${height / 2}px`,
          border: '1px solid rgba(255, 255, 255, 0.08)',
          position: 'relative',
          overflow: 'hidden',
          boxShadow: 'inset 0 1px 3px rgba(0, 0, 0, 0.8)',
          animation: config.lowWarning ? 'pulseWarning 1.5s infinite ease-in-out' : 'none',
          '@keyframes pulseWarning': {
            '0%, 100%': { borderColor: 'rgba(239, 68, 68, 0.4)' },
            '50%': { borderColor: 'rgba(239, 68, 68, 0.9)', boxShadow: '0 0 10px rgba(239, 68, 68, 0.6)' },
          },
        }}
      >
        <Box
          sx={{
            width: `${percent}%`,
            height: '100%',
            background: config.color,
            borderRadius: `${height / 2}px`,
            transition: 'width 0.4s cubic-bezier(0.4, 0, 0.2, 1)',
            boxShadow: `0 0 10px ${config.glow}`,
          }}
        />
      </Box>
    </Box>
  );
};
