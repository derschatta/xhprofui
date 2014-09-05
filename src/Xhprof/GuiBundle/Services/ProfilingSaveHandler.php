<?php
namespace Xhprof\GuiBundle\Services;

use Doctrine\ORM\EntityManager;
use Xhprof\GuiBundle\Entity\Profiling;

class ProfilingSaveHandler
{

    /**
     * @var EntityManager
     */
    private $entity_manager;

    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * register the profiling save handler
     *
     * @return void
     */
    public function register()
    {
        xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
        register_shutdown_function(array($this, 'shutdownFunction'));
    }

    /**
     * sets the doctrine service
     *
     * @param EntityManager $entity_manager
     */
    public function setDoctrineEntityManager($entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    /**
     * @return void
     */
    public function shutdownFunction()
    {
        $xhprof_data = xhprof_disable();
        $this->save($xhprof_data);
    }

    /**
     * @param array $xhprof_data
     *
     * @return void
     */
    private function save(array $xhprof_data)
    {
        $main = $xhprof_data['main()'];

        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-';
        $request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '-';
        $server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '-';

        $profiling = new Profiling();
        $profiling->setData($xhprof_data);
        $profiling->setCpu($main['cpu']);
        $profiling->setMemory($main['mu']);
        $profiling->setPeakMemory($main['pmu']);
        $profiling->setTimestamp(new \DateTime());
        $profiling->setWallTime($main['wt']);
        $profiling->setRequestUri($request_uri);
        $profiling->setRequestMethod($request_method);
        $profiling->setGetParams($_GET);
        $profiling->setPostParams($_POST);
        $profiling->setCookiesParams($_COOKIE);
        $profiling->setServerName($server_name);

        $this->entity_manager->persist($profiling);
        $this->entity_manager->flush();
    }
}
