import React, { useState } from 'react';
import {
  Box,
  Drawer,
  AppBar,
  Toolbar,
  Typography,
  IconButton,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Divider,
  Tooltip,
  Badge,
  Menu,
  MenuItem,
} from '@mui/material';
import { Outlet, useNavigate, useLocation } from 'react-router-dom';
import MenuIcon from '@mui/icons-material/Menu';
import ChevronLeftIcon from '@mui/icons-material/ChevronLeft';
import PersonIcon from '@mui/icons-material/Person';
import AutoStoriesIcon from '@mui/icons-material/AutoStories';
import ShieldIcon from '@mui/icons-material/Shield';
import FitnessCenterIcon from '@mui/icons-material/FitnessCenter';
import BackpackIcon from '@mui/icons-material/Backpack';
import ExploreIcon from '@mui/icons-material/Explore';
import SportsMartialArtsIcon from '@mui/icons-material/SportsMartialArts';
import AccountBalanceIcon from '@mui/icons-material/AccountBalance';
import StoreIcon from '@mui/icons-material/Store';
import MailIcon from '@mui/icons-material/Mail';
import PeopleIcon from '@mui/icons-material/People';
import ChatIcon from '@mui/icons-material/Chat';
import MonetizationOnIcon from '@mui/icons-material/MonetizationOn';
import PlaceIcon from '@mui/icons-material/Place';
import LogoutIcon from '@mui/icons-material/Logout';
import SwitchAccountIcon from '@mui/icons-material/SwitchAccount';
import { useCharacterStore } from '../store/useCharacterStore';
import { useAuthStore } from '../store/useAuthStore';
import { useUIStore } from '../store/useUIStore';
import { VitalsBar } from '../components/VitalsBar';
import { OfflineBanner } from '../components/OfflineBanner';
import { NotificationToast } from '../components/NotificationToast';

const DRAWER_WIDTH = 250;

export const GameShell = () => {
  const { activeCharacter } = useCharacterStore();
  const { logout } = useAuthStore();
  const { sidebarOpen, toggleSidebar, setSidebarOpen, chatOpen, toggleChat } = useUIStore();
  const navigate = useNavigate();
  const location = useLocation();

  const [anchorEl, setAnchorEl] = useState(null);

  // If no active character, redirect to selection
  React.useEffect(() => {
    if (!activeCharacter) {
      navigate('/select');
    }
  }, [activeCharacter, navigate]);

  if (!activeCharacter) return null;

  const navItems = [
    { title: 'Character Sheet', path: '/game/sheet', icon: <PersonIcon /> },
    { title: 'Equipment & Gear', path: '/game/equipment', icon: <ShieldIcon /> },
    { title: 'Inventory', path: '/game/inventory', icon: <BackpackIcon /> },
    { title: 'Skills & Spells', path: '/game/skills', icon: <AutoStoriesIcon /> },
    { title: 'Training Grounds', path: '/game/train', icon: <FitnessCenterIcon /> },
    { divider: true },
    { title: 'Explore & Travel', path: '/game/explore', icon: <ExploreIcon /> },
    { title: 'Monster Hunt / Battle', path: '/game/battle', icon: <SportsMartialArtsIcon /> },
    { divider: true },
    { title: 'Citadel Bank', path: '/game/bank', icon: <AccountBalanceIcon /> },
    { title: 'Black Market', path: '/game/market', icon: <StoreIcon /> },
    { title: 'Courier Mailbox', path: '/game/mail', icon: <MailIcon />, badge: 2 },
    { title: 'Fellowship / Friends', path: '/game/friends', icon: <PeopleIcon /> },
  ];

  return (
    <Box sx={{ display: 'flex', flexDirection: 'column', minHeight: '100vh', backgroundColor: '#07090e' }}>
      <OfflineBanner />

      {/* Top Fantasy HUD */}
      <AppBar
        position="sticky"
        sx={{
          backgroundColor: 'rgba(10, 14, 22, 0.95)',
          backdropFilter: 'blur(16px)',
          borderBottom: '1px solid rgba(212, 175, 55, 0.25)',
          boxShadow: '0 4px 24px rgba(0, 0, 0, 0.7)',
          zIndex: (theme) => theme.zIndex.drawer + 1,
        }}
      >
        <Toolbar sx={{ justifyContent: 'space-between', minHeight: '68px', px: { xs: 1.5, sm: 3 } }}>
          {/* Left: Menu toggle & Hero Visage */}
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
            <IconButton onClick={toggleSidebar} sx={{ color: '#d4af37' }}>
              {sidebarOpen ? <ChevronLeftIcon /> : <MenuIcon />}
            </IconButton>

            <Box
              component="img"
              src={`/img/avatars/${activeCharacter.avatar}`}
              alt={activeCharacter.name}
              onError={(e) => {
                e.target.src = '/img/avatars/avatar-1.webp';
              }}
              onClick={(e) => setAnchorEl(e.currentTarget)}
              sx={{
                width: 44,
                height: 44,
                borderRadius: '50%',
                border: '2px solid #ffd700',
                boxShadow: '0 0 10px rgba(212, 175, 55, 0.4)',
                cursor: 'pointer',
                objectFit: 'cover',
                backgroundColor: '#0c111c',
                transition: 'transform 0.2s ease',
                '&:hover': {
                  transform: 'scale(1.06)',
                },
              }}
            />

            <Box sx={{ display: { xs: 'none', sm: 'block' } }}>
              <Typography variant="subtitle2" sx={{ color: '#f8fafc', fontWeight: 700, lineHeight: 1.2 }}>
                {activeCharacter.name}
              </Typography>
              <Typography variant="caption" sx={{ color: '#ffd700', letterSpacing: '0.04em' }}>
                LVL {activeCharacter.level} • {activeCharacter.race}
              </Typography>
            </Box>
          </Box>

          {/* Center: Vitals Progress Bars */}
          <Box
            sx={{
              display: 'flex',
              alignItems: 'center',
              gap: { xs: 1.5, md: 3 },
              width: { xs: '180px', sm: '260px', md: '360px' },
            }}
          >
            <Box sx={{ flex: 1 }}>
              <VitalsBar type="hp" current={activeCharacter.vitals.hp} max={activeCharacter.vitals.maxHp} height={8} />
            </Box>
            <Box sx={{ flex: 1 }}>
              <VitalsBar type="mp" current={activeCharacter.vitals.mp} max={activeCharacter.vitals.maxMp} height={8} />
            </Box>
          </Box>

          {/* Right: Gold, Coordinates, Actions */}
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
            <Box
              sx={{
                display: { xs: 'none', md: 'flex' },
                alignItems: 'center',
                gap: 1.5,
                backgroundColor: 'rgba(7, 10, 16, 0.8)',
                border: '1px solid rgba(212, 175, 55, 0.2)',
                borderRadius: 2,
                px: 1.5,
                py: 0.5,
              }}
            >
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                <PlaceIcon sx={{ fontSize: 16, color: '#38bdf8' }} />
                <Typography variant="caption" sx={{ color: '#cbd5e1', fontWeight: 600 }}>
                  Floor {activeCharacter.floor} ({activeCharacter.x}, {activeCharacter.y})
                </Typography>
              </Box>

              <Divider orientation="vertical" flexItem sx={{ borderColor: 'rgba(212, 175, 55, 0.2)' }} />

              <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                <MonetizationOnIcon sx={{ fontSize: 16, color: '#ffd700' }} />
                <Typography variant="caption" sx={{ color: '#ffd700', fontWeight: 700 }}>
                  {activeCharacter.gold?.toLocaleString()}
                </Typography>
              </Box>
            </Box>

            <Tooltip title="Toggle Global Chat">
              <IconButton onClick={toggleChat} sx={{ color: chatOpen ? '#ffd700' : '#94a3b8' }}>
                <Badge color="primary" variant="dot">
                  <ChatIcon />
                </Badge>
              </IconButton>
            </Tooltip>

            <Tooltip title="Switch Hero">
              <IconButton onClick={() => navigate('/select')} sx={{ color: '#94a3b8' }}>
                <SwitchAccountIcon />
              </IconButton>
            </Tooltip>
          </Box>
        </Toolbar>
      </AppBar>

      {/* Hero Menu */}
      <Menu
        anchorEl={anchorEl}
        open={Boolean(anchorEl)}
        onClose={() => setAnchorEl(null)}
        PaperProps={{
          sx: {
            backgroundColor: 'rgba(13, 18, 30, 0.95)',
            border: '1px solid rgba(212, 175, 55, 0.3)',
            backdropFilter: 'blur(16px)',
          },
        }}
      >
        <MenuItem
          onClick={() => {
            navigate('/select');
            setAnchorEl(null);
          }}
        >
          <ListItemIcon>
            <SwitchAccountIcon fontSize="small" sx={{ color: '#ffd700' }} />
          </ListItemIcon>
          <ListItemText>Switch Champion</ListItemText>
        </MenuItem>
        <Divider sx={{ borderColor: 'rgba(212, 175, 55, 0.2)' }} />
        <MenuItem
          onClick={() => {
            logout();
            navigate('/login');
            setAnchorEl(null);
          }}
        >
          <ListItemIcon>
            <LogoutIcon fontSize="small" sx={{ color: '#ef4444' }} />
          </ListItemIcon>
          <ListItemText sx={{ color: '#ef4444' }}>Sign Out</ListItemText>
        </MenuItem>
      </Menu>

      {/* Main Container with Sidebar + Canvas */}
      <Box sx={{ display: 'flex', flex: 1, position: 'relative' }}>
        {/* Fantasy Sidebar Drawer */}
        <Drawer
          variant="persistent"
          open={sidebarOpen}
          sx={{
            width: sidebarOpen ? DRAWER_WIDTH : 0,
            flexShrink: 0,
            '& .MuiDrawer-paper': {
              width: DRAWER_WIDTH,
              boxSizing: 'border-box',
              backgroundColor: 'rgba(10, 14, 22, 0.96)',
              borderRight: '1px solid rgba(212, 175, 55, 0.18)',
              top: '68px',
              height: 'calc(100vh - 68px)',
              overflowY: 'auto',
            },
          }}
        >
          <List sx={{ p: 1.5 }}>
            {navItems.map((item, idx) => {
              if (item.divider) {
                return (
                  <Divider
                    key={`div-${idx}`}
                    sx={{ my: 1.5, borderColor: 'rgba(212, 175, 55, 0.12)' }}
                  />
                );
              }

              const isActive = location.pathname === item.path;

              return (
                <ListItem key={item.path} disablePadding sx={{ mb: 0.5 }}>
                  <ListItemButton
                    onClick={() => navigate(item.path)}
                    sx={{
                      borderRadius: 1.5,
                      py: 1,
                      px: 1.5,
                      backgroundColor: isActive ? 'rgba(212, 175, 55, 0.12)' : 'transparent',
                      border: isActive
                        ? '1px solid rgba(212, 175, 55, 0.4)'
                        : '1px solid transparent',
                      boxShadow: isActive ? 'inset 0 0 12px rgba(212, 175, 55, 0.15)' : 'none',
                      '&:hover': {
                        backgroundColor: 'rgba(212, 175, 55, 0.08)',
                        borderColor: 'rgba(212, 175, 55, 0.25)',
                      },
                    }}
                  >
                    <ListItemIcon
                      sx={{
                        color: isActive ? '#ffd700' : '#94a3b8',
                        minWidth: '38px',
                      }}
                    >
                      {item.badge ? (
                        <Badge badgeContent={item.badge} color="primary">
                          {item.icon}
                        </Badge>
                      ) : (
                        item.icon
                      )}
                    </ListItemIcon>
                    <ListItemText
                      primary={item.title}
                      primaryTypographyProps={{
                        fontFamily: "'Cinzel', serif",
                        fontWeight: isActive ? 700 : 500,
                        fontSize: '0.85rem',
                        color: isActive ? '#ffd700' : '#cbd5e1',
                        letterSpacing: '0.02em',
                      }}
                    />
                  </ListItemButton>
                </ListItem>
              );
            })}
          </List>
        </Drawer>

        {/* Center Stage Canvas */}
        <Box
          component="main"
          sx={{
            flexGrow: 1,
            p: { xs: 2, sm: 3, md: 4 },
            minHeight: 'calc(100vh - 68px)',
            backgroundColor: '#07090e',
            backgroundImage:
              'radial-gradient(ellipse at 50% 0%, rgba(212, 175, 55, 0.05) 0%, transparent 70%)',
            transition: 'margin 0.3s ease',
          }}
        >
          <Outlet />
        </Box>

        {/* Right Chat Drawer (Collapsible) */}
        <Drawer
          anchor="right"
          open={chatOpen}
          onClose={toggleChat}
          PaperProps={{
            sx: {
              width: { xs: '100%', sm: 360 },
              backgroundColor: 'rgba(10, 14, 22, 0.98)',
              borderLeft: '1px solid rgba(212, 175, 55, 0.25)',
              p: 2.5,
              display: 'flex',
              flexDirection: 'column',
              justifyContent: 'space-between',
            },
          }}
        >
          <Box>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Typography variant="h6" sx={{ color: '#ffd700' }}>
                Aetheria Tavern Chat
              </Typography>
              <IconButton onClick={toggleChat} sx={{ color: '#94a3b8' }}>
                <ChevronLeftIcon sx={{ transform: 'rotate(180deg)' }} />
              </IconButton>
            </Box>
            <Divider sx={{ mb: 2, borderColor: 'rgba(212, 175, 55, 0.15)' }} />

            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1.5, maxHeight: '65vh', overflowY: 'auto' }}>
              <Box sx={{ p: 1.2, backgroundColor: 'rgba(255, 255, 255, 0.03)', borderRadius: 1 }}>
                <Typography variant="caption" sx={{ color: '#ffd700', fontWeight: 700 }}>
                  [System]:
                </Typography>{' '}
                <Typography variant="caption" sx={{ color: '#cbd5e1' }}>
                  A cold wind sweeps through Valensgard Citadel. The gates stand open.
                </Typography>
              </Box>
              <Box sx={{ p: 1.2, backgroundColor: 'rgba(255, 255, 255, 0.03)', borderRadius: 1 }}>
                <Typography variant="caption" sx={{ color: '#38bdf8', fontWeight: 700 }}>
                  Mage_Eldrin:
                </Typography>{' '}
                <Typography variant="caption" sx={{ color: '#cbd5e1' }}>
                  Anyone venturing into Deepstone Caverns Floor 3 today? Need a sturdy frontline!
                </Typography>
              </Box>
            </Box>
          </Box>

          <Box sx={{ pt: 2, borderTop: '1px solid rgba(212, 175, 55, 0.15)' }}>
            <Typography variant="caption" sx={{ color: '#64748b', display: 'block', mb: 1 }}>
              Connecting to realm chat broadcast...
            </Typography>
          </Box>
        </Drawer>
      </Box>

      <NotificationToast />
    </Box>
  );
};
