// Vite entry for Laravel 12 + Tailwind + DaisyUI
import './bootstrap'
import '../css/app.css'

// ===== Theme handling (emerald <-> dark) =====
const THEME_KEY = 'dmatch_theme'
const root = document.documentElement

function setTheme(theme) {
    root.setAttribute('data-theme', theme)
    try { localStorage.setItem(THEME_KEY, theme) } catch {}
    }

    function initTheme() {
    let theme = null
    try { theme = localStorage.getItem(THEME_KEY) } catch {}
    if (!theme) {
        const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches
        theme = prefersDark ? 'dark' : 'emerald'
    }
    setTheme(theme)
    }

    document.addEventListener('DOMContentLoaded', () => {
    initTheme()

    // Any element with [data-theme-toggle] will toggle theme
    document.querySelectorAll('[data-theme-toggle]').forEach((el) => {
        el.addEventListener('click', () => {
        const current = root.getAttribute('data-theme') || 'emerald'
        setTheme(current === 'dark' ? 'emerald' : 'dark')
        })
    })
    })

    // ===== Helpers =====
    export function formatIDR(value) {
    try {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value)
    } catch {
        return `Rp ${String(value ?? 0).replace(/\B(?=(\d{3})+(?!\d))/g, '.')}`
    }
    }

    // Expose for inline Blade usage if needed
    window.formatIDR = formatIDR
