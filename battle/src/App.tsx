import { useEffect, useState } from 'react';
import { BattleArena } from '@/components/battle/BattleArena';
import type { BattleConfig } from '@/types/battle';

declare global {
  interface Window {
    AetheriaBattle?: {
      mount: (container: HTMLElement, config: BattleConfig) => void;
      unmount: () => void;
    };
  }
}

const defaultConfig: BattleConfig = {
  player: {
    name: 'Hero',
    level: 12,
    stats: {
      hp: 245,
      maxHP: 300,
      mp: 80,
      maxMP: 100,
      ep: 5,
      maxEP: 10,
      str: 45,
      def: 32,
    },
  },
  monster: {
    name: 'Shadow Wraith',
    level: 15,
    stats: {
      hp: 280,
      maxHP: 350,
      mp: 60,
      maxMP: 80,
      ep: 8,
      maxEP: 8,
      str: 52,
      def: 28,
    },
  },
  csrfToken: 'demo-token',
  apiEndpoint: '/battle',
};

function App() {
  const [config, setConfig] = useState<BattleConfig>(
    (window as any).__AETHERIA_CONFIG__ || defaultConfig
  );
  useEffect(() => {
    // Expose global mount function for embedding
    window.AetheriaBattle = {
      mount: (_container: HTMLElement, userConfig: BattleConfig) => {
        setConfig(userConfig);
        // In a real embed scenario, this would render into the container
        // For now, the component renders into #root
        console.log('AetheriaBattle mounted with config:', userConfig);
      },
      unmount: () => {
        console.log('AetheriaBattle unmounted');
      },
    };
  }, []);

  return (
    <div
      className="w-full min-h-screen flex items-center justify-center p-4"
      style={{
        background: 'var(--void-black)',
      }}
    >
      <BattleArena config={config} />
    </div>
  );
}

export default App;
