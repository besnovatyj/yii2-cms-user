/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

document.addEventListener("DOMContentLoaded", () => {
    new RouteManager();
});

class RouteManager {
    constructor() {
        this.csrfToken = document.head.querySelector('[name="csrf-token"]').getAttribute("content");
        this.routeBlock = document.getElementById('route-block'); // весь блок работы с маршрутами
        this.opts = { routes: {} };

        this.initElements();
        this.bindEvents();

        // Загрузка начальных данных
        const initialData = JSON.parse(this.routeBlock.dataset.opts);
        this.populateRoutes(initialData).catch(console.error);
    }

    initElements() {
        this.elements = {
            btnNew: document.getElementById('btn-new'), // Кнопка создания нового маршрута
            btnRefresh: document.getElementById('btn-refresh'), // Кнопка обновления маршрутов
            createRouteInput: document.getElementById('inp-route'), // Поле ввода нового маршрута
            searchFields: this.routeBlock.querySelectorAll('.search[data-target]'), // Поля поиска
            refreshIcons: this.routeBlock.querySelectorAll('i.bi.bi-arrow-repeat.animate'), // Вращающиеся иконки
            assignButtons: this.routeBlock.querySelectorAll('.btn-assign'), // Кнопки перемещения маршрутов
            selectAvailable: this.routeBlock.querySelector('select.list[data-target="available"]'),
            selectAssigned: this.routeBlock.querySelector('select.list[data-target="assigned"]')
        };

        // Прячем анимированные иконки изначально
        this.elements.refreshIcons.forEach(icon => icon.style.display = 'none');
    }

    bindEvents() {
        this.elements.btnNew.addEventListener('click', e => this.createRoute(e));
        this.elements.btnRefresh.addEventListener('click', e => this.refresh(e));

        this.elements.assignButtons.forEach(btn => {
            btn.addEventListener('click', e => this.sendRequest(e));
        });

        this.elements.searchFields.forEach(input => {
            input.addEventListener('keyup', () => this.filterRoutes(input.dataset.target));
        });
    }

    async createRoute(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const input = this.elements.createRouteInput;
        const route = input.value.trim();
        if (!route) return;

        const url = this.elements.btnNew.dataset.url;
        const formData = new FormData();
        formData.append('route', route);

        this.toggleLoadingIcon(this.elements.btnNew, true);

        try {
            const response = await this.sendPost(url, formData);
            await this.populateRoutes(response.data);
            input.value = '';
            this.showAlert({ message: response.message, type: 'success' });
        } catch (err) {
            this.showAlert({ message: err.message, type: 'error' });
        }

        this.toggleLoadingIcon(this.elements.btnNew, false);
    }

    async refresh(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const url = this.elements.btnRefresh.dataset.url;
        this.toggleLoadingIcon(this.elements.btnRefresh, true, 'animate');

        try {
            const response = await this.sendPost(url);
            await this.populateRoutes(response.data);
            this.showAlert({ message: response.message, type: 'success' });
        } catch (err) {
            this.showAlert({ message: err.message, type: 'error' });
        }

        this.elements.btnRefresh.querySelector('i.bi.bi-arrow-repeat').classList.remove('animate');
    }

    async sendRequest(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const button = e.currentTarget;
        const target = button.dataset.target;
        const select = this.routeBlock.querySelector(`select.list[data-target="${target}"]`);
        const selectedItems = Array.from(select.selectedOptions).map(opt => opt.value);

        if (selectedItems.length === 0) return;

        const url = button.getAttribute('href');
        const formData = new FormData();
        formData.append('routes', JSON.stringify(selectedItems));

        this.toggleLoadingIcon(button, true);

        try {
            const response = await this.sendPost(url, formData);
            await this.populateRoutes(response.data);
            this.showAlert({ message: response.message, type: 'success' });
        } catch (err) {
            this.showAlert({ message: err.message, type: 'error' });
        }

        this.toggleLoadingIcon(button, false);
    }

    async populateRoutes(data) {
        this.opts.routes = {
            available: data.available || [],
            assigned: data.assigned || []
        };

        await this.filterRoutes('available');
        await this.filterRoutes('assigned');
    }

    async filterRoutes(target) {
        // TODO Element: replaceChildren()???? @see https://developer.mozilla.org/en-US/docs/Web/API/Element/replaceChildren
        const selectList = this.routeBlock.querySelector(`select.list[data-target="${target}"]`);
        const searchQuery = this.routeBlock.querySelector(`.search[data-target="${target}"]`).value.toLowerCase();
        selectList.innerHTML = '';

        for (let name in this.opts.routes[target]) {
            const route = this.opts.routes[target][name];
            if (route.toLowerCase().includes(searchQuery)) {
                const option = document.createElement('option');
                option.text = route;
                option.value = route;
                selectList.appendChild(option);
            }
        }
    }

    showAlert({ message, type }) {
        showAlert({message: message, type: type})
    }

    async sendPost(url, body = undefined) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'x-csrf-token': this.csrfToken
        };

        const response = await fetch(url, {
            method: 'POST',
            headers,
            body
        });

        if (!response.ok) {
            // Нативный формат ошибки Yii: {name, message, code, status}
            const errBody = await response.json().catch(() => ({}));
            throw new Error(errBody.message || errBody.name || `HTTP ${response.status}`);
        }

        return await response.json();
    }

    toggleLoadingIcon(element, show, className = 'animate') {
        const icon = element.querySelector('i.bi.bi-arrow-repeat');
        if (!icon) return;
        if (show) {
            icon.classList.add(className);
            icon.style.display = 'inline-block';
        } else {
            icon.classList.remove(className);
            icon.style.display = 'none';
        }
    }
}
