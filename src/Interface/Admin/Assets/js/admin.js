import "../scss/admin.scss";

import "./pages/menu.js";

import tinymce from "tinymce/tinymce";
import "tinymce/themes/silver/theme";
import "tinymce/icons/default/icons";
import "tinymce/models/dom/model";
import "tinymce/plugins/link";
import "tinymce/plugins/image";
import "tinymce/plugins/lists";
import "tinymce/plugins/code";

import "tinymce/skins/ui/oxide/skin.css";
import "tinymce/skins/ui/oxide/content.css";
import "tinymce/skins/content/default/content.css";

console.log("Admin panel loaded");

document.addEventListener("DOMContentLoaded", () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  if (csrfToken) {
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
  tinymce.init({
    selector: 'textarea[data-wysiwyg="1"], #editor',
    menubar: false,
    plugins: "link image lists code template",
    toolbar:
      "components | columns | h2 h3 h4 | alignleft aligncenter alignright undo redo | bold italic | bullist numlist | link image | code",
    height: 300,
    branding: false,
    promotion: false,
    license_key: "gpl",
    skin: false,
    // inject compiled page/_crud.scss (ensure this is built to /dist/css/admin-crud-style.css)
    content_css: "/dist/css/admin-crud-style.css",
    images_upload_url: "/admin/upload-image",
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
    setup(editor) {
      editor.ui.registry.addMenuButton("components", {
        text: "Composants",
        fetch: (callback) => {
          callback([
            {
              type: "menuitem",
              text: "Titre + Image (2 colonnes)",
              onAction: () => {
                const htmlInner = `
                  <div class="bare-crud__text-image">
                    <div class="bare-crud__text-image__text"><h3 class="comp-title">Titre</h3></div>
                    <div class="bare-crud__text-image__image"><img src="/dist/images/placeholder.png" alt="Image" class="comp-image" /></div>
                  </div>
                `;

                const newNode = editor.dom.create(
                  "div",
                  { class: "bare-crud__text-image" },
                  htmlInner
                );

                const selNode = editor.selection.getNode();
                if (
                  selNode &&
                  selNode.nodeName !== "BODY" &&
                  selNode.parentNode
                ) {
                  editor.dom.insertAfter(newNode, selNode);
                } else {
                  editor.getBody().appendChild(newNode);
                }

                const titleNode = editor.dom.select(
                  ".bare-crud__text-image__text",
                  newNode
                )[0];
                if (titleNode) {
                  editor.selection.setCursorLocation(titleNode, 0);
                  editor.focus();
                }
              },
            },
          ]);
        },
      });

      editor.ui.registry.addMenuButton("columns", {
        text: "Colonnes",
        fetch: (callback) => {
          const items = [];
          for (let n = 1; n <= 6; n++) {
            items.push({
              type: "menuitem",
              text: n === 1 ? "1 colonne" : `${n} colonnes`,
              onAction: () => {
                let cols = "";
                for (let i = 1; i <= n; i++) {
                  cols += `<div class="mce-col"><p>Colonne ${i}</p></div>`;
                }
                const newNode = editor.dom.create(
                  "div",
                  {
                    class: "mce-columns",
                    style: `grid-template-columns: repeat(${n}, 1fr);`,
                  },
                  cols
                );

                const existing = editor.dom.select(".mce-columns");
                const last =
                  existing && existing.length
                    ? existing[existing.length - 1]
                    : null;
                if (last && last.parentNode) {
                  editor.dom.insertAfter(newNode, last);
                } else {
                  editor.getBody().appendChild(newNode);
                }

                const firstCol = editor.dom.select(".mce-col", newNode)[0];
                if (firstCol) {
                  editor.selection.setCursorLocation(firstCol, 0);
                  editor.focus();
                }
              },
            });
          }
          callback(items);
        },
      });
    },
  });
});
