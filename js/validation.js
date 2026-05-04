document.addEventListener("DOMContentLoaded", function () {

    // --- 1. Ініціалізація Choices.js (Випадаючий список жанрів) ---
    const genreElement = document.getElementById('genre-select');
    if (genreElement) {
        new Choices(genreElement, {
            removeItemButton: true,
            placeholder: true, // Вмикаємо підтримку плейсхолдера
            placeholderValue: 'Оберіть потрібні жанри...', // Головний текст підказки
            searchPlaceholderValue: 'Почніть вводити жанр...', // Текст, коли юзер починає друкувати
            noResultsText: 'Такого жанру не знайдено',
            itemSelectText: 'Натисніть, щоб обрати'
        });
    }

    // --- 2. Валідація основної форми (Створення / Редагування) ---
    const form = document.querySelector("form");

    // Запускаємо валідацію тільки якщо форма існує на сторінці
    if (form && !form.classList.contains('delete-forever-form') && form.id !== 'trash-form' && !form.classList.contains('form-inline')) {
        form.addEventListener("submit", function (event) {

            // Очищаємо помилки від попередньої спроби відправки
            document.querySelectorAll(".error-text").forEach(el => el.remove());
            document.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));

            let hasErrors = false;

            function showError(inputId, message) {
                hasErrors = true;
                const input = document.getElementById(inputId);
                if (!input) return;

                input.classList.add("input-error");

                const errorSpan = document.createElement("span");
                errorSpan.className = "error-text";
                errorSpan.textContent = "⚠️ " + message;
                input.parentNode.appendChild(errorSpan);
            }

            // Перевірка назви
            const titleInput = document.getElementById("title");
            if (titleInput) {
                const title = titleInput.value.trim();
                if (title.length < 2) {
                    showError("title", "Назва має містити мінімум 2 символи.");
                }
            }

            // Перевірка жанрів
            const genreSelect = document.getElementById("genre-select");
            if (genreSelect) {
                if (genreSelect.selectedOptions.length === 0) {
                    showError("genre-select", "Оберіть хоча б один жанр.");
                }
            }

            // Перевірка року
            const yearInput = document.getElementById("release_year");
            if (yearInput) {
                const year = parseInt(yearInput.value, 10);
                const currentYear = new Date().getFullYear();
                if (isNaN(year) || year < 1888 || year > currentYear + 2) {
                    showError("release_year", `Рік випуску має бути між 1888 та ${currentYear + 2}.`);
                }
            }

            // Перевірка оцінки
            const ratingInput = document.getElementById("rating");
            if (ratingInput) {
                const ratingVal = ratingInput.value.trim();
                if (ratingVal !== "") {
                    const rating = parseFloat(ratingVal);
                    if (isNaN(rating) || rating < 0 || rating > 10) {
                        showError("rating", "Оцінка має бути від 0 до 10.");
                    }
                }
            }

            // Блокуємо відправку, якщо є помилки
            if (hasErrors) {
                event.preventDefault();
            }
        });
    }

    // --- 3. Підтвердження для жорсткого видалення (Кошик) ---
    document.querySelectorAll('.delete-forever-form').forEach(function (formElement) {
        formElement.addEventListener('submit', function (event) {
            if (!confirm('Видалити назавжди? Це незворотна дія.')) {
                event.preventDefault();
            }
        });
    });

    // --- 4. Підтвердження для відправки в кошик (Сторінка фільму) ---
    const trashForm = document.getElementById('trash-form');
    if (trashForm) {
        trashForm.addEventListener('submit', function (event) {
            if (!confirm('Відправити фільм у кошик?')) {
                event.preventDefault();
            }
        });
    }

});