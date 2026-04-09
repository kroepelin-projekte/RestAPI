<?php
/**
 * ilRestAPIPlugin
 *
 * @author Keven Clausen support@kroepelin-projekte.de
 * &copy; Kröpelin Projekt GmbH
 * @version $Id$
 *
 */
class ilRestAPIPlugin extends ilEventHookPlugin
{
    /**
     * @param ilDBInterface              $db
     * @param ilComponentRepositoryWrite $component_repository
     * @param string                     $id
     */
    public function __construct(ilDBInterface $db, ilComponentRepositoryWrite $component_repository, string $id)
    {
        parent::__construct($db, $component_repository, $id);
    }

    /**
     * @param string $a_component
     * @param string $a_event
     * @param array  $a_parameter
     * @return void
     */
    public function handleEvent(string $a_component, string $a_event, array $a_parameter): void
    {
    }

    protected function afterUninstall(): void
    {
        (new KPG\RestAPI\ILIAS\Setup\DatabaseSetup())->uninstall();
    }

    public function getPluginInfo(): ilPluginInfo
    {
        return parent::getPluginInfo();
    }
}
