<?php

namespace Amilio\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function storeAction(Request $request) 
    {
        var_dump( $request->get('url'));
        return $this->render('AmilioCoreBundle:Default:index.html.twig', array('name' => ''));
    }
}