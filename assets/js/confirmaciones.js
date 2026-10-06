document.querySelectorAll("[data-swal-feedback]").forEach((feedback) => {
    const type = feedback.dataset.swalFeedbackType;
    const allowedTypes = ["success", "error", "warning", "info", "question"];
    const icon = allowedTypes.includes(type) ? type : "info";

    Swal.fire({
        text: feedback.textContent.trim(),
        icon,
        confirmButtonText: "Aceptar",
    });
});

document.querySelectorAll("form[data-swal-confirm]").forEach((form) => {
    form.addEventListener("submit", (event) => {
        if (form.dataset.swalConfirmed === "true") {
            delete form.dataset.swalConfirmed;
            return;
        }

        event.preventDefault();

        const submitter = event.submitter;
        Swal.fire({
            title: form.dataset.swalConfirmTitle || "¿Confirmar esta acción?",
            text: form.dataset.swalConfirmText || "",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, continuar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#435ebe",
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.swalConfirmed = "true";
                form.requestSubmit(submitter);
            }
        }).catch((error) => {
            console.error("No se pudo mostrar la confirmación.", error);
        });
    });
});
