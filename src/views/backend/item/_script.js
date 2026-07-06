/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

document.addEventListener("DOMContentLoaded", () => {
    new AssignmentManager();
});

class AssignmentManager {
    constructor() {
        // Инициализация данных из DOM
        const assignmentBlock = document.getElementById('assignment-block');
        const rawData = JSON.parse(assignmentBlock.dataset.opts);

        this._opts = {
            items: rawData.items,
            getUserUrl: rawData.getUserUrl,
            userList: [],       // Список пользователей
            pagination: {}      // Данные пагинации
        };

        // Инициализируем начальные значения userList и pagination
        this.initUserData(rawData.users);

        // Инициализация элементов интерфейса
        this.initElements();

        // Привязка событий
        this.bindEvents();

        this.currentUsersPage = 0;

        // Инициализация начального состояния
        this.search('available');
        this.search('assigned');
        this.listUsers();
    }

    initUserData(usersData) {
        this._opts.userList = usersData.users || [];
        this._opts.pagination = {
            prev: usersData.prev,
            next: usersData.next,
            first: usersData.first,
            last: usersData.last
        };
    }

    initElements() {
        this.elements = {
            listUsers: document.getElementById('list-users'),
            availableSelect: document.querySelector('select.list[data-target="available"]'),
            assignedSelect: document.querySelector('select.list[data-target="assigned"]'),
            assignButtons: document.querySelectorAll('.btn-assign'),
            searchInputs: document.querySelectorAll('.search[data-target]'),
            refreshIcons: document.querySelectorAll('i.bi.bi-arrow-repeat.animate')
        };

        // Скрыть индикаторы загрузки
        this.elements.refreshIcons.forEach(icon => icon.style.display = 'none');
    }

    bindEvents() {
        // Обработчики для кнопок назначения (назначить/отобрать)
        this.elements.assignButtons.forEach(btn => {
            btn.addEventListener('click', e => this.handleAssign(e));
        });

        // Поиск по полям
        this.elements.searchInputs.forEach(input => {
            input.addEventListener('input', e => {
                this.search(e.target.dataset.target);
            });
        });

        // Пагинация пользователей
        if (this.elements.listUsers) {
            this.elements.listUsers.addEventListener('click', e => {
                const targetLink = e.target.closest('a[data-target]');
                if (targetLink) {
                    const page = parseInt(targetLink.dataset.target);
                    this.loadUserPage(page);
                    e.preventDefault();
                }
            });
        }
    }

    async handleAssign(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const button = e.currentTarget;
        const target = button.dataset.target;
        const select = document.querySelector(`select.list[data-target="${target}"]`);
        const selectedItems = Array.from(select.selectedOptions).map(opt => opt.value);

        if (!selectedItems.length) return;

        const url = button.getAttribute('href');

        this.toggleLoadingIcon(button, true);

        try {
            const formData = new FormData();
            formData.append('items', JSON.stringify(selectedItems));

            const response = await fetch(url, {
                method: 'POST',
                headers: this.getRequestHeaders(),
                body: formData
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const result = await response.json();
            if (result.status === 'success') {
                this.updateItems(result);
                this.showAlert({message: 'Перенесено: ' + result.success + ' шт.', type: 'success'});
            } else {
                this.showAlert({message: `Ошибка: ${result.message}`, type: 'error'});
            }
        } catch (err) {
            this.showAlert({message: `Ошибка: ${err.message}`, type: 'error'});
        }

        this.toggleLoadingIcon(button, false);
    }

    updateItems(data) {
        this._opts.items.available = data.available || {};
        this._opts.items.assigned = data.assigned || {};
        this.search('available');
        this.search('assigned');
    }

    async loadUserPage(page) {
        const {getUserUrl} = this._opts;
        const params = new URLSearchParams({page});

        try {
            const response = await fetch(`${getUserUrl}&${params}`, {
                method: 'GET',
                headers: this.getRequestHeaders()
            });

            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

            const result = await response.json();
            if (result.status === 'success') {
                this._opts.userList = result.users || [];
                this._opts.pagination = {
                    prev: result.prev,
                    next: result.next,
                    first: result.first,
                    last: result.last
                };

                // Сохраняем текущую страницу локально
                this.currentUsersPage = page;

                this.listUsers();
            } else {
                this.showAlert({message: `Ошибка: ${result.message}`, type: 'error'});
            }
        } catch (err) {
            this.showAlert({message: `Сеть: ${err.message}`, type: 'error'});
        }
    }

    listUsers_____firs_var() {
        const $list = this.elements.listUsers;
        const users = this._opts.userList.map(user => {
            return `<span class="label label-info"><a href="${user.link}">${user.username}</a></span>`;
        });

        users.push('<br>');

        if (this._opts.pagination.prev !== undefined) {
            users.push(`<span class="label label-primary">
                <a href="#" data-target="${this._opts.pagination.prev}">&laquo;</a>
            </span>`);
        }

        if (this._opts.pagination.next !== undefined) {
            users.push(`<span class="label label-primary">
                <a href="#" data-target="${this._opts.pagination.next}">&raquo;</a>
            </span>`);
        }

        $list.innerHTML = users.join(' ');
    }

    listUsers() {
        const $list = this.elements.listUsers;
        const users = this._opts.userList || [];
        const pagination = this._opts.pagination || {};

        // Устанавливаем текущую страницу (начинается с 0)
        const currentPage = this.currentUsersPage || 0;

        // Пагинация
        const prevLink = pagination.prev !== undefined
            ? `<li class="page-item"><a class="page-link" href="#" data-target="${pagination.prev}">&laquo;</a></li>`
            : `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;

        const nextLink = pagination.next !== undefined
            ? `<li class="page-item"><a class="page-link" href="#" data-target="${pagination.next}">&raquo;</a></li>`
            : `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;

        const paginationHtml = `
        <nav aria-label="Пагинация пользователей" class="mt-3">
            <ul class="pagination">
                ${prevLink}
                <li class="page-item disabled">
                    <span class="page-link">Страница ${(currentPage + 1)}</span>
                </li>
                ${nextLink}
            </ul>
        </nav>
    `;

        // Блок с пользователями
        const userBadges = users.length > 0
            ? `
        <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-light">
            ${users.map(user => `
                <a href="${user.link}"
                   class="badge text-bg-primary d-inline-flex align-items-center text-decoration-none"
                   title="Перейти к пользователю ${user.username}">
                    <i class="bi bi-person-fill me-1"></i>
                    ${user.username}
                </a>
            `).join('')}
        </div>`
            : `<div class="alert alert-info mb-2 mb-0">Нет пользователей на этой странице</div>`;

        // Собираем всё вместе
        $list.innerHTML = `
        ${userBadges}
        ${paginationHtml}
    `;
    }

    search(target) {
        const selectEl = document.querySelector(`select.list[data-target="${target}"]`);
        const q = document.querySelector(`.search[data-target="${target}"]`)?.value.trim().toLowerCase() || '';

        selectEl.innerHTML = '';

        const groups = {
            role: {el: document.createElement('optgroup'), label: 'Roles', hasItems: false},
            permission: {el: document.createElement('optgroup'), label: 'Permissions', hasItems: false},
            route: {el: document.createElement('optgroup'), label: 'Routes', hasItems: false}
        };

        groups.role.el.label = groups.role.label;
        groups.permission.el.label = groups.permission.label;
        groups.route.el.label = groups.route.label;

        for (const name in this._opts.items[target]) {
            const groupType = this._opts.items[target][name];
            if (name.toLowerCase().includes(q)) {
                const option = document.createElement('option');
                option.textContent = name;
                option.value = name;
                groups[groupType]?.el.appendChild(option);
                groups[groupType].hasItems = true;
            }
        }

        Object.values(groups).forEach(group => {
            if (group.hasItems) {
                selectEl.appendChild(group.el);
            }
        });
    }

    getRequestHeaders() {
        const csrfToken = document.head.querySelector('[name="csrf-token"]').content;
        return {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': csrfToken
        };
    }

    toggleLoadingIcon(button, show) {
        const icon = button.querySelector('i.bi.bi-arrow-repeat');
        if (!icon) return;
        icon.style.display = show ? 'inline-block' : 'none';
    }

    showAlert({message, type = 'info'}) {
        showAlert({message: message, type: type})
    }
}
