<?php
defined("_JEXEC") or die();

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;

class PlgSystemVmadmincustom extends CMSPlugin
{
    protected $app;

    /**
     * Joomla 4/5: carica asset con WebAssetManager PRIMA che la head sia “locked”
     */
    public function onBeforeCompileHead()
    {
        $app = Factory::getApplication();
        $option = $app->input->getCmd("option", "");

        // Solo backend VirtueMart
        if (
            !($app->isClient("administrator") && $option === "com_virtuemart")
        ) {
            return;
        }

        // Carica le lingue del plugin (J3/4/5)
        $this->loadLanguage();
        $lang = Factory::getLanguage();
        $lang->load(
            "plg_system_vmadmincustom",
            JPATH_ADMINISTRATOR,
            null,
            true
        );
        $lang->load("plg_system_vmadmincustom", JPATH_SITE, null, true);

        // Esporta in JS le chiavi
        if (class_exists("\Joomla\CMS\Language\Text")) {
            Text::script("PLG_SYSTEM_VMADMINCUSTOM_OPEN_ALL");
            Text::script("PLG_SYSTEM_VMADMINCUSTOM_CLOSE_ALL");
        } elseif (class_exists("JText")) {
            \JText::script("PLG_SYSTEM_VMADMINCUSTOM_OPEN_ALL");
            \JText::script("PLG_SYSTEM_VMADMINCUSTOM_CLOSE_ALL");
        }

        // Config da esporre a JS
        $CFG = [
            "openDefault" => (bool) $this->params->get("open_default", 0),
            "rememberState" => (bool) $this->params->get("remember_state", 1),
            "persistState" => (bool) $this->params->get("persist_state", 0),
            "showGlobalButtons" => (bool) $this->params->get(
                "show_global_buttons",
                1
            ),
            "autoCloseGroups" => (bool) $this->params->get(
                "auto_close_groups",
                1
            ),
            "autoOpenInvalid" => (bool) $this->params->get(
                "auto_open_invalid",
                1
            ),
        ];

        $doc = $app->getDocument();

        // Percorsi ASSOLUTI (includono dominio): evitano //media/...
        $base = rtrim(Uri::root(), "/"); // es: https://dominio.tld
        $scriptUrl = $base . "/media/plg_system_vmadmincustom/vmadmincustom.js";
        $styleUrl = $base . "/media/plg_system_vmadmincustom/vmadmincustom.css";

        // Joomla 4/5: usa WAM, altrimenti fallback
        $version = new Version();
        if (version_compare($version->getShortVersion(), "4.0", ">=")) {
            try {
                $wa = $doc->getWebAssetManager();

                if (!$wa->assetExists("script", "plg_vmadmincustom")) {
                    $wa->registerScript(
                        "plg_vmadmincustom",
                        $scriptUrl,
                        [],
                        ["defer" => true, "version" => "auto"]
                    );
                }
                if (!$wa->assetExists("style", "plg_vmadmincustom")) {
                    $wa->registerStyle(
                        "plg_vmadmincustom",
                        $styleUrl,
                        [],
                        ["version" => "auto"]
                    );
                }

                // Inietta config PRIMA del file JS
                $wa->addInlineScript(
                    "window.VMACFG = " . json_encode($CFG) . ";"
                );

                $wa->useStyle("plg_vmadmincustom");
                $wa->useScript("plg_vmadmincustom");
            } catch (\Throwable $e) {
                // Fallback in caso WAM sia locked
                $doc->addStyleSheet($styleUrl);
                $doc->addScriptDeclaration(
                    "window.VMACFG = " . json_encode($CFG) . ";"
                );
                $doc->addScript($scriptUrl, [
                    "defer" => true,
                    "version" => "auto",
                ]);
            }
        } else {
            // Su J3 questo metodo non verrà invocato (vedi onAfterDispatch)
            // ma se dovesse capitare, mettiamo anche qui un fallback
            $doc->addStyleSheet($styleUrl);
            $doc->addScriptDeclaration(
                "window.VMACFG = " . json_encode($CFG) . ";"
            );
            $doc->addScript($scriptUrl, ["defer" => true]);
        }
    }

    /**
     * Joomla 3: carica asset “alla vecchia”
     */
    public function onAfterDispatch()
    {
        $version = new Version();
        if (version_compare($version->getShortVersion(), "4.0", ">=")) {
            return; // J4/5 già gestiti sopra
        }

        $app = Factory::getApplication();
        $option = $app->input->getCmd("option", "");
        if (
            !($app->isClient("administrator") && $option === "com_virtuemart")
        ) {
            return;
        }

        // Lingue & Text::script
        $this->loadLanguage();
        if (class_exists("JText")) {
            \JText::script("PLG_SYSTEM_VMADMINCUSTOM_OPEN_ALL");
            \JText::script("PLG_SYSTEM_VMADMINCUSTOM_CLOSE_ALL");
        }

        $CFG = [
            "openDefault" => (bool) $this->params->get("open_default", 0),
            "rememberState" => (bool) $this->params->get("remember_state", 1),
            "persistState" => (bool) $this->params->get("persist_state", 0),
            "showGlobalButtons" => (bool) $this->params->get(
                "show_global_buttons",
                1
            ),
            "autoCloseGroups" => (bool) $this->params->get(
                "auto_close_groups",
                1
            ),
            "autoOpenInvalid" => (bool) $this->params->get(
                "auto_open_invalid",
                1
            ),
        ];

        $doc = $app->getDocument();
        $base = rtrim(Uri::root(), "/");
        $doc->addStyleSheet(
            $base . "/media/plg_system_vmadmincustom/vmadmincustom.css"
        );
        $doc->addScriptDeclaration(
            "window.VMACFG = " . json_encode($CFG) . ";"
        );
        $doc->addScript(
            $base . "/media/plg_system_vmadmincustom/vmadmincustom.js"
        );
    }
}
