<?php

namespace Xhprof\GuiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Xhprof\GuiBundle\Entity\ProfilingRepository;
use Xhprof\GuiBundle\Model\DataParser;

class ProfilingController extends Controller
{
    /**
     * Show a single profiling in detail
     *
     * @param integer $id the profiling id
     * @param string $sort_by
     * @param string $sort_direction
     *
     * @throws \InvalidArgumentException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id, $sort_by, $sort_direction)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('Invalid id given, numeric value expected!');
        }
        $doctrine = $this->get('doctrine');
        $profiling = $doctrine->getRepository('XhprofGuiBundle:Profiling')
            ->find($id);
        $data = null;
        if ($profiling) {
            $data = $profiling->getData();
            $parser = new DataParser();
            $parsed_data = $parser->parse($data, $sort_by, $sort_direction);
        }

        return $this->render(
            'XhprofGuiBundle:Profiling:show.html.twig',
            array(
                'profiling'      => $profiling,
                'data'           => $parsed_data,
                'sort_by'        => $sort_by,
                'sort_direction' => $sort_direction
            )
        );
    }

    /**
     * Show a single profiling in detail
     *
     * @param integer $id the profiling id
     * @param string  $function_id
     * @param string  $sort_by
     * @param string  $sort_direction
     *
     * @throws \InvalidArgumentException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partialAction($id, $function_id, $sort_by, $sort_direction)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('Invalid id given, numeric value expected!');
        }
        $doctrine = $this->get('doctrine');
        $profiling = $doctrine->getRepository('XhprofGuiBundle:Profiling')
            ->find($id);
        $data = null;
        $parsed_data = [];
        $function_name = '';
        if ($profiling) {
            $data = $profiling->getData();
            $parser = new DataParser();
            $parsed_data = $parser->parsePartial($data, $function_id, $sort_by, $sort_direction);
            $function_name = DataParser::findNameById($function_id, $parsed_data['current']);
        }

        return $this->render(
            'XhprofGuiBundle:Profiling:partial.html.twig',
            array(
                'function_name'  => $function_name,
                'profiling'      => $profiling,
                'data'           => $parsed_data,
                'sort_by'        => $sort_by,
                'sort_direction' => $sort_direction
            )
        );
    }

    /**
     * list all profilings
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $doctrine = $this->get('doctrine');
        /** @var ProfilingRepository $repository */
        $repository = $doctrine->getRepository('XhprofGuiBundle:Profiling');
        $profilings = $repository->findBy(array(), array('timestamp' => 'DESC'));

        $session = $request->getSession();
        $last_accessed = $session->get('last_accessed', null);
        $output = $this->render('XhprofGuiBundle:Profiling:list.html.twig', [
            'profilings' => $profilings,
            'last_accessed' => $last_accessed
        ]);

        $session->set('last_accessed', date('Y-m-d H:i:s'));
        return $output;
    }

    /**
     * just a test action to create a new profiling
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction()
    {
        return $this->render('XhprofGuiBundle:Profiling:test.html.twig', array());
    }
}
