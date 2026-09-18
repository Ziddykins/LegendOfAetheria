import React, { useState } from 'react';
import {
  Box,
  Card,
  CardContent,
  Typography,
  TextField,
  Button,
  Tabs,
  Tab,
  MenuItem,
  CircularProgress,
  InputAdornment,
  IconButton,
} from '@mui/material';
import { motion, AnimatePresence } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import Visibility from '@mui/icons-material/Visibility';
import VisibilityOff from '@mui/icons-material/VisibilityOff';
import ShieldIcon from '@mui/icons-material/Shield';
import PersonAddIcon from '@mui/icons-material/PersonAdd';
import LoginIcon from '@mui/icons-material/Login';
import { useAuthStore } from '../store/useAuthStore';
import { useUIStore } from '../store/useUIStore';

const races = ['Human', 'Elf', 'Dwarf', 'Orc', 'Gnome'];

export const AuthView = () => {
  const [tab, setTab] = useState(0); // 0 = Login, 1 = Register
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [characterName, setCharacterName] = useState('');
  const [race, setRace] = useState('Human');
  const [showPassword, setShowPassword] = useState(false);

  const { login, register, isLoading, error } = useAuthStore();
  const { notify } = useUIStore();
  const navigate = useNavigate();

  const handleLoginSubmit = async (e) => {
    e.preventDefault();
    if (!email || !password) {
      notify('Please enter both email and password.', 'warning');
      return;
    }

    const res = await login(email, password);
    if (res.success) {
      navigate('/select');
    }
  };

  const handleRegisterSubmit = async (e) => {
    e.preventDefault();
    if (!email || !password || !characterName) {
      notify('Please complete all required fields.', 'warning');
      return;
    }
    if (password !== confirmPassword) {
      notify('Passwords do not match.', 'error');
      return;
    }

    const res = await register({ email, password, characterName, race });
    if (res.success) {
      setTab(0);
    }
  };

  return (
    <Box
      sx={{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        p: 2,
        position: 'relative',
        backgroundImage:
          'radial-gradient(circle at 50% 25%, rgba(212, 175, 55, 0.12) 0%, rgba(7, 9, 14, 0.95) 75%)',
      }}
    >
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, ease: 'easeOut' }}
        style={{ width: '100%', maxWidth: '480px' }}
      >
        <Card
          sx={{
            p: 1,
            position: 'relative',
            overflow: 'hidden',
            '&::before': {
              content: '""',
              position: 'absolute',
              top: 0,
              left: 0,
              right: 0,
              height: '3px',
              background: 'linear-gradient(90deg, transparent, #d4af37, #ffd700, #d4af37, transparent)',
            },
          }}
        >
          <CardContent sx={{ p: { xs: 2.5, sm: 3.5 } }}>
            {/* Game Banner Logo */}
            <Box sx={{ textAlign: 'center', mb: 3 }}>
              <Box
                component="img"
                src="/img/logos/logo-banner-no-bg.webp"
                alt="Legend of Aetheria"
                sx={{
                  maxWidth: '100%',
                  height: 'auto',
                  maxHeight: '110px',
                  filter: 'drop-shadow(0 4px 12px rgba(0, 0, 0, 0.8))',
                  transition: 'transform 0.3s ease',
                  '&:hover': {
                    transform: 'scale(1.02)',
                  },
                }}
              />
              <Typography
                variant="h6"
                sx={{
                  color: '#ffd700',
                  fontSize: '0.95rem',
                  letterSpacing: '0.15em',
                  textTransform: 'uppercase',
                  mt: 1,
                }}
              >
                Chronicles of the Shattered Realm
              </Typography>
            </Box>

            {/* Auth Mode Tabs */}
            <Tabs
              value={tab}
              onChange={(e, val) => setTab(val)}
              centered
              sx={{
                mb: 3,
                borderBottom: '1px solid rgba(212, 175, 55, 0.2)',
                '& .MuiTabs-indicator': {
                  backgroundColor: '#ffd700',
                  height: '2px',
                  boxShadow: '0 0 10px #ffd700',
                },
                '& .MuiTab-root': {
                  fontFamily: "'Cinzel', serif",
                  fontWeight: 600,
                  fontSize: '0.9rem',
                  letterSpacing: '0.05em',
                  color: '#94a3b8',
                  '&.Mui-selected': {
                    color: '#ffd700',
                  },
                },
              }}
            >
              <Tab icon={<LoginIcon sx={{ fontSize: 18 }} />} iconPosition="start" label="Enter Realm" />
              <Tab icon={<PersonAddIcon sx={{ fontSize: 18 }} />} iconPosition="start" label="Forge Hero" />
            </Tabs>

            {/* Error Display */}
            {error && (
              <Box
                sx={{
                  mb: 2.5,
                  p: 1.5,
                  borderRadius: 1,
                  backgroundColor: 'rgba(239, 68, 68, 0.15)',
                  border: '1px solid rgba(239, 68, 68, 0.4)',
                  color: '#fca5a5',
                  fontSize: '0.85rem',
                }}
              >
                {error}
              </Box>
            )}

            <AnimatePresence mode="wait">
              {tab === 0 ? (
                /* Login Form */
                <motion.form
                  key="login-form"
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  exit={{ opacity: 0, x: 10 }}
                  transition={{ duration: 0.25 }}
                  onSubmit={handleLoginSubmit}
                >
                  <TextField
                    fullWidth
                    label="Account Email"
                    variant="outlined"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    sx={{ mb: 2 }}
                    placeholder="hero@aetheria.realm"
                    required
                  />

                  <TextField
                    fullWidth
                    label="Secret Passphrase"
                    variant="outlined"
                    type={showPassword ? 'text' : 'password'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    sx={{ mb: 3 }}
                    required
                    InputProps={{
                      endAdornment: (
                        <InputAdornment position="end">
                          <IconButton
                            onClick={() => setShowPassword(!showPassword)}
                            edge="end"
                            sx={{ color: '#94a3b8' }}
                          >
                            {showPassword ? <VisibilityOff /> : <Visibility />}
                          </IconButton>
                        </InputAdornment>
                      ),
                    }}
                  />

                  <Button
                    fullWidth
                    variant="contained"
                    color="primary"
                    size="large"
                    type="submit"
                    disabled={isLoading}
                    sx={{ py: 1.4, fontSize: '1.05rem' }}
                  >
                    {isLoading ? <CircularProgress size={24} sx={{ color: '#07090e' }} /> : 'Embark Journey'}
                  </Button>

                  <Button
                    fullWidth
                    variant="outlined"
                    color="primary"
                    size="medium"
                    onClick={() => {
                      demoLogin();
                      navigate('/select');
                    }}
                    sx={{ mt: 1.5, py: 1, borderColor: 'rgba(212, 175, 55, 0.4)' }}
                  >
                    Quick Test Access (1-Click)
                  </Button>

                  <Box sx={{ textAlign: 'center', mt: 2.5 }}>
                    <Typography
                      variant="caption"
                      sx={{
                        color: '#64748b',
                        cursor: 'pointer',
                        '&:hover': { color: '#ffd700' },
                      }}
                      onClick={() => setTab(1)}
                    >
                      New to Aetheria? Create your lineage here
                    </Typography>
                  </Box>
                </motion.form>
              ) : (
                /* Registration Form */
                <motion.form
                  key="register-form"
                  initial={{ opacity: 0, x: 10 }}
                  animate={{ opacity: 1, x: 0 }}
                  exit={{ opacity: 0, x: -10 }}
                  transition={{ duration: 0.25 }}
                  onSubmit={handleRegisterSubmit}
                >
                  <TextField
                    fullWidth
                    label="Account Email"
                    variant="outlined"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    sx={{ mb: 2 }}
                    required
                  />

                  <TextField
                    fullWidth
                    label="First Hero's Name"
                    variant="outlined"
                    value={characterName}
                    onChange={(e) => setCharacterName(e.target.value)}
                    sx={{ mb: 2 }}
                    placeholder="e.g. Valerie Dawnstrider"
                    required
                  />

                  <TextField
                    fullWidth
                    select
                    label="Ancestral Lineage (Race)"
                    value={race}
                    onChange={(e) => setRace(e.target.value)}
                    sx={{ mb: 2 }}
                  >
                    {races.map((r) => (
                      <MenuItem key={r} value={r}>
                        {r}
                      </MenuItem>
                    ))}
                  </TextField>

                  <TextField
                    fullWidth
                    label="Secret Passphrase"
                    variant="outlined"
                    type={showPassword ? 'text' : 'password'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    sx={{ mb: 2 }}
                    required
                  />

                  <TextField
                    fullWidth
                    label="Confirm Passphrase"
                    variant="outlined"
                    type={showPassword ? 'text' : 'password'}
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    sx={{ mb: 3 }}
                    required
                  />

                  <Button
                    fullWidth
                    variant="contained"
                    color="primary"
                    size="large"
                    type="submit"
                    disabled={isLoading}
                    sx={{ py: 1.4, fontSize: '1.05rem' }}
                  >
                    {isLoading ? <CircularProgress size={24} sx={{ color: '#07090e' }} /> : 'Inscribe Destiny'}
                  </Button>
                </motion.form>
              )}
            </AnimatePresence>
          </CardContent>
        </Card>
      </motion.div>
    </Box>
  );
};
