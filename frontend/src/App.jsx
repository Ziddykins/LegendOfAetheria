import React, { useEffect } from 'react';
import { Routes, Route, Navigate, useNavigate } from 'react-router-dom';
import { AuthView } from './views/AuthView';
import { CharacterSelectView } from './views/CharacterSelectView';
import { GameShell } from './layouts/GameShell';
import { CharacterSheetView } from './views/CharacterSheetView';
import { PlaceholderView } from './views/PlaceholderView';
import { useAuthStore } from './store/useAuthStore';
import { useUIStore } from './store/useUIStore';
import { checkApiHealth } from './api/client';

export const App = () => {
  const { isAuthenticated } = useAuthStore();
  const { setApiOffline } = useUIStore();

  useEffect(() => {
    // Check if backend API is reachable
    checkApiHealth().then((isHealthy) => {
      setApiOffline(!isHealthy);
    });
  }, [setApiOffline]);

  return (
    <Routes>
      {/* Auth View */}
      <Route
        path="/login"
        element={isAuthenticated ? <Navigate to="/select" replace /> : <AuthView />}
      />

      {/* Character Selection */}
      <Route
        path="/select"
        element={isAuthenticated ? <CharacterSelectView /> : <Navigate to="/login" replace />}
      />

      {/* Main Game Shell & Nested Views */}
      <Route
        path="/game"
        element={isAuthenticated ? <GameShell /> : <Navigate to="/login" replace />}
      >
        <Route index element={<Navigate to="/game/sheet" replace />} />
        <Route path="sheet" element={<CharacterSheetView />} />
        <Route
          path="equipment"
          element={<PlaceholderView title="Equipment & Armory" description="Inspect weapon enchantments, armor fortification, and relic sockets." />}
        />
        <Route
          path="inventory"
          element={<PlaceholderView title="Backpack & Inventory" description="Sort potions, crafting ingredients, and rare dungeon loot." />}
        />
        <Route
          path="skills"
          element={<PlaceholderView title="Grimoire of Spells & Skills" description="Learn arcane destruction, restoration incantations, and combat arts." />}
        />
        <Route
          path="train"
          element={<PlaceholderView title="Training Grounds" description="Hone discipline with the citadel masters to increase stats." />}
        />
        <Route
          path="explore"
          element={<PlaceholderView title="Realm Cartography & Exploration" description="Traverse treacherous mountain passes, eerie swamps, and forgotten crypts." />}
        />
        <Route
          path="battle"
          element={<PlaceholderView title="Colosseum & Wild Encounters" description="Engage roaming beasts, dark sorcerers, and arena gladiators." />}
        />
        <Route
          path="bank"
          element={<PlaceholderView title="Citadel Vault & Bank" description="Deposit gold safely and earn interest away from lurking thieves." />}
        />
        <Route
          path="market"
          element={<PlaceholderView title="Underground Black Market" description="Trade contraband, forbidden relics, and black market wares." />}
        />
        <Route
          path="mail"
          element={<PlaceholderView title="Courier Mailbox" description="Send and receive courier missives across the continents." />}
        />
        <Route
          path="friends"
          element={<PlaceholderView title="Fellowship of Heroes" description="Form adventuring parties and coordinate dungeon raids." />}
        />
      </Route>

      {/* Root & Catch-all Fallback */}
      <Route path="/" element={<Navigate to="/login" replace />} />
      <Route path="*" element={<Navigate to="/login" replace />} />
    </Routes>
  );
};

export default App;
