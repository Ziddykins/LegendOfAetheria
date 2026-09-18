import React from 'react';
import { Snackbar, Alert } from '@mui/material';
import { useUIStore } from '../store/useUIStore';

export const NotificationToast = () => {
  const { notification, closeNotification } = useUIStore();

  return (
    <Snackbar
      open={notification.open}
      autoHideDuration={4000}
      onClose={closeNotification}
      anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
    >
      <Alert
        onClose={closeNotification}
        severity={notification.severity}
        variant="filled"
        sx={{
          backgroundColor:
            notification.severity === 'success'
              ? 'rgba(16, 185, 129, 0.95)'
              : notification.severity === 'error'
              ? 'rgba(239, 68, 68, 0.95)'
              : notification.severity === 'warning'
              ? 'rgba(245, 158, 11, 0.95)'
              : 'rgba(56, 189, 248, 0.95)',
          color: '#080b11',
          fontWeight: 600,
          boxShadow: '0 8px 30px rgba(0, 0, 0, 0.6)',
          backdropFilter: 'blur(8px)',
        }}
      >
        {notification.message}
      </Alert>
    </Snackbar>
  );
};
