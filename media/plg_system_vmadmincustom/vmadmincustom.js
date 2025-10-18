(function () {
  "use strict";

  // evitiamo doppie inizializzazioni
  if (window.__VMADMINCUSTOM_BOUND__) return;
  window.__VMADMINCUSTOM_BOUND__ = true;

  const CFG = window.VMACFG || {};

  // trova il contenitore dei campi VM nel backend
  function findContainer() {
    return document.querySelector("ul.vmuikit-js-container-removable");
  }

  function enhance(container) {
    if (!container || container.dataset.vmcfEnhanced === "1") return;

    const items = container.querySelectorAll("li.vmuikit-js-removable");
    const groups = [];
    let current = null;

    // mappa gruppi (G) e figli
    items.forEach((li, i) => {
      const type = li.querySelector('input[name*="[field_type]"]')?.value || "";
      const titleEl = li.querySelector(".uk-text-bold");
      const title = titleEl ? titleEl.textContent.trim() : `Gruppo ${i}`;

      if (type === "G") {
        li.classList.add("group-header");
        current = { el: li, name: title, children: [] };
        groups.push(current);
      } else if (current) {
        current.children.push(li);
      }
    });

    if (!groups.length) {
      container.dataset.vmcfEnhanced = "1";
      return;
    }

    // --- Toolbar globale Apri/Chiudi tutto
    if (CFG.showGlobalButtons && !document.querySelector(".vmcf-toolbar")) {
      const tb = document.createElement("div");
      tb.className = "vmcf-toolbar uk-margin-small-bottom";

      const openAll = document.createElement("button");
      openAll.type = "button";
      openAll.className = "uk-button uk-button-small uk-button-default";
      openAll.textContent =
        (window.Joomla &&
          Joomla.Text &&
          Joomla.Text._("PLG_SYSTEM_VMADMINCUSTOM_OPEN_ALL")) ||
        "Apri tutti";

      const closeAll = document.createElement("button");
      closeAll.type = "button";
      closeAll.className =
        "uk-button uk-button-small uk-button-secondary uk-margin-small-left";
      closeAll.textContent =
        (window.Joomla &&
          Joomla.Text &&
          Joomla.Text._("PLG_SYSTEM_VMADMINCUSTOM_CLOSE_ALL")) ||
        "Chiudi tutti";

      openAll.addEventListener("click", (e) => {
        e.preventDefault();
        groups.forEach((g) => setGroupOpen(g, true));
      });
      closeAll.addEventListener("click", (e) => {
        e.preventDefault();
        groups.forEach((g) => setGroupOpen(g, false));
      });

      container.parentNode.insertBefore(tb, container);
      tb.appendChild(openAll);
      tb.appendChild(closeAll);
    }

    // --- Inizializza i gruppi
    groups.forEach((group, idx) => {
      const header = group.el.querySelector(".uk-text-bold");
      if (!header) return;

      // icona UIkit chevron-right
      const icon = document.createElement("span");
      icon.className = "vmcf-toggle uk-margin-small-right";
      icon.setAttribute("uk-icon", "chevron-right");
      icon.setAttribute("aria-hidden", "true");
      icon.style.cursor = "pointer";
      header.prepend(icon);

      // click su header: toggle
      header.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        const isOpen = !group.el.classList.contains("collapsed");
        setGroupOpen(group, !isOpen);

        // chiusura automatica degli altri gruppi quando ne apro uno
        if (CFG.autoCloseGroups && !isOpen) {
          groups.forEach((g2) => {
            if (g2 !== group) setGroupOpen(g2, false);
          });
        }
      });

      // stato iniziale
      if (groups.length > 1) {
        if (CFG.openDefault && idx === 0) setGroupOpen(group, true);
        else setGroupOpen(group, false);
      } else {
        // un solo gruppo: tienilo aperto
        setGroupOpen(group, true);
      }
    });

    container.dataset.vmcfEnhanced = "1";
  }

  function setGroupOpen(group, open) {
    group.el.classList.toggle("collapsed", !open);
    group.children.forEach((li) => {
      li.style.display = open ? "" : "none";
    });
    const icon = group.el.querySelector(".vmcf-toggle");
    if (icon) icon.classList.toggle("is-open", open); // per la rotazione CSS
  }

  // init immediato se il DOM è già pronto
  function initIfReady() {
    const c = findContainer();
    if (c) enhance(c);
  }

  // 1) su DOM pronto
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initIfReady);
  } else {
    initIfReady();
  }

  // 2) osserva cambi nel DOM (VM/UiKit ridisegna liste)
  const mo = new MutationObserver(() => initIfReady());
  mo.observe(document.documentElement, { childList: true, subtree: true });
})();
