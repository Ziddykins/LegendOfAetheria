import React from 'react';
import {
  Box,
  Grid,
  Card,
  CardContent,
  Typography,
  IconButton,
  Tooltip,
  Divider,
  Chip,
} from '@mui/material';
import { motion } from 'framer-motion';
import AddCircleIcon from '@mui/icons-material/AddCircle';
import ShieldIcon from '@mui/icons-material/Shield';
import AutoAwesomeIcon from '@mui/icons-material/AutoAwesome';
import FitnessCenterIcon from '@mui/icons-material/FitnessCenter';
import SpeedIcon from '@mui/icons-material/Speed';
import CasinoIcon from '@mui/icons-material/Casino';
import MonetizationOnIcon from '@mui/icons-material/MonetizationOn';
import PlaceIcon from '@mui/icons-material/Place';
import BalanceIcon from '@mui/icons-material/Balance';
import { useCharacterStore } from '../store/useCharacterStore';
import { VitalsBar } from '../components/VitalsBar';

const rarityBorders = {
  common: 'rgba(148, 163, 184, 0.4)',
  rare: 'rgba(56, 189, 248, 0.65)',
  epic: 'rgba(192, 132, 252, 0.75)',
  legendary: 'rgba(251, 191, 36, 0.85)',
};

const rarityGlows = {
  common: 'none',
  rare: '0 0 12px rgba(56, 189, 248, 0.25)',
  epic: '0 0 14px rgba(192, 132, 252, 0.35)',
  legendary: '0 0 18px rgba(251, 191, 36, 0.45)',
};

export const CharacterSheetView = () => {
  const { activeCharacter, allocateAP } = useCharacterStore();

  if (!activeCharacter) return null;

  const { vitals, stats, equipment } = activeCharacter;

  const statList = [
    {
      key: 'str',
      name: 'Strength (STR)',
      value: stats.str,
      icon: <FitnessCenterIcon sx={{ color: '#ef4444' }} />,
      desc: 'Dictates raw physical power, weapon damage, and carrying capacity.',
    },
    {
      key: 'int',
      name: 'Intelligence (INT)',
      value: stats.int,
      icon: <AutoAwesomeIcon sx={{ color: '#38bdf8' }} />,
      desc: 'Amplifies magical incantations, elemental affinity, and maximum mana.',
    },
    {
      key: 'def',
      name: 'Defense (DEF)',
      value: stats.def,
      icon: <ShieldIcon sx={{ color: '#10b981' }} />,
      desc: 'Mitigates incoming physical strikes and fortifies max health pool.',
    },
    {
      key: 'agi',
      name: 'Agility (AGI)',
      value: stats.agi,
      icon: <SpeedIcon sx={{ color: '#fbbf24' }} />,
      desc: 'Determines turn speed, evasion reflexes, and critical strike frequency.',
    },
    {
      key: 'luc',
      name: 'Luck (LUC)',
      value: stats.luc,
      icon: <CasinoIcon sx={{ color: '#c084fc' }} />,
      desc: 'Influences rare treasure discoveries, critical fortune, and survival chance.',
    },
  ];

  const equipSlots = [
    { slotName: 'Head', item: equipment?.head, defaultIcon: 'sports_motorsports' },
    { slotName: 'Chest', item: equipment?.chest, defaultIcon: 'shield' },
    { slotName: 'Main Hand', item: equipment?.mainHand, defaultIcon: 'colorize' },
    { slotName: 'Off Hand', item: equipment?.offHand, defaultIcon: 'security' },
    { slotName: 'Legs', item: equipment?.legs, defaultIcon: 'styler' },
    { slotName: 'Feet', item: equipment?.feet, defaultIcon: 'snowshoeing' },
    { slotName: 'Amulet', item: equipment?.amulet, defaultIcon: 'diamond' },
    { slotName: 'Ring', item: equipment?.ring, defaultIcon: 'radio_button_checked' },
  ];

  return (
    <Box sx={{ maxWidth: '1200px', mx: 'auto' }}>
      {/* View Header */}
      <Box sx={{ mb: 4, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Box>
          <Typography variant="h4" sx={{ color: '#ffd700', letterSpacing: '0.04em' }}>
            Hero's Grimoire & Status
          </Typography>
          <Typography variant="body2" sx={{ color: '#94a3b8' }}>
            Comprehensive attributes, equipped armaments, and realm standing
          </Typography>
        </Box>

        {stats.ap > 0 && (
          <Chip
            label={`${stats.ap} Unspent AP Available`}
            color="warning"
            sx={{
              fontFamily: "'Cinzel', serif",
              fontWeight: 700,
              fontSize: '0.85rem',
              boxShadow: '0 0 12px rgba(245, 158, 11, 0.45)',
              animation: 'pulse 2s infinite',
              '@keyframes pulse': {
                '0%, 100%': { transform: 'scale(1)' },
                '50%': { transform: 'scale(1.04)' },
              },
            }}
          />
        )}
      </Box>

      <Grid container spacing={3}>
        {/* Left Column: Character Portrait & Vitals */}
        <Grid item xs={12} md={4}>
          <motion.div initial={{ opacity: 0, x: -20 }} animate={{ opacity: 1, x: 0 }} transition={{ duration: 0.4 }}>
            <Card sx={{ height: '100%' }}>
              <CardContent sx={{ p: 3, textAlign: 'center' }}>
                {/* Avatar Portrait */}
                <Box sx={{ position: 'relative', display: 'inline-block', mb: 2 }}>
                  <Box
                    component="img"
                    src={`/img/avatars/${activeCharacter.avatar}`}
                    alt={activeCharacter.name}
                    onError={(e) => {
                      e.target.src = '/img/avatars/avatar-1.webp';
                    }}
                    sx={{
                      width: 180,
                      height: 180,
                      borderRadius: 3,
                      border: '3px solid #d4af37',
                      boxShadow: '0 8px 30px rgba(0, 0, 0, 0.8), 0 0 20px rgba(212, 175, 55, 0.3)',
                      objectFit: 'cover',
                      backgroundColor: '#07090e',
                    }}
                  />
                  <Box
                    sx={{
                      position: 'absolute',
                      bottom: -10,
                      left: '50%',
                      transform: 'translateX(-50%)',
                      backgroundColor: '#07090e',
                      border: '1px solid #ffd700',
                      color: '#ffd700',
                      px: 2,
                      py: 0.3,
                      borderRadius: 2,
                      fontSize: '0.85rem',
                      fontWeight: 700,
                      fontFamily: "'Cinzel', serif",
                      whiteSpace: 'nowrap',
                    }}
                  >
                    Level {activeCharacter.level}
                  </Box>
                </Box>

                <Typography variant="h5" sx={{ color: '#f8fafc', fontWeight: 700, mt: 1 }}>
                  {activeCharacter.name}
                </Typography>
                <Typography variant="subtitle2" sx={{ color: '#d4af37', letterSpacing: '0.06em', mb: 3 }}>
                  The {activeCharacter.race}
                </Typography>

                {/* Vitals Summary */}
                <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2, mb: 3, textAlign: 'left' }}>
                  <VitalsBar type="hp" current={vitals.hp} max={vitals.maxHp} />
                  <VitalsBar type="mp" current={vitals.mp} max={vitals.maxMp} />
                  <VitalsBar type="ep" current={vitals.ep} max={vitals.maxEp} />
                </Box>

                <Divider sx={{ my: 2.5, borderColor: 'rgba(212, 175, 55, 0.15)' }} />

                {/* World Coordinates & Wealth */}
                <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1.5, textAlign: 'left' }}>
                  <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                      <PlaceIcon sx={{ fontSize: 18, color: '#38bdf8' }} />
                      <Typography variant="body2" sx={{ color: '#94a3b8' }}>
                        Current Domain:
                      </Typography>
                    </Box>
                    <Typography variant="body2" sx={{ fontWeight: 600, color: '#f1f5f9' }}>
                      {activeCharacter.location}
                    </Typography>
                  </Box>

                  <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                      <MonetizationOnIcon sx={{ fontSize: 18, color: '#ffd700' }} />
                      <Typography variant="body2" sx={{ color: '#94a3b8' }}>
                        Purse Wealth:
                      </Typography>
                    </Box>
                    <Typography variant="body2" sx={{ fontWeight: 700, color: '#ffd700' }}>
                      {activeCharacter.gold?.toLocaleString()} Gold
                    </Typography>
                  </Box>

                  <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                      <BalanceIcon sx={{ fontSize: 18, color: '#c084fc' }} />
                      <Typography variant="body2" sx={{ color: '#94a3b8' }}>
                        Moral Alignment:
                      </Typography>
                    </Box>
                    <Typography variant="body2" sx={{ fontWeight: 600, color: '#e2e8f0' }}>
                      {activeCharacter.alignment} / 100 (Righteous)
                    </Typography>
                  </Box>
                </Box>
              </CardContent>
            </Card>
          </motion.div>
        </Grid>

        {/* Center & Right Column: Attributes & Equipment */}
        <Grid item xs={12} md={8}>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
            {/* Attributes Card */}
            <motion.div initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4, delay: 0.1 }}>
              <Card>
                <CardContent sx={{ p: 3 }}>
                  <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                    <Typography variant="h6" sx={{ color: '#ffd700' }}>
                      Core Attributes
                    </Typography>
                    {stats.ap > 0 && (
                      <Typography variant="caption" sx={{ color: '#f59e0b', fontWeight: 600 }}>
                        Click + to assign attribute points
                      </Typography>
                    )}
                  </Box>

                  <Grid container spacing={2}>
                    {statList.map((stat) => (
                      <Grid item xs={12} sm={6} key={stat.key}>
                        <Tooltip title={stat.desc} arrow placement="top">
                          <Box
                            sx={{
                              p: 1.75,
                              borderRadius: 1.5,
                              backgroundColor: 'rgba(7, 10, 16, 0.75)',
                              border: '1px solid rgba(212, 175, 55, 0.18)',
                              display: 'flex',
                              justifyContent: 'space-between',
                              alignItems: 'center',
                              transition: 'all 0.2s ease',
                              '&:hover': {
                                borderColor: 'rgba(212, 175, 55, 0.45)',
                                backgroundColor: 'rgba(212, 175, 55, 0.04)',
                              },
                            }}
                          >
                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
                              {stat.icon}
                              <Typography variant="body2" sx={{ fontWeight: 600, color: '#f1f5f9' }}>
                                {stat.name}
                              </Typography>
                            </Box>

                            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                              <Typography
                                variant="body1"
                                sx={{
                                  fontWeight: 800,
                                  color: '#ffd700',
                                  fontSize: '1.1rem',
                                  fontVariantNumeric: 'tabular-nums',
                                }}
                              >
                                {stat.value}
                              </Typography>

                              {stats.ap > 0 && (
                                <IconButton
                                  size="small"
                                  onClick={() => allocateAP(stat.key)}
                                  sx={{
                                    color: '#10b981',
                                    p: 0.25,
                                    '&:hover': { color: '#34d399', transform: 'scale(1.15)' },
                                  }}
                                >
                                  <AddCircleIcon fontSize="small" />
                                </IconButton>
                              )}
                            </Box>
                          </Box>
                        </Tooltip>
                      </Grid>
                    ))}
                  </Grid>
                </CardContent>
              </Card>
            </motion.div>

            {/* Equipment Paperdoll Card */}
            <motion.div initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4, delay: 0.2 }}>
              <Card>
                <CardContent sx={{ p: 3 }}>
                  <Typography variant="h6" sx={{ color: '#ffd700', mb: 2 }}>
                    Equipped Armament & Relics
                  </Typography>

                  <Grid container spacing={2}>
                    {equipSlots.map((slot) => {
                      const hasItem = Boolean(slot.item);
                      const rarity = slot.item?.rarity || 'common';

                      return (
                        <Grid item xs={6} sm={3} key={slot.slotName}>
                          <Box
                            sx={{
                              p: 1.5,
                              minHeight: '92px',
                              borderRadius: 1.5,
                              backgroundColor: hasItem ? 'rgba(12, 17, 28, 0.85)' : 'rgba(5, 7, 12, 0.6)',
                              border: `1px solid ${hasItem ? rarityBorders[rarity] : 'rgba(255, 255, 255, 0.08)'}`,
                              boxShadow: hasItem ? rarityGlows[rarity] : 'none',
                              display: 'flex',
                              flexDirection: 'column',
                              justifyContent: 'space-between',
                              transition: 'transform 0.2s ease',
                              '&:hover': {
                                transform: hasItem ? 'translateY(-2px)' : 'none',
                              },
                            }}
                          >
                            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                              <Typography variant="caption" sx={{ color: '#64748b', fontWeight: 600, letterSpacing: '0.04em' }}>
                                {slot.slotName}
                              </Typography>
                              <span
                                className="material-symbols-outlined"
                                style={{
                                  fontSize: 18,
                                  color: hasItem ? '#ffd700' : '#475569',
                                }}
                              >
                                {slot.item?.icon || slot.defaultIcon}
                              </span>
                            </Box>

                            <Box sx={{ mt: 1 }}>
                              <Typography
                                variant="caption"
                                sx={{
                                  fontWeight: 600,
                                  color: hasItem ? '#f8fafc' : '#475569',
                                  display: '-webkit-box',
                                  WebkitLineClamp: 1,
                                  WebkitBoxOrient: 'vertical',
                                  overflow: 'hidden',
                                }}
                              >
                                {slot.item?.name || 'Empty'}
                              </Typography>
                              {hasItem && (
                                <Typography
                                  variant="caption"
                                  sx={{
                                    textTransform: 'uppercase',
                                    fontSize: '0.65rem',
                                    color: rarityBorders[rarity],
                                    fontWeight: 700,
                                  }}
                                >
                                  {rarity}
                                </Typography>
                              )}
                            </Box>
                          </Box>
                        </Grid>
                      );
                    })}
                  </Grid>
                </CardContent>
              </Card>
            </motion.div>
          </Box>
        </Grid>
      </Grid>
    </Box>
  );
};
