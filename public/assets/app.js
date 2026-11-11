/** Boots navigation and clipboard interactions. */
document.addEventListener('DOMContentLoaded', () => {
    const navigation = document.querySelector('[data-nav]');
    document.querySelector('[data-nav-toggle]')?.addEventListener('click', () => navigation?.classList.toggle('open'));
    document.querySelectorAll('[data-copy]').forEach((button) => button.addEventListener('click', async () => {
        await navigator.clipboard.writeText(button.dataset.copy);
        const text = button.textContent;
        button.textContent = document.body.dataset.copySuccess;
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

    document.querySelectorAll('[data-locale-tabs]').forEach((tabs) => {
        const buttons = [...tabs.querySelectorAll('[data-locale-tab]')];
        const panels = [...tabs.querySelectorAll('[data-locale-panel]')];
        const activate = (locale, moveFocus = false) => {
            buttons.forEach((button) => {
                const isActive = button.dataset.localeTab === locale;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                button.tabIndex = isActive ? 0 : -1;
                if (isActive && moveFocus) button.focus();
            });
            panels.forEach((panel) => { panel.hidden = panel.dataset.localePanel !== locale; });
            tabs.dataset.activeLocale = locale;
        };

        buttons.forEach((button, index) => {
            button.addEventListener('click', () => activate(button.dataset.localeTab));
            button.addEventListener('keydown', (event) => {
                let nextIndex = index;
                if (event.key === 'ArrowRight') nextIndex = (index + 1) % buttons.length;
                else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + buttons.length) % buttons.length;
                else if (event.key === 'Home') nextIndex = 0;
                else if (event.key === 'End') nextIndex = buttons.length - 1;
                else return;

                event.preventDefault();
                activate(buttons[nextIndex].dataset.localeTab, true);
            });
        });

        tabs.closest('form')?.addEventListener('invalid', (event) => {
            const panel = event.target.closest('[data-locale-panel]');
            if (panel?.hidden) activate(panel.dataset.localePanel);
        }, true);

        activate(tabs.dataset.activeLocale ?? buttons[0]?.dataset.localeTab);
    });

    document.querySelectorAll('[data-rich-editor]').forEach((editor) => {
        const content = editor.querySelector('.editor-content');
        const source = editor.querySelector('.editor-source');
        const sync = () => { source.value = content.innerHTML.trim(); };

        content.addEventListener('input', sync);
        editor.closest('form')?.addEventListener('submit', sync);
        editor.querySelectorAll('[data-editor-command]').forEach((button) => button.addEventListener('click', () => {
            const command = button.dataset.editorCommand;
            let value = button.dataset.editorValue ?? null;

            if (command === 'createLink') {
                value = window.prompt(editor.dataset.linkPrompt, 'https://');
                if (! value) return;
            }

            content.focus();
            document.execCommand(command, false, value);
            sync();
        }));
    });
});
