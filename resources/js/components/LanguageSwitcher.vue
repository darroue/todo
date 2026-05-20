<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import type { Locale } from '@/composables/useLocale';
import { i18n } from '@/i18n';
import { update } from '@/routes/language';
import type { Auth } from '@/types';

const { locale: i18nLocale } = useI18n();
const page = usePage<{ auth?: Auth }>();

const locale = ref<Locale>('en');

const locales: { value: Locale; flag: string; label: string }[] = [
    { value: 'en', flag: '🇬🇧', label: 'English' },
    { value: 'cs', flag: '🇨🇿', label: 'Čeština' },
];

const isAuthenticated = computed(() => !!page.props.auth?.user);

onMounted(() => {
    const userLocale = page.props.auth?.user?.locale as Locale | undefined;
    const stored = localStorage.getItem('locale') as Locale | null;
    locale.value = userLocale ?? stored ?? 'en';
});

function switchLocale(value: Locale) {
    locale.value = value;
    i18n.global.locale.value = value;
    i18nLocale.value = value;
    localStorage.setItem('locale', value);

    if (isAuthenticated.value) {
        router.patch(update().url, { locale: value }, { preserveScroll: true });
    }
}
</script>

<template>
    <div class="flex gap-1">
        <button
            v-for="{ value, flag, label } in locales"
            :key="value"
            :title="label"
            :aria-label="label"
            @click="switchLocale(value)"
            :class="[
                'rounded px-1.5 py-0.5 text-base leading-none transition-colors',
                locale === value
                    ? 'bg-neutral-200 dark:bg-neutral-700'
                    : 'opacity-40 hover:opacity-70',
            ]"
        >
            {{ flag }}
        </button>
    </div>
</template>
