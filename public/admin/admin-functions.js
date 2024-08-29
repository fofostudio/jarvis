const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

(function ($) {
    "use strict";

    // Inicializaciones
    function initComponents() {
        initGLightbox();
        initTooltips();
        initSelect2();
        initNumberInputs();
    }


    if (typeof window.appTranslations === "undefined") {
        console.error(
            "Las traducciones no están definidas. Asegúrate de que el script de traducciones se carga antes que admin-functions.js"
        );
        return;
    }
    function initGLightbox() {
        if (typeof GLightbox !== "undefined") {
            const lightbox = GLightbox({
                touchNavigation: true,
                loop: false,
                closeEffect: "fade",
            });
        } else {
            console.warn(
                "GLightbox no está definido. Asegúrate de que el script se haya cargado correctamente."
            );
        }
    }

    function initTooltips() {
        $(".showTooltip").tooltip();
    }

    function initSelect2() {
        if ($.fn.select2) {
            $(".select").select2({
                theme: "bootstrap-5",
            });
        }
    }

    function initNumberInputs() {
        $(".onlyNumber").keydown(function (e) {
            // Permitir: backspace, delete, tab, escape, enter y .
            if (
                $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Permitir: Ctrl+A, Command+A
                (e.keyCode === 65 &&
                    (e.ctrlKey === true || e.metaKey === true)) ||
                // Permitir: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)
            ) {
                return;
            }
            // Asegurar que sea un número y detener el keypress
            if (
                (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
                (e.keyCode < 96 || e.keyCode > 105)
            ) {
                e.preventDefault();
            }
        });
    }

    function setupActionDelete() {
        $(document).on("click", ".actionDelete", function (e) {
            e.preventDefault();
            var element = $(this);
            var form = element.closest("form");
            element.blur();

            Swal.fire({
                title: window.appTranslations.delete_confirm,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: window.appTranslations.yes_confirm,
                cancelButtonText: window.appTranslations.cancel_confirm,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }

    function setupFilter() {
        $(".filter").on("change", function () {
            window.location.href = $(this).val();
        });
    }

    function setupLoginAsUser() {
        $(".loginAsUser").on("click", function (e) {
            e.preventDefault();
            var element = $(this);
            var form = element.parents("form");
            element.blur();

            Swal.fire({
                title: window.appTranslations.delete_confirm,
                text: window.appTranslations.login_as_user_warning,
                icon: "warning",
                showLoaderOnConfirm: true,
                showCancelButton: true,
                confirmButtonColor: "#52bb03",
                confirmButtonText: window.appTranslations.yes,
                cancelButtonText: window.appTranslations.cancel_confirm,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }

    function setupTestSMTP() {
        $("#testSMTP").on("click", function (e) {
            e.preventDefault();
            $("#formTestSMTP").submit();
        });
    }

    // Inicialización principal
    $(document).ready(function () {
        try {
            initComponents();
            setupActionDelete();
            setupFilter();
            setupLoginAsUser();
            setupTestSMTP();
        } catch (error) {
            console.error("Se produjo un error en admin-functions.js:", error);
        }
    });
})(jQuery);
