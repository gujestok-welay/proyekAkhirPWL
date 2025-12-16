// Modal Konfirmasi Reusable (Bootstrap 5)
// options: { title, message, confirmText, cancelText, variant, onConfirm }
window.showConfirm = function (options) {
  const opts = Object.assign(
    {
      title: "Konfirmasi Aksi",
      message: "Apakah Anda yakin ingin melanjutkan?",
      confirmText: "Ya, Lanjutkan",
      cancelText: "Batal",
      variant: "primary",
      onConfirm: null,
    },
    options || {}
  );

  let modalEl = document.getElementById("confirmModal");
  if (!modalEl) {
    console.warn("confirmModal element not found");
    return;
  }

  // Set content
  modalEl.querySelector(".confirm-title").textContent = opts.title;
  modalEl.querySelector(".confirm-message").textContent = opts.message;

  const confirmBtn = modalEl.querySelector(".btn-confirm");
  const cancelBtn = modalEl.querySelector(".btn-cancel");

  // Reset classes then apply variant
  confirmBtn.className =
    "btn btn-" + (opts.variant || "primary") + " btn-confirm";
  confirmBtn.textContent = opts.confirmText;
  cancelBtn.textContent = opts.cancelText;

  // Clean previous listeners
  confirmBtn.replaceWith(confirmBtn.cloneNode(true));
  cancelBtn.replaceWith(cancelBtn.cloneNode(true));

  // Re-bind after clone
  const newConfirmBtn = modalEl.querySelector(".btn-confirm");
  newConfirmBtn.addEventListener("click", function () {
    if (typeof opts.onConfirm === "function") opts.onConfirm();
    bootstrap.Modal.getInstance(modalEl)?.hide();
  });

  // Show modal
  const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl, {
    backdrop: "static",
  });
  bsModal.show();
};
