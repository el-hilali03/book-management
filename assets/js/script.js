

document.addEventListener("DOMContentLoaded", function () {

    // AUTO HIDE ALERTS

    const alerts = document.querySelectorAll(".alert");

    alerts.forEach(function (alert) {

        setTimeout(function () {

            alert.style.opacity = "0";

            alert.style.transition = "opacity 0.5s";

            setTimeout(function () {
                alert.remove();
            }, 500);

        }, 4000);

    });


    // CONFIRM DELETE

    const deleteForms = document.querySelectorAll(".delete-form");

    deleteForms.forEach(function (form) {

        form.addEventListener("submit", function (event) {

            const confirmed = confirm(
                "Are you sure you want to delete this book?"
            );

            if (!confirmed) {
                event.preventDefault();
            }

        });

    });

    // CONFIRM REMOVE FAVORITE

    const favoriteForms =
        document.querySelectorAll(".remove-favorite-form");

    favoriteForms.forEach(function (form) {

        form.addEventListener("submit", function (event) {

            const confirmed = confirm(
                "Remove this book from your favorites?"
            );

            if (!confirmed) {
                event.preventDefault();
            }

        });

    });

    // PASSWORD SHOW / HIDE

    const passwordButtons =
        document.querySelectorAll(".toggle-password");

    passwordButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const inputId = button.getAttribute("data-target");

            const passwordInput =
                document.getElementById(inputId);

            if (!passwordInput) {
                return;
            }

            if (passwordInput.type === "password") {

                passwordInput.type = "text";

                button.textContent = "Hide";

            } else {

                passwordInput.type = "password";

                button.textContent = "Show";
            }

        });

    });


    // PREVENT DOUBLE FORM SUBMISSION

    const forms = document.querySelectorAll("form");

    forms.forEach(function (form) {

        form.addEventListener("submit", function () {

            const submitButton =
                form.querySelector('button[type="submit"]');

            if (!submitButton) {
                return;
            }

            // Don't disable search buttons
            if (
                submitButton.closest(".search-form")
            ) {
                return;
            }

            setTimeout(function () {

                submitButton.disabled = true;

                submitButton.style.opacity = "0.7";

            }, 10);

        });

    });

});