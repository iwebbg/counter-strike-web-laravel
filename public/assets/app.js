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

    const slugSource = document.querySelector('[data-slug-source]');
    const slugTarget = document.querySelector('[data-slug-target]');

    if (slugSource && slugTarget) {
        let slugWasEdited = slugTarget.value.length > 0;
        const cyrillic = { а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ж: 'zh', з: 'z', и: 'i', й: 'y', к: 'k', л: 'l', м: 'm', н: 'n', о: 'o', п: 'p', р: 'r', с: 's', т: 't', у: 'u', ф: 'f', х: 'h', ц: 'ts', ч: 'ch', ш: 'sh', щ: 'sht', ъ: 'a', ь: '', ю: 'yu', я: 'ya' };
        const slugify = (value) => value.toLowerCase().split('').map((character) => cyrillic[character] ?? character).join('').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

        slugTarget.addEventListener('input', () => { slugWasEdited = true; });
        slugSource.addEventListener('input', () => {
            if (! slugWasEdited) {
                slugTarget.value = slugify(slugSource.value);
            }
        });
    }
});
