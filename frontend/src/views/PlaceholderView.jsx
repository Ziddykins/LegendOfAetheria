import React from 'react';
import { Box, Card, CardContent, Typography, Button } from '@mui/material';
import AutoAwesomeIcon from '@mui/icons-material/AutoAwesome';
import { useNavigate } from 'react-router-dom';

export const PlaceholderView = ({ title, description }) => {
  const navigate = useNavigate();

  return (
    <Box sx={{ maxWidth: '800px', mx: 'auto', mt: 6 }}>
      <Card sx={{ textAlign: 'center', p: 4 }}>
        <CardContent>
          <AutoAwesomeIcon sx={{ fontSize: 56, color: '#ffd700', mb: 2, opacity: 0.85 }} />
          <Typography variant="h4" sx={{ color: '#ffd700', mb: 1.5 }}>
            {title}
          </Typography>
          <Typography variant="body1" sx={{ color: '#94a3b8', mb: 4, maxWidth: '480px', mx: 'auto' }}>
            {description || 'The scribes and architects of Aetheria are currently weaving the spells for this realm section.'}
          </Typography>
          <Button variant="outlined" color="primary" onClick={() => navigate('/game/sheet')}>
            Return to Character Sheet
          </Button>
        </CardContent>
      </Card>
    </Box>
  );
};
