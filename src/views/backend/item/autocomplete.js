/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

class Autocomplete {
    static setup(input, data) {
        if (!(input instanceof HTMLInputElement)) return;

        const container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.border = '1px solid #ccc';
        container.style.backgroundColor = '#fff';
        container.style.maxHeight = '200px';
        container.style.overflowY = 'auto';
        container.style.zIndex = '1000';
        input.parentNode.appendChild(container);

        const filterData = (value) => {
            const val = value.toLowerCase();
            return data.filter(item => item.toLowerCase().includes(val));
        };

        const showResults = (results) => {
            container.innerHTML = '';
            if (!results.length) {
                container.style.display = 'none';
                return;
            }

            results.forEach(item => {
                const div = document.createElement('div');
                div.textContent = item;
                div.style.padding = '8px';
                div.style.cursor = 'pointer';

                div.addEventListener('click', () => {
                    input.value = item;
                    container.style.display = 'none';
                });

                container.appendChild(div);
            });

            container.style.display = 'block';
        };

        input.addEventListener('input', (e) => {
            const results = filterData(e.target.value);
            showResults(results);
        });

        // Скрытие подсказок при клике вне области
        document.addEventListener('click', (e) => {
            if (e.target !== input && !container.contains(e.target)) {
                container.style.display = 'none';
            }
        });
    }
}

const inputElement = document.getElementById('rule_name');
const autocompleteSource = document.getElementById('auth-item-form').dataset.autocompleteSource;
const autocompleteSourceArray = JSON.parse(autocompleteSource);

Autocomplete.setup(inputElement, autocompleteSourceArray);
