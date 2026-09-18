import { create } from 'zustand';

export const useUIStore = create((set) => ({
  sidebarOpen: true,
  toggleSidebar: () => set((state) => ({ sidebarOpen: !state.sidebarOpen })),
  setSidebarOpen: (open) => set({ sidebarOpen: open }),

  apiOffline: false,
  setApiOffline: (offline) => set({ apiOffline: offline }),

  chatOpen: false,
  toggleChat: () => set((state) => ({ chatOpen: !state.chatOpen })),

  notification: {
    open: false,
    message: '',
    severity: 'info', // 'success' | 'info' | 'warning' | 'error'
  },
  notify: (message, severity = 'info') =>
    set({
      notification: {
        open: true,
        message,
        severity,
      },
    }),
  closeNotification: () =>
    set((state) => ({
      notification: { ...state.notification, open: false },
    })),
}));
