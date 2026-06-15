<script setup lang="ts">
import { getInitials } from '@/composables/useInitials';
import type { PresenceUser } from '@/composables/useTodoPresence';

type Props = {
    users: PresenceUser[];
};

defineProps<Props>();
</script>

<template>
    <div
        v-if="users.length > 0"
        class="flex -space-x-1.5"
        data-test="presence-indicators"
    >
        <span
            v-for="user in users"
            :key="user.id"
            :title="user.name"
            :data-test="`presence-user-${user.id}`"
            class="inline-flex h-6 w-6 items-center justify-center rounded-full border-2 border-background bg-primary text-[10px] font-medium text-primary-foreground ring-1 ring-primary/20"
        >
            {{ getInitials(user.name) }}
        </span>
    </div>
</template>
