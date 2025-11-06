import { defineConfig } from 'eslint/config'
import js from '@eslint/js'
import tseslint from 'typescript-eslint'

export default defineConfig([
  // ...tseslint.configs.recommended,
  {
    plugins: {
      js,
    },
    extends: ['js/recommended', tseslint.configs.recommended],
    rules: {
      'comma-dangle': ['error', 'always-multiline'],
      semi: ['error', 'never'],
    },
    files: ['src/**/*.ts', 'src/**/*.tsx', 'src/**/*.js', 'src/**/*.jsx'],
    ignores: ['node_modules', 'assets'],
  },
])
