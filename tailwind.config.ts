import type { Config } from 'tailwindcss'

const config: Config = {
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#1f2933',   // primary brand color
          light: '#3b4a5a',
          dark: '#111827',
        },
        accent: {
          DEFAULT: '#f97316',
          soft: '#ffedd5',
        },

        // Needed for text-primary, bg-primary, border-primary, etc.
        primary: '#f97316',
      },

      fontFamily: {
        sans: ['Cabin', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        serif: ['Merriweather', 'ui-serif', 'Georgia', 'serif'],
      },

      maxWidth: {
        content: '72rem', // ~1152px
      },
    },
  },
}

export default config
