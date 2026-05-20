import { createI18n } from 'vue-i18n';
import cs from './cs';
import en from './en';

const storedLocale =
    typeof window !== 'undefined'
        ? (localStorage.getItem('locale') as 'en' | 'cs' | null)
        : null;

export const i18n = createI18n({
    legacy: false,
    locale: storedLocale ?? 'en',
    fallbackLocale: 'en',
    messages: { en, cs },
});
