import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
    let persistentToastId: string | number | null = null;

    router.on('navigate', () => {
        if (persistentToastId !== null) {
            toast.dismiss(persistentToastId);
            persistentToastId = null;
        }
    });

    router.on('flash', (event) => {
        const flash = (event as CustomEvent).detail?.flash;
        const data = flash?.toast as FlashToast | undefined;

        if (!data) {
            return;
        }

        const id = toast[data.type](data.message, {
            duration: data.action ? Infinity : undefined,
            action: data.action
                ? {
                      label: data.action.label,
                      onClick: () => router.post(data.action!.url),
                  }
                : undefined,
        });

        if (data.action) {
            persistentToastId = id;
        }
    });
}
