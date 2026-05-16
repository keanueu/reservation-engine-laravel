import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class', // Enables class-based dark mode

  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/**/*.vue',
    './public/**/*.php',
    './*.php',
  ],

  theme: {
    extend: {
      colors: {
        earth: {
          espresso: '#3C2A21',
          walnut: '#5B3A29',
          teak: '#8B4513',
          ochre: '#D4A373',
          sand: '#F5EBE0',
          parchment: '#FFF7F0',
          bronze: '#B07D62',
          clay: '#A67C52',
          mist: 'rgba(245, 235, 224, 0.68)',
        },
        calamity: {
          green: '#5E7C5A',
          amber: '#C38B2E',
          red: '#A94438',
        },
      },
      fontFamily: {
        sans: ['Raleway', ...defaultTheme.fontFamily.sans],
        serif: ['Raleway', ...defaultTheme.fontFamily.serif],
        mono: ['Raleway', ...defaultTheme.fontFamily.mono],
        display: ['Raleway', ...defaultTheme.fontFamily.sans],
      },
      boxShadow: {
        'wood-glass': '0 24px 60px rgba(60, 42, 33, 0.16)',
        'wood-glow': '0 10px 30px rgba(212, 163, 115, 0.18)',
      },
      borderRadius: {
        '4xl': '2rem',
      },
      backgroundImage: {
        'wood-frost':
          'linear-gradient(135deg, rgba(60, 42, 33, 0.74), rgba(91, 58, 41, 0.38))',
        'sand-sheen':
          'radial-gradient(circle at top left, rgba(255, 247, 240, 0.95), rgba(245, 235, 224, 0.76) 55%, rgba(212, 163, 115, 0.18))',
        'cta-ochre':
          'linear-gradient(135deg, #D4A373 0%, #E6BC87 100%)',
      },
      keyframes: {
        'fade-up': {
          '0%': { opacity: '0', transform: 'translateY(12px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'fade-up-lg': {
          '0%': { opacity: '0', transform: 'translateY(96px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'anchor-sway': {
          '0%, 100%': { transform: 'rotate(-6deg)' },
          '50%': { transform: 'rotate(6deg)' },
        },
        'door-open': {
          '0%': { transform: 'perspective(1200px) rotateY(0deg)' },
          '100%': { transform: 'perspective(1200px) rotateY(-14deg)' },
        },
      },
      animation: {
        'fade-up': 'fade-up 600ms cubic-bezier(.22,.98,.36,.99) both',
        'fade-up-slow': 'fade-up 1000ms cubic-bezier(.22,.98,.36,.99) both',
        'fade-up-lg': 'fade-up-lg 1400ms cubic-bezier(.22,.98,.36,.99) both',
        'fade-up-lg-slow': 'fade-up-lg 2200ms cubic-bezier(.22,.98,.36,.99) both',
        'anchor-sway': 'anchor-sway 1.6s ease-in-out infinite',
        'door-open': 'door-open 320ms ease forwards',
      },
    },
  },

  safelist: [
    'animate-fade-up',
    'animate-fade-up-slow',
    'animate-fade-up-lg',
    'animate-fade-up-lg-slow',
    'animate-anchor-sway',
    'animate-door-open'
  ],

  plugins: [forms, typography],
};

