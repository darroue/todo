import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { i18n } from '@/i18n';
import { update } from '@/routes/language';

export type Locale = 'en' | 'cs';

export function initializeLocale(locale: Locale): void {
    i18n.global.locale.value = locale;
}

const locale = ref<Locale>('en');

export type UseLocaleReturn = {
    locale: typeof locale;
    updateLocale: (value: Locale) => void;
};

export function useLocale(initialLocale?: Locale): UseLocaleReturn {
    onMounted(() => {
        if (initialLocale) {
            locale.value = initialLocale;
        }
    });

    function updateLocale(value: Locale) {
        locale.value = value;
        i18n.global.locale.value = value;

        router.patch(update().url, { locale: value }, { preserveScroll: true });
    }

    return {
        locale,
        updateLocale,
    };
}
