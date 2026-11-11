/** Boots navigation and clipboard interactions. */
document.addEventListener('DOMContentLoaded', () => {
    const navigation = document.querySelector('[data-nav]');
    document.querySelector('[data-nav-toggle]')?.addEventListener('click', () => navigation?.classList.toggle('open'));
    document.querySelectorAll('[data-copy]').forEach((button) => button.addEventListener('click', async () => {
        await navigator.clipboard.writeText(button.dataset.copy);
        const text = button.textContent;
        button.textContent = 'КОПИРАНО ✓';
        window.setTimeout(() => { button.textContent = text; }, 1200);
    }));
});
