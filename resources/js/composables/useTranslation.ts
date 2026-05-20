import { trans } from 'laravel-vue-i18n';

export function useI18n() {
    return { t: trans };
}
