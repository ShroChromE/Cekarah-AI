import { createInertiaApp } from '@inertiajs/react';

createInertiaApp({
    title: (title) => (title ? `${title} — Cekarah` : 'Cekarah'),
    layout: () => null,
    strictMode: true,
    progress: {
        color: '#3B82F6',
    },
});
