/// <reference types="vite/client" />

import type { Stores } from 'alpinejs';
import type { User } from './src/assets/stores/auth';

interface IAuthStore {
  user: User | null;
  init: () => void;
  getUser: (project: string) => void;
  can: (action: string) => boolean;
  canOneOf: (permissions: string[]) => boolean;
  isAdmin: () => boolean;
}

declare module 'alpinejs' {
  export interface Stores {
    auth: IAuthStore;
  }
}
