<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useTranslation';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

const { t } = useI18n();

setLayoutProps({
    title: t('auth.verify_email.title'),
    description: t('auth.verify_email.description'),
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head :title="t('auth.verify_email.title')" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ t('auth.verify_email.link_sent') }}
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button :disabled="processing" variant="secondary">
            <Spinner v-if="processing" />
            {{ t('auth.verify_email.button') }}
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            {{ t('common.log_out') }}
        </TextLink>
    </Form>
</template>
