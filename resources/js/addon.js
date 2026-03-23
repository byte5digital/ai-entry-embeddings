const pages = import.meta.glob('../views/*.vue', { eager: true });

Statamic.booting(() => {
    Object.entries(pages).forEach(([path, module]) => {
        const name = path.match(/\/(\w+)\.vue$/)[1];
        Statamic.$inertia.register(`ai-entry-embeddings::${name}`, module.default);
    });
});
