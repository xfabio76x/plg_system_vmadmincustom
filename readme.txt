=========================================================
PLUGIN: System - VM Admin Custom
Version: 3.5 Final
Author: Fabio + ChatGPT
Date: 17/10/2025
License: GNU/GPL v3
Compatible with: Joomla 3 / Joomla 4 / Joomla 5
=========================================================

DESCRIPTION
-----------
This plugin enhances VirtueMart Custom Fields management inside the Joomla backend (VMadmin).
It makes the list of custom field groups collapsible and adds several UX improvements.

Le migliorie principali:
- Gruppi collassabili per i Campi Personalizzati di VirtueMart
- Toolbar globale con pulsanti "Apri tutti" / "Chiudi tutti"
- Memorizzazione dello stato aperto/chiuso (sessione o localStorage)
- Apertura automatica di gruppi con errori o campi obbligatori
- Chiusura automatica degli altri gruppi quando se ne apre uno nuovo
- Se c'è solo un gruppo, rimane aperto di default
- Caricamento automatico JS solo all'interno di VirtueMart backend (vmadmin)
- Compatibilità con Joomla 3, 4 e 5

---------------------------------------------------------
INSTALLAZIONE
---------------------------------------------------------
1. Comprimi la cartella "plg_system_vmadmincustom" in formato ZIP:
   Nome file consigliato: plg_system_vmadmincustom_v3_5_final.zip

2. Installa da:
   Estensioni → Gestione → Installa → Carica file

3. Attiva il plugin da:
   Estensioni → Plugin → Sistema - VM Admin Custom

4. Opzionale: configura i parametri dal pannello del plugin:
   - Open all groups by default
   - Remember open/close state (session)
   - Persist state (localStorage)
   - Show "Open all / Close all" buttons
   - Auto-close other groups
   - Auto-open invalid/required groups

5. Apri un prodotto in VirtueMart → scheda "Campi personalizzati"
   → Premi Ctrl+F5 per aggiornare la cache del browser.

---------------------------------------------------------
TESTED ON
---------------------------------------------------------
✓ Joomla 3.10.12 + VirtueMart 4.0.22
✓ Joomla 4.4.5 + VirtueMart 4.2.x
✓ Joomla 5.1.3 + VirtueMart 4.4.x

---------------------------------------------------------
CHANGELOG
---------------------------------------------------------
v3.5 Final (2025-10-17)
- Aggiunto auto-close quando si aggiunge un nuovo gruppo
- Fix definitivo 404 JS (manifest <media>)
- Toolbar multilanguage (EN/IT)
- Ricorda stato (sessione / localStorage)
- Caricamento condizionato solo in VMAdmin
- Compatibilità completa Joomla 3–5

---------------------------------------------------------
SUPPORTO
---------------------------------------------------------
In caso di problemi:
- Pulisci cache del browser (Ctrl+F5)
- Verifica che il file JS sia presente in:
  /media/plg_system_vmadmincustom/vmadmincustom.js
- Se il plugin è attivo ma non vedi modifiche,
  disattivalo e riattivalo, poi aggiorna VirtueMart.

=========================================================