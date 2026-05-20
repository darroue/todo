<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';
import type { Locale } from '@/composables/useLocale';
import { initializeLocale } from '@/composables/useLocale';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { Auth, BreadcrumbItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

const page = usePage<{ auth: Auth }>();

watchEffect(() => {
    const locale = page.props.auth?.user?.locale as Locale | undefined;
    if (locale) {
        initializeLocale(locale);
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
