<?php
namespace Jeka\PagesBundle\Controller;

use \Application\Vespolina\ProductBundle\Document\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PagesController extends Controller{

    /**
     * @Route("/contacts", name="pages_contacts")
     * @Template
     */
    function contactsAction()
    {
        $session = $this->getRequest()->getSession();

        $data = $session->get('customer_info',array());
        $form = $this->createFormBuilder($data)
            ->add('name','text',array('label'=>'Ваше имя'))
            ->add('email','email',array('required'=>false,'label'=>'Ваш Email'))
            ->add('message','textarea',array('required'=>false,'label'=>'Сообщение'))
            ->getForm();
        if ($this->getRequest()->getMethod()=='POST')
        {
            $form->bindRequest($this->getRequest());
            if ($form->isValid())
            {
                $data = array_merge($data,$form->getData());

                $session->set('customer_info',$data);
                $session->setFlash('success','Ваш запрос отправлен');
                return $this->redirect($this->generateUrl('pages_contacts'));
            }
        }

        return array(
            'form'=>$form->createView()
        );
    }

    /**
     * @Route("/delivery", name="pages_delivery")
     * @Template
     */
    function deliveryAction()
    {

    }

}