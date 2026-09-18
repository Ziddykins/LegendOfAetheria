import React, { useState } from 'react';
import {
  Box,
  Container,
  Grid,
  Card,
  CardContent,
  Typography,
  Button,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  TextField,
  MenuItem,
  IconButton,
  Tooltip,
} from '@mui/material';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import AddCircleIcon from '@mui/icons-material/AddCircle';
import PlayArrowIcon from '@mui/icons-material/PlayArrow';
import LogoutIcon from '@mui/icons-material/Logout';
import AddIcon from '@mui/icons-material/Add';
import RemoveIcon from '@mui/icons-material/Remove';
import MonetizationOnIcon from '@mui/icons-material/MonetizationOn';
import PlaceIcon from '@mui/icons-material/Place';
import ArrowBackIosNewIcon from '@mui/icons-material/ArrowBackIosNew';
import ArrowForwardIosIcon from '@mui/icons-material/ArrowForwardIos';
import { useCharacterStore } from '../store/useCharacterStore';
import { useAuthStore } from '../store/useAuthStore';
import { VitalsBar } from '../components/VitalsBar';

const raceModifiers = {
  Human: { str: 10, def: 10, int: 10, bonus: 'Balanced adaptability across all attributes' },
  Elf: { str: 8, def: 8, int: 14, bonus: '+4 INT, heightened mana affinity and agility' },
  Dwarf: { str: 12, def: 14, int: 6, bonus: '+4 DEF, +2 STR, subterranean resilience' },
  Orc: { str: 15, def: 11, int: 6, bonus: '+5 STR, +1 DEF, ferocious combat potency' },
  Gnome: { str: 6, def: 9, int: 15, bonus: '+5 INT, inventive mind and arcane mastery' },
};

const avatars = [
  'avatar-1.webp',
  'avatar-2.webp',
  'avatar-3.webp',
  'avatar-4.webp',
  'avatar-5.webp',
  'avatar-6.webp',
  'avatar-7.webp',
  'avatar-8.webp',
];

export const CharacterSelectView = () => {
  const { characterSlots, selectCharacter, createCharacter } = useCharacterStore();
  const { logout, account } = useAuthStore();
  const navigate = useNavigate();

  // Create Modal State
  const [createModalOpen, setCreateModalOpen] = useState(false);
  const [targetSlot, setTargetSlot] = useState(1);
  const [newCharName, setNewCharName] = useState('');
  const [selectedRace, setSelectedRace] = useState('Human');
  const [avatarIdx, setAvatarIdx] = useState(0);

  // AP allocation for new character (Total starting points: 30)
  const [str, setStr] = useState(10);
  const [def, setDef] = useState(10);
  const [intVal, setIntVal] = useState(10);

  const totalAllocated = str + def + intVal;
  const remainingAP = 30 - totalAllocated;

  const handleOpenCreate = (slot) => {
    setTargetSlot(slot);
    setNewCharName('');
    setSelectedRace('Human');
    setStr(10);
    setDef(10);
    setIntVal(10);
    setAvatarIdx(slot % avatars.length);
    setCreateModalOpen(true);
  };

  const handleCreateSubmit = () => {
    if (!newCharName.trim()) return;

    createCharacter(targetSlot, {
      name: newCharName.trim(),
      race: selectedRace,
      avatar: avatars[avatarIdx],
      str,
      def,
      int: intVal,
    });
    setCreateModalOpen(false);
  };

  const handleSelect = (character) => {
    selectCharacter(character);
    navigate('/game/sheet');
  };

  return (
    <Box
      sx={{
        minHeight: '100vh',
        py: 6,
        px: 2,
        backgroundImage: 'radial-gradient(circle at 50% 20%, rgba(212, 175, 55, 0.08) 0%, #07090e 80%)',
      }}
    >
      <Container maxWidth="lg">
        {/* Top Header */}
        <Box
          sx={{
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            mb: 6,
            pb: 2,
            borderBottom: '1px solid rgba(212, 175, 55, 0.2)',
          }}
        >
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Box
              component="img"
              src="/img/logos/logo-banner-no-bg.webp"
              alt="Logo"
              sx={{ height: 44, display: { xs: 'none', sm: 'block' } }}
            />
            <Box>
              <Typography variant="h5" sx={{ color: '#ffd700', letterSpacing: '0.04em' }}>
                Hall of Champions
              </Typography>
              <Typography variant="caption" sx={{ color: '#94a3b8' }}>
                Select an existing champion or forge a new path
              </Typography>
            </Box>
          </Box>

          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Typography variant="body2" sx={{ color: '#cbd5e1', display: { xs: 'none', sm: 'block' } }}>
              {account?.email}
            </Typography>
            <Button
              variant="outlined"
              color="primary"
              size="small"
              startIcon={<LogoutIcon />}
              onClick={() => {
                logout();
                navigate('/login');
              }}
            >
              Sign Out
            </Button>
          </Box>
        </Box>

        {/* 3 Character Slots */}
        <Grid container spacing={4} justifyContent="center">
          {characterSlots.map((char) => {
            const isEmpty = !char.name;

            return (
              <Grid item xs={12} md={4} key={char.slot}>
                <motion.div
                  initial={{ opacity: 0, y: 30 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.5, delay: char.slot * 0.1 }}
                  style={{ height: '100%' }}
                >
                  {isEmpty ? (
                    /* Empty Slot Card */
                    <Card
                      sx={{
                        height: '100%',
                        minHeight: '460px',
                        display: 'flex',
                        flexDirection: 'column',
                        justifyContent: 'center',
                        alignItems: 'center',
                        textAlign: 'center',
                        p: 3,
                        border: '2px dashed rgba(212, 175, 55, 0.25)',
                        backgroundColor: 'rgba(10, 14, 22, 0.5)',
                        cursor: 'pointer',
                        transition: 'all 0.3s ease',
                        '&:hover': {
                          borderColor: '#ffd700',
                          backgroundColor: 'rgba(212, 175, 55, 0.04)',
                          transform: 'translateY(-4px)',
                        },
                      }}
                      onClick={() => handleOpenCreate(char.slot)}
                    >
                      <AddCircleIcon sx={{ fontSize: 56, color: '#d4af37', mb: 2, opacity: 0.8 }} />
                      <Typography variant="h6" sx={{ color: '#f1f5f9', mb: 1 }}>
                        Slot {char.slot}: Empty
                      </Typography>
                      <Typography variant="body2" sx={{ color: '#64748b', mb: 3, maxWidth: '240px' }}>
                        No hero currently occupies this slot. Inscribe a new legend.
                      </Typography>
                      <Button variant="outlined" color="primary" startIcon={<AddIcon />}>
                        Forge Hero
                      </Button>
                    </Card>
                  ) : (
                    /* Occupied Slot Card */
                    <Card
                      sx={{
                        height: '100%',
                        minHeight: '460px',
                        display: 'flex',
                        flexDirection: 'column',
                        justifyContent: 'space-between',
                        position: 'relative',
                        overflow: 'hidden',
                        '&::after': {
                          content: '""',
                          position: 'absolute',
                          top: 0,
                          right: 0,
                          width: '80px',
                          height: '80px',
                          background: 'linear-gradient(135deg, transparent 50%, rgba(212, 175, 55, 0.15) 100%)',
                        },
                      }}
                    >
                      <CardContent sx={{ p: 3 }}>
                        {/* Avatar & Badge */}
                        <Box sx={{ position: 'relative', textAlign: 'center', mb: 2.5 }}>
                          <Box
                            component="img"
                            src={`/img/avatars/${char.avatar}`}
                            alt={char.name}
                            onError={(e) => {
                              e.target.src = '/img/avatars/avatar-1.webp';
                            }}
                            sx={{
                              width: 140,
                              height: 140,
                              borderRadius: '50%',
                              border: '3px solid #d4af37',
                              boxShadow: '0 0 20px rgba(212, 175, 55, 0.35)',
                              objectFit: 'cover',
                              backgroundColor: '#0c111c',
                            }}
                          />
                          <Box
                            sx={{
                              position: 'absolute',
                              bottom: 0,
                              left: '50%',
                              transform: 'translateX(-50%)',
                              backgroundColor: '#07090e',
                              border: '1px solid #d4af37',
                              color: '#ffd700',
                              px: 1.5,
                              py: 0.25,
                              borderRadius: '12px',
                              fontSize: '0.75rem',
                              fontWeight: 700,
                              fontFamily: "'Cinzel', serif",
                            }}
                          >
                            LVL {char.level}
                          </Box>
                        </Box>

                        {/* Name & Race */}
                        <Box sx={{ textAlign: 'center', mb: 2 }}>
                          <Typography variant="h5" sx={{ color: '#f8fafc', fontWeight: 700 }}>
                            {char.name}
                          </Typography>
                          <Typography variant="caption" sx={{ color: '#d4af37', letterSpacing: '0.08em' }}>
                            {char.race} • Slot {char.slot}
                          </Typography>
                        </Box>

                        {/* Location & Gold */}
                        <Box
                          sx={{
                            display: 'flex',
                            justifyContent: 'space-between',
                            p: 1.25,
                            borderRadius: 1,
                            backgroundColor: 'rgba(7, 10, 16, 0.75)',
                            border: '1px solid rgba(255, 255, 255, 0.06)',
                            mb: 2.5,
                          }}
                        >
                          <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                            <PlaceIcon sx={{ fontSize: 16, color: '#38bdf8' }} />
                            <Typography variant="caption" sx={{ color: '#cbd5e1', fontWeight: 500 }}>
                              Floor {char.floor}
                            </Typography>
                          </Box>
                          <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>
                            <MonetizationOnIcon sx={{ fontSize: 16, color: '#ffd700' }} />
                            <Typography variant="caption" sx={{ color: '#ffd700', fontWeight: 700 }}>
                              {char.gold?.toLocaleString()}
                            </Typography>
                          </Box>
                        </Box>

                        {/* Vitals */}
                        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1.25 }}>
                          <VitalsBar type="hp" current={char.vitals.hp} max={char.vitals.maxHp} />
                          <VitalsBar type="mp" current={char.vitals.mp} max={char.vitals.maxMp} />
                        </Box>
                      </CardContent>

                      <Box sx={{ p: 3, pt: 0 }}>
                        <Button
                          fullWidth
                          variant="contained"
                          color="primary"
                          size="large"
                          startIcon={<PlayArrowIcon />}
                          onClick={() => handleSelect(char)}
                          sx={{ py: 1.2 }}
                        >
                          Enter Realm
                        </Button>
                      </Box>
                    </Card>
                  )}
                </motion.div>
              </Grid>
            );
          })}
        </Grid>
      </Container>

      {/* Character Creation Modal */}
      <Dialog
        open={createModalOpen}
        onClose={() => setCreateModalOpen(false)}
        maxWidth="sm"
        fullWidth
        PaperProps={{
          sx: {
            backgroundColor: 'rgba(13, 18, 30, 0.95)',
            border: '1px solid rgba(212, 175, 55, 0.35)',
            backdropFilter: 'blur(20px)',
            borderRadius: 2,
            boxShadow: '0 16px 50px rgba(0, 0, 0, 0.85)',
          },
        }}
      >
        <DialogTitle
          sx={{
            fontFamily: "'Cinzel', serif",
            color: '#ffd700',
            borderBottom: '1px solid rgba(212, 175, 55, 0.2)',
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
          }}
        >
          Forge New Champion
          <Typography variant="caption" sx={{ color: '#94a3b8' }}>
            Slot {targetSlot}
          </Typography>
        </DialogTitle>

        <DialogContent sx={{ pt: 3 }}>
          {/* Avatar Selector Carousel */}
          <Box sx={{ textAlign: 'center', mb: 3 }}>
            <Typography variant="caption" sx={{ color: '#d4af37', letterSpacing: '0.08em', mb: 1, display: 'block' }}>
              CHOOSE VISAGE
            </Typography>
            <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 2 }}>
              <IconButton
                onClick={() => setAvatarIdx((prev) => (prev - 1 + avatars.length) % avatars.length)}
                sx={{ color: '#d4af37' }}
              >
                <ArrowBackIosNewIcon />
              </IconButton>
              <Box
                component="img"
                src={`/img/avatars/${avatars[avatarIdx]}`}
                alt="Avatar Preview"
                sx={{
                  width: 110,
                  height: 110,
                  borderRadius: '50%',
                  border: '2px solid #ffd700',
                  boxShadow: '0 0 16px rgba(212, 175, 55, 0.4)',
                  objectFit: 'cover',
                  backgroundColor: '#07090e',
                }}
              />
              <IconButton
                onClick={() => setAvatarIdx((prev) => (prev + 1) % avatars.length)}
                sx={{ color: '#d4af37' }}
              >
                <ArrowForwardIosIcon />
              </IconButton>
            </Box>
          </Box>

          {/* Name & Race Inputs */}
          <TextField
            fullWidth
            label="Champion's Name"
            variant="outlined"
            value={newCharName}
            onChange={(e) => setNewCharName(e.target.value)}
            sx={{ mb: 2 }}
            required
            placeholder="e.g. Elyndra Silverleaf"
          />

          <TextField
            fullWidth
            select
            label="Lineage / Race"
            value={selectedRace}
            onChange={(e) => setSelectedRace(e.target.value)}
            sx={{ mb: 1 }}
          >
            {Object.keys(raceModifiers).map((r) => (
              <MenuItem key={r} value={r}>
                {r}
              </MenuItem>
            ))}
          </TextField>

          <Typography variant="caption" sx={{ color: '#94a3b8', display: 'block', mb: 3, fontStyle: 'italic' }}>
            Trait: {raceModifiers[selectedRace].bonus}
          </Typography>

          {/* AP Allocator */}
          <Box
            sx={{
              p: 2,
              borderRadius: 1.5,
              backgroundColor: 'rgba(7, 10, 16, 0.8)',
              border: '1px solid rgba(212, 175, 55, 0.2)',
            }}
          >
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
              <Typography variant="subtitle2" sx={{ color: '#ffd700', fontFamily: "'Cinzel', serif" }}>
                Starting Attribute Allocation
              </Typography>
              <Typography
                variant="caption"
                sx={{
                  color: remainingAP === 0 ? '#10b981' : '#f59e0b',
                  fontWeight: 700,
                  px: 1.2,
                  py: 0.3,
                  borderRadius: 1,
                  backgroundColor: 'rgba(0, 0, 0, 0.4)',
                }}
              >
                Remaining AP: {remainingAP}
              </Typography>
            </Box>

            {/* STR */}
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 1.5 }}>
              <Box>
                <Typography variant="body2" sx={{ fontWeight: 600, color: '#f1f5f9' }}>
                  Strength (STR)
                </Typography>
                <Typography variant="caption" sx={{ color: '#64748b' }}>
                  Physical prowess & melee strike damage
                </Typography>
              </Box>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <IconButton
                  size="small"
                  disabled={str <= 8}
                  onClick={() => setStr(str - 1)}
                  sx={{ color: '#ef4444' }}
                >
                  <RemoveIcon fontSize="small" />
                </IconButton>
                <Typography variant="body1" sx={{ fontWeight: 700, minWidth: '24px', textAlign: 'center' }}>
                  {str}
                </Typography>
                <IconButton
                  size="small"
                  disabled={remainingAP <= 0}
                  onClick={() => setStr(str + 1)}
                  sx={{ color: '#10b981' }}
                >
                  <AddIcon fontSize="small" />
                </IconButton>
              </Box>
            </Box>

            {/* DEF */}
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 1.5 }}>
              <Box>
                <Typography variant="body2" sx={{ fontWeight: 600, color: '#f1f5f9' }}>
                  Defense (DEF)
                </Typography>
                <Typography variant="caption" sx={{ color: '#64748b' }}>
                  Armor fortification & maximum health pool
                </Typography>
              </Box>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <IconButton
                  size="small"
                  disabled={def <= 8}
                  onClick={() => setDef(def - 1)}
                  sx={{ color: '#ef4444' }}
                >
                  <RemoveIcon fontSize="small" />
                </IconButton>
                <Typography variant="body1" sx={{ fontWeight: 700, minWidth: '24px', textAlign: 'center' }}>
                  {def}
                </Typography>
                <IconButton
                  size="small"
                  disabled={remainingAP <= 0}
                  onClick={() => setDef(def + 1)}
                  sx={{ color: '#10b981' }}
                >
                  <AddIcon fontSize="small" />
                </IconButton>
              </Box>
            </Box>

            {/* INT */}
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <Box>
                <Typography variant="body2" sx={{ fontWeight: 600, color: '#f1f5f9' }}>
                  Intelligence (INT)
                </Typography>
                <Typography variant="caption" sx={{ color: '#64748b' }}>
                  Spell potency & maximum mana capacity
                </Typography>
              </Box>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <IconButton
                  size="small"
                  disabled={intVal <= 8}
                  onClick={() => setIntVal(intVal - 1)}
                  sx={{ color: '#ef4444' }}
                >
                  <RemoveIcon fontSize="small" />
                </IconButton>
                <Typography variant="body1" sx={{ fontWeight: 700, minWidth: '24px', textAlign: 'center' }}>
                  {intVal}
                </Typography>
                <IconButton
                  size="small"
                  disabled={remainingAP <= 0}
                  onClick={() => setIntVal(intVal + 1)}
                  sx={{ color: '#10b981' }}
                >
                  <AddIcon fontSize="small" />
                </IconButton>
              </Box>
            </Box>
          </Box>
        </DialogContent>

        <DialogActions sx={{ p: 2.5, borderTop: '1px solid rgba(212, 175, 55, 0.2)' }}>
          <Button variant="text" sx={{ color: '#94a3b8' }} onClick={() => setCreateModalOpen(false)}>
            Cancel
          </Button>
          <Button
            variant="contained"
            color="primary"
            onClick={handleCreateSubmit}
            disabled={!newCharName.trim() || remainingAP !== 0}
          >
            Forge Champion
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};
