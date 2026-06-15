<script setup lang="ts">
import { ChevronLeft, ChevronRight, Download, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, watch } from 'vue';
import { useI18n } from '@/composables/useTranslation';

type LightboxImage = {
    url: string;
    filename: string;
};

const props = defineProps<{
    images: LightboxImage[];
    index: number;
    open: boolean;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
    'update:index': [value: number];
}>();

const { t } = useI18n();

const current = computed(() => props.images[props.index] ?? null);
const hasMultiple = computed(() => props.images.length > 1);

function close() {
    emit('update:open', false);
}

async function download() {
    if (!current.value) {
        return;
    }
    const response = await fetch(current.value.url);
    const blob = await response.blob();
    const objectUrl = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = objectUrl;
    link.download = current.value.filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(objectUrl);
}

function prev() {
    if (!hasMultiple.value) {
        return;
    }
    emit('update:index', (props.index - 1 + props.images.length) % props.images.length);
}

function next() {
    if (!hasMultiple.value) {
        return;
    }
    emit('update:index', (props.index + 1) % props.images.length);
}

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        close();
    } else if (event.key === 'ArrowLeft') {
        prev();
    } else if (event.key === 'ArrowRight') {
        next();
    }
}

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            window.addEventListener('keydown', onKeydown);
            document.body.style.overflow = 'hidden';
        } else {
            window.removeEventListener('keydown', onKeydown);
            document.body.style.overflow = '';
        }
    },
);

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-150"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-150"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open && current"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
                data-test="image-lightbox"
                @click.self="close"
            >
                <button
                    type="button"
                    class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    :aria-label="t('todos.show.lightbox.close')"
                    data-test="image-lightbox-close"
                    @click="close"
                >
                    <X class="h-5 w-5" />
                </button>

                <button
                    type="button"
                    class="absolute right-16 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    :aria-label="t('todos.show.lightbox.download')"
                    data-test="image-lightbox-download"
                    @click.stop="download"
                >
                    <Download class="h-5 w-5" />
                </button>

                <button
                    v-if="hasMultiple"
                    type="button"
                    class="absolute left-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    :aria-label="t('todos.show.lightbox.previous')"
                    data-test="image-lightbox-prev"
                    @click.stop="prev"
                >
                    <ChevronLeft class="h-6 w-6" />
                </button>

                <figure class="flex max-h-[90vh] max-w-[90vw] flex-col items-center gap-3" @click.stop>
                    <img
                        :src="current.url"
                        :alt="current.filename"
                        class="max-h-[80vh] max-w-[90vw] rounded-md object-contain"
                    />
                    <figcaption class="flex items-center gap-2 text-sm text-white/80">
                        <span class="max-w-[60vw] truncate">{{ current.filename }}</span>
                        <span v-if="hasMultiple" class="text-white/50">
                            {{ index + 1 }} / {{ images.length }}
                        </span>
                    </figcaption>
                </figure>

                <button
                    v-if="hasMultiple"
                    type="button"
                    class="absolute right-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    :aria-label="t('todos.show.lightbox.next')"
                    data-test="image-lightbox-next"
                    @click.stop="next"
                >
                    <ChevronRight class="h-6 w-6" />
                </button>
            </div>
        </Transition>
    </Teleport>
</template>
