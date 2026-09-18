import React from 'react';
import { Box, Typography } from '@mui/material';
import CloudOffIcon from '@mui/icons-material/CloudOff';
import { useUIStore } from '../store/useUIStore';

export const OfflineBanner = () => {
  const { apiOffline } = useUIStore();

  if (!apiOffline) return null;

  return (
    <Box
      sx={{
        backgroundColor: 'rgba(239, 68, 68, 0.15)',
        borderBottom: '1px solid rgba(239, 68, 68, 0.4)',
        color: '#fca5a5',
        px: 2,
        py: 0.75,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        gap: 1.5,
        fontSize: '0.85rem',
        zIndex: 9999,
      }}
    >
      <CloudOffIcon sx={{ fontSize: 18, color: '#ef4444' }} />
      <Typography variant="caption" sx={{ fontWeight: 500, letterSpacing: '0.02em' }}>
        <strong>Backend Unreachable:</strong> Express API at <code>http://localhost:3000</code> is offline. Connect database & run <code>node api/server.js</code> for full live sync.
      </Typography>
    </Box>
  );
};
