// Import the SCSS
import "../scss/admin.scss";

// Import page-specific modules
import "./pages/menu.js";

import tinymce from "tinymce/tinymce";
import "tinymce/themes/silver/theme";
import "tinymce/icons/default/icons";
import "tinymce/models/dom/model";
import "tinymce/plugins/link";
import "tinymce/plugins/image";
import "tinymce/plugins/lists";
import "tinymce/plugins/code";

// Import TinyMCE skins
import "tinymce/skins/ui/oxide/skin.css";
import "tinymce/skins/ui/oxide/content.css";
import "tinymce/skins/content/default/content.css";

// Admin JavaScript
console.log("Admin panel loaded");

// Example: CSRF token auto-insert
document.addEventListener("DOMContentLoaded", () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  if (csrfToken) {
    // Auto-add CSRF to all forms
    document.querySelectorAll("form").forEach((form) => {
      if (!form.querySelector('input[name="_token"]')) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "_token";
        input.value = csrfToken;
        form.appendChild(input);
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelectorAll('textarea[data-wysiwyg="1"]')
    .forEach((textarea) => {
      const type = textarea.dataset.wysiwygType || "tinymce";

      if (type === "tinymce") {
        tinymce.init({
          target: textarea,
          menubar: false,
          plugins: "link image lists code",
          toolbar:
            "undo redo | bold italic | bullist numlist | link image | code",
          height: 300,
          branding: false,
          promotion: false,
          license_key: "gpl",
          skin: false,
          content_css: false,
          images_upload_url: "/admin/upload-image", // Endpoint pour l'upload
          automatic_uploads: true,
          file_picker_types: "image",
          file_picker_callback: (callback, value, meta) => {
            if (meta.filetype === "image") {
              const input = document.createElement("input");
              input.setAttribute("type", "file");
              input.setAttribute("accept", "image/*");

              input.onchange = function () {
                const file = this.files[0];
                const formData = new FormData();
                formData.append("file", file);

                fetch("/admin/upload-image", {
                  method: "POST",
                  body: formData,
                })
                  .then((response) => response.json())
                  .then((data) => {
                    if (data && data.location) {
                      callback(data.location, { alt: file.name });
                    } else {
                      alert("Échec de l'upload de l'image.");
                    }
                  })
                  .catch(() => {
                    alert("Erreur lors de l'upload de l'image.");
                  });
              };

              input.click();
            }
          },
        });
      }
    });
});
