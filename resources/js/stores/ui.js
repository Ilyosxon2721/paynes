/**
 * UI Store - Pinia
 * Управление темой (dark/light) и состоянием UI
 */

import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

export const useUiStore = defineStore('ui', () => {
    // Theme: 'light' | 'dark' | 'system'
    const theme = ref(localStorage.getItem('paynes-theme') || 'light');
    const sidebarCollapsed = ref(localStorage.getItem('paynes-sidebar-collapsed') === 'true');

    // Computed dark mode based on theme setting
    const isDark = ref(false);

    // Apply theme to document
    function applyTheme() {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (theme.value === 'system') {
            isDark.value = prefersDark;
        } else {
            isDark.value = theme.value === 'dark';
        }

        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    // Set theme
    function setTheme(newTheme) {
        theme.value = newTheme;
        localStorage.setItem('paynes-theme', newTheme);
        applyTheme();
    }

    // Toggle theme
    function toggleTheme() {
        const newTheme = isDark.value ? 'light' : 'dark';
        setTheme(newTheme);
    }

    // Toggle sidebar
    function toggleSidebar() {
        sidebarCollapsed.value = !sidebarCollapsed.value;
        localStorage.setItem('paynes-sidebar-collapsed', sidebarCollapsed.value);
    }

    // Initialize theme on store creation
    applyTheme();

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (theme.value === 'system') {
            applyTheme();
        }
    });

    return {
        theme,
        isDark,
        sidebarCollapsed,
        setTheme,
        toggleTheme,
        toggleSidebar,
        applyTheme
    };
});
