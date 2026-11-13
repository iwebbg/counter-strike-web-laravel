# План за развитие

[Български](roadmap.md) | [English](en/roadmap.md)

## Етап 1 — работещ портал (завършен)

- Laravel основа, MariaDB схема, примерни данни и Docker режими.
- Публични начална страница, списък и детайли за сървъри, и новини.
- Admin authentication, dashboard и управление на сървъри.
- Valve A2S info заявки и планирани snapshots.

## Етап 2 — развитие на общността (в процес)

- Завършено: player query challenge flow и snapshot класации, сортирани по резултат.
- Завършено: управление на категории/новини, BG/EN локализация и конфигурируеми branding/SEO настройки.
- Остава: дългосрочно обединяване на player identity, правила против дублиране, история на картите и uptime графики.
- Остава: uploads, audit log и детайлни администраторски роли.

## Етап 3 — операции

- Паралелно обновяване чрез опашки, retry/backoff политика и известия за проблемни заявки.
- Import/export, обогатяване чрез Steam Web API и опционален LGSL adapter.
- Browser и protocol integration тестове, observability и процедури за backup.
