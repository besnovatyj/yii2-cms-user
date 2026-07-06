/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

document.addEventListener("DOMContentLoaded", () => {
    new AssignmentItemsManager();
});

class AssignmentItemsManager {
    constructor() {
        this.csrfToken = document.head.querySelector('[name="csrf-token"]').getAttribute("content");
        this.asgmtsBlock = document.getElementById('asgmts-block');

        // Инициализация начальных данных
        this.opts = {items: {}};
        this.initialData = JSON.parse(this.asgmtsBlock.dataset.opts);

        // Элементы интерфейса
        this.elements = {
            assignButtons: this.asgmtsBlock.querySelectorAll('.btn-assign'),
            searchFields: this.asgmtsBlock.querySelectorAll('.search[data-target]'),
            refreshIcons: this.asgmtsBlock.querySelectorAll('i.bi.bi-arrow-repeat.animate'),
        };

        // Прячем индикаторы загрузки
        this.elements.refreshIcons.forEach(icon => icon.style.display = 'none');

        // Инициализируем обработчики событий
        this.initEventListeners();

        // Загружаем начальные данные
        this.populateOpts(this.initialData).catch(console.error);
    }

    initEventListeners() {
        this.elements.assignButtons.forEach(btn => {
            btn.addEventListener('click', e => this.sendRequest(e));
        });

        this.elements.searchFields.forEach(input => {
            input.addEventListener('input', () => {
                this.filter(input.dataset.target).catch(console.error);
            });
        });
    }

    async sendRequest(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const button = e.currentTarget;
        const target = button.dataset.target;

        const selectList = this.asgmtsBlock.querySelector(`select.list[data-target="${target}"]`);
        const selectedItems = Array.from(selectList.selectedOptions).map(opt => opt.value);

        if (!selectedItems.length) return;

        const url = button.getAttribute('href');
        const formData = new FormData();
        formData.append('items', JSON.stringify(selectedItems));

        this.toggleLoadingIcon(button, true);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: this.getHeaders(),
                body: formData
            });

            if (!response.ok) {
                // Нативный формат ошибки Yii: {name, message, code, status}
                const errBody = await response.json().catch(() => ({}));
                throw new Error(errBody.message || errBody.name || `HTTP ${response.status}`);
            }

            const result = await response.json();
            await this.populateOpts(result.data);
            this.showAlert({message: result.message, type: 'success'});
        } catch (err) {
            this.showAlert({message: err.message, type: 'error'});
        }

        this.toggleLoadingIcon(button, false);
    }

    async filter(target) {
        // TODO Element: replaceChildren()???? @see https://developer.mozilla.org/en-US/docs/Web/API/Element/replaceChildren
        const selectList = this.asgmtsBlock.querySelector(`select.list[data-target="${target}"]`);
        const searchQuery = this.asgmtsBlock.querySelector(`.search[data-target="${target}"]`).value.toLowerCase();

        selectList.innerHTML = '';

        const groups = {
            role: [this.createOptgroup('Roles'), false],
            permission: [this.createOptgroup('Permission'), false],
        };

        for (const name in this.opts.items[target]) {
            const groupType = this.opts.items[target][name];
            if (name.toLowerCase().includes(searchQuery)) {
                const option = document.createElement('option');
                option.text = name;
                option.value = name;
                groups[groupType][0].append(option);
                groups[groupType][1] = true;
            }
        }

        for (const group in groups) {
            if (groups[group][1]) {
                selectList.append(groups[group][0]);
            }
        }
    }

    createOptgroup(label) {
        const optgroup = document.createElement('optgroup');
        optgroup.label = label;
        return optgroup;
    }

    async populateOpts(data) {
        this.opts.items.available = data.available || {};
        this.opts.items.assigned = data.assigned || {};
        await this.filter('available');
        await this.filter('assigned');
    }

    getHeaders() {
        return {
            'x-csrf-token': this.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        };
    }

    toggleLoadingIcon(button, show) {
        const icon = button.querySelector('i.bi.bi-arrow-repeat.animate');
        if (icon) {
            icon.style.display = show ? 'inline-block' : 'none';
        }
    }

    showAlert({message, type}) {
        showAlert({message: message, type: type})
    }

}
